<?php
/**
 * HS Properties — Secure File Upload Handler.
 *
 * Handles file validation (MIME type, size, extension), generates secure
 * file names, moves uploads to organised directories, and provides
 * checksum calculation for document verification.
 */

declare(strict_types=1);

class FileUpload
{
    private string $uploadDir;
    private int    $maxSize;

    /** Allowed MIME types mapped to extensions. */
    private const ALLOWED_TYPES = [
        'application/pdf'       => 'pdf',
        'image/jpeg'            => 'jpg',
        'image/png'             => 'png',
        'image/tiff'            => 'tiff',
        'image/webp'            => 'webp',
        'application/acad'      => 'dwg',
        'image/vnd.dwg'         => 'dwg',
        'image/x-dwg'           => 'dwg',
        'application/dxf'       => 'dxf',
        'image/vnd.dxf'         => 'dxf',
    ];

    /** Allowed extensions (additional safeguard). */
    private const ALLOWED_EXTENSIONS = [
        'pdf', 'jpg', 'jpeg', 'png', 'tiff', 'tif', 'webp', 'dwg', 'dxf',
    ];

    public function __construct(?string $uploadDir = null, ?int $maxSize = null)
    {
        $this->uploadDir = $uploadDir ?? (APP_ROOT . '/uploads');
        $this->maxSize   = $maxSize   ?? (int) (env('UPLOAD_MAX_SIZE', '20971520'));
    }

    /**
     * Upload a single file from the $_FILES superglobal.
     *
     * @param array  $file      A single $_FILES entry.
     * @param string $subDir    Sub-directory (e.g. 'documents', 'images').
     * @param string $category  Category label for the file.
     *
     * @return array File metadata (original_name, stored_name, file_path, mime_type, file_size, checksum).
     * @throws RuntimeException On validation failure.
     */
    public function upload(array $file, string $subDir = 'documents', string $category = 'other'): array
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException($this->uploadErrorMessage($file['error']));
        }

        // Validate file size
        if ($file['size'] > $this->maxSize) {
            $maxMB = round($this->maxSize / (1024 * 1024), 1);
            throw new RuntimeException("File exceeds the maximum size of {$maxMB} MB.");
        }

        // Validate extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            throw new RuntimeException("File type '.{$ext}' is not allowed.");
        }

        // Validate MIME type
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        // Allow common text/plain false positives for DWG/DXF
        $validMime = isset(self::ALLOWED_TYPES[$mimeType])
                   || in_array($ext, ['dwg', 'dxf'], true);

        if (!$validMime) {
            throw new RuntimeException("MIME type '{$mimeType}' is not allowed.");
        }

        // Build destination directory
        $datePath = date('Y/m');
        $destDir  = "{$this->uploadDir}/{$subDir}/{$datePath}";

        if (!is_dir($destDir)) {
            if (!mkdir($destDir, 0755, true)) {
                throw new RuntimeException('Failed to create upload directory.');
            }
        }

        // Generate secure filename
        $storedName = $this->generateSecureName($ext);
        $destPath   = "{$destDir}/{$storedName}";

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        // Calculate checksum
        $checksum = hash_file('sha256', $destPath);

        // Return relative path from project root
        $relativePath = str_replace(APP_ROOT . '/', '', $destPath);
        $relativePath = str_replace('\\', '/', $relativePath);

        return [
            'original_name' => $file['name'],
            'stored_name'   => $storedName,
            'file_path'     => $relativePath,
            'mime_type'     => $mimeType,
            'file_size'     => (int) $file['size'],
            'checksum'      => $checksum,
            'category'      => $category,
        ];
    }

    /**
     * Upload multiple files.
     *
     * @param array  $files   The $_FILES array for a multi-file input.
     * @param string $subDir  Sub-directory.
     *
     * @return array Array of file metadata arrays.
     */
    public function uploadMultiple(array $files, string $subDir = 'documents'): array
    {
        $results = [];
        $errors  = [];

        // Normalise the PHP multi-file array structure
        $normalised = $this->normaliseFiles($files);

        foreach ($normalised as $file) {
            try {
                $results[] = $this->upload($file, $subDir);
            } catch (RuntimeException $e) {
                $errors[] = $file['name'] . ': ' . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            throw new RuntimeException(implode("\n", $errors));
        }

        return $results;
    }

    /**
     * Delete a file from the uploads directory.
     */
    public function delete(string $filePath): bool
    {
        $fullPath = APP_ROOT . '/' . ltrim($filePath, '/');
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    /**
     * Format a file size for display.
     */
    public static function formatSize(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 KB';
        }
        $mb = $bytes / (1024 * 1024);
        return $mb >= 1
            ? number_format($mb, 1) . ' MB'
            : max(1, (int) round($bytes / 1024)) . ' KB';
    }

    /**
     * Generate a secure, unique filename.
     */
    private function generateSecureName(string $ext): string
    {
        return sprintf(
            '%s_%s.%s',
            date('Ymd_His'),
            bin2hex(random_bytes(8)),
            $ext
        );
    }

    /**
     * Normalise PHP's confusing multi-file upload array structure.
     *
     * Converts $_FILES['field']['name'][0..n] into an array of single file arrays.
     */
    private function normaliseFiles(array $files): array
    {
        $normalised = [];

        if (isset($files['name']) && is_array($files['name'])) {
            for ($i = 0, $n = count($files['name']); $i < $n; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                $normalised[] = [
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ];
            }
        } elseif (isset($files['name'])) {
            if ($files['error'] !== UPLOAD_ERR_NO_FILE) {
                $normalised[] = $files;
            }
        }

        return $normalised;
    }

    /**
     * Human-readable upload error message.
     */
    private function uploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE   => 'File exceeds the server upload size limit.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds the form upload size limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the upload.',
            default               => 'Unknown upload error.',
        };
    }
}
