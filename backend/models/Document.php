<?php
/**
 * HS Properties — Document Model.
 *
 * Manages uploaded documents associated with seller requests.
 */

declare(strict_types=1);

class Document
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create a document record.
     */
    public function create(array $data): int
    {
        return $this->db->insert('documents', $data);
    }

    /**
     * Find by ID.
     */
    public function find(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM `documents` WHERE `id` = :id',
            ['id' => $id]
        );
    }

    /**
     * Get all documents for a seller request.
     */
    public function getByRequest(int $requestId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM `documents` WHERE `seller_request_id` = :rid ORDER BY `uploaded_at` DESC',
            ['rid' => $requestId]
        );
    }

    /**
     * Delete a document record and file.
     */
    public function delete(int $id): bool
    {
        $doc = $this->find($id);
        if (!$doc) {
            return false;
        }

        // Delete the physical file
        $uploader = new FileUpload();
        $uploader->delete($doc['file_path']);

        // Delete the record
        $this->db->delete('documents', '`id` = :id', ['id' => $id]);
        return true;
    }

    /**
     * Update document status.
     */
    public function updateStatus(int $id, string $status): void
    {
        $this->db->update('documents', ['status' => $status], '`id` = :id', ['id' => $id]);
    }

    /**
     * Format documents for the review step display.
     */
    public function formatForReview(array $documents): array
    {
        $formatted = [];
        foreach ($documents as $doc) {
            $ext = strtoupper(pathinfo($doc['original_name'], PATHINFO_EXTENSION));
            $formatted[] = [
                'name'     => $doc['original_name'],
                'size'     => FileUpload::formatSize((int) $doc['file_size']),
                'state'    => match ($doc['status']) {
                    'verified' => 'Verified Checksum',
                    'rejected' => 'Rejected',
                    default    => 'Staged for Audit',
                },
                'category' => ucfirst(str_replace('_', ' ', $doc['category'])),
                'kind'     => $ext ?: 'FILE',
            ];
        }
        return $formatted;
    }
}
