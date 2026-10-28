<?php
/**
 * HS Properties — Seller Intake Controller.
 *
 * Handles processing and session persistence for the 3-step seller intake wizard.
 */

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../models/SellerRequest.php';
require_once __DIR__ . '/../models/Document.php';

class SellerIntakeController
{
    private SellerRequest $sellerModel;
    private Document $documentModel;

    public function __construct()
    {
        $this->sellerModel   = new SellerRequest();
        $this->documentModel = new Document();
    }

    /**
     * Process Step 1 submission.
     */
    public function handleStep1(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(route('seller.step1'));
        }

        // Validate CSRF if present
        if (isset($_POST['_csrf'])) {
            Session::validateCsrf($_POST['_csrf']);
        }

        $validator = new Validator($_POST);
        $validator->required('property_type', 'Please select a property type.');
        $validator->required('property_status', 'Please select the property status.');
        $validator->required('city', 'City is required.');
        $validator->required('locality', 'Locality is required.');
        $validator->pincode('pincode', 'Please enter a valid 6-digit PIN code.');
        $validator->required('extent', 'Total area / extent is required.');
        $validator->required('unit', 'Measurement unit is required.');
        $validator->required('ownership', 'Ownership structure is required.');

        if ($validator->fails()) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', $_POST);
            redirect(route('seller.step1'));
        }

        // Persist to session
        $draft = Session::get('seller_intake', []);
        $draft['step1'] = [
            'property_type'    => $_POST['property_type'] ?? '',
            'property_status'  => $_POST['property_status'] ?? '',
            'city'             => trim($_POST['city'] ?? ''),
            'locality'         => trim($_POST['locality'] ?? ''),
            'pincode'          => trim($_POST['pincode'] ?? ''),
            'extent'           => trim($_POST['extent'] ?? ''),
            'unit'             => $_POST['unit'] ?? 'sqft',
            'bedrooms'         => $_POST['bedrooms'] ?? '',
            'ownership'        => $_POST['ownership'] ?? '',
        ];
        Session::set('seller_intake', $draft);

        // Also save/update draft in DB
        $dbDraft = $this->sellerModel->getOrCreateDraft([
            'city'     => $draft['step1']['city'],
            'locality' => $draft['step1']['locality'],
        ]);
        $this->sellerModel->saveStep1((int)$dbDraft['id'], $draft['step1']);
        Session::set('seller_request_id', (int)$dbDraft['id']);

        redirect(route('seller.step2'));
    }

    /**
     * Process Step 2 submission.
     */
    public function handleStep2(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(route('seller.step2'));
        }

        if (isset($_POST['_csrf'])) {
            Session::validateCsrf($_POST['_csrf']);
        }

        $validator = new Validator($_POST);
        $validator->required('address', 'Property address is required.');
        $validator->required('condition', 'Physical condition is required.');
        $validator->required('timeline', 'Expected timeline is required.');
        $validator->required('mandate', 'Mandate structure is required.');

        if ($validator->fails()) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', $_POST);
            redirect(route('seller.step2'));
        }

        $draft = Session::get('seller_intake', []);
        $step2Data = [
            'address'           => trim($_POST['address'] ?? ''),
            'condition'         => $_POST['condition'] ?? '',
            'year_built'        => !empty($_POST['year_built']) ? (int)$_POST['year_built'] : null,
            'furnishing'        => $_POST['furnishing'] ?? '',
            'target_price'      => !empty($_POST['target_price']) ? (float)$_POST['target_price'] : null,
            'timeline'          => $_POST['timeline'] ?? '',
            'mandate'           => $_POST['mandate'] ?? '',
            'documents'         => $_POST['documents'] ?? [],
        ];
        $draft['step2'] = $step2Data;
        Session::set('seller_intake', $draft);

        $requestId = Session::get('seller_request_id');
        if ($requestId) {
            $this->sellerModel->saveStep2((int)$requestId, $step2Data);

            // Handle file uploads if any
            $filesInput = !empty($_FILES['document_files']['name'][0]) ? $_FILES['document_files'] : (!empty($_FILES['files']['name'][0]) ? $_FILES['files'] : null);
            if ($filesInput) {
                $uploader = new FileUpload(APP_ROOT . '/uploads/documents');
                try {
                    $uploaded = $uploader->uploadMultiple($filesInput);
                    foreach ($uploaded as $file) {
                        $this->documentModel->create([
                            'seller_request_id' => $requestId,
                            'category'          => $file['category'] ?? 'other',
                            'original_name'     => $file['original_name'],
                            'stored_name'       => $file['stored_name'],
                            'file_path'         => $file['file_path'],
                            'file_size'         => $file['file_size'],
                            'mime_type'         => $file['mime_type'],
                            'checksum'          => $file['checksum'] ?? null,
                            'status'            => 'staged',
                        ]);
                    }
                } catch (\Throwable $e) {
                    Session::flash('error', 'File upload notice: ' . $e->getMessage());
                }
            }
        }

        redirect(route('seller.step3'));
    }

    /**
     * Process Step 3 final submission.
     */
    public function handleStep3(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(route('seller.step3'));
        }

        if (isset($_POST['_csrf'])) {
            Session::validateCsrf($_POST['_csrf']);
        } elseif (isset($_POST['_csrf_token'])) {
            Session::validateCsrf($_POST['_csrf_token']);
        }

        $requestId = Session::get('seller_request_id');
        $reference = 'HS-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));

        if ($requestId) {
            $this->sellerModel->submit((int)$requestId);
            $record = $this->sellerModel->find((int)$requestId);
            if ($record && !empty($record['reference'])) {
                $reference = $record['reference'];
            }
            log_activity('seller_mandate_submitted', 'seller_requests', (int)$requestId, "Mandate {$reference} submitted for advisory evaluation");
        }

        // Set last submitted ref before clearing draft
        Session::set('last_submitted_ref', $reference);
        Session::remove('seller_intake');
        Session::remove('seller_request_id');

        redirect(route('seller.step3') . '?state=submitted&ref=' . urlencode($reference));
    }
}
