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
     * Process Step 1 submission: Contact Details.
     */
    public function handleStep1(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(route('seller.step1'));
        }

        // Validate CSRF token
        if (isset($_POST['_csrf'])) {
            Session::validateCsrf($_POST['_csrf']);
        } elseif (isset($_POST['_csrf_token'])) {
            Session::validateCsrf($_POST['_csrf_token']);
        }

        $validator = new Validator($_POST);
        $validator->required('full_name', 'Please enter your full name.');
        $validator->required('phone', 'Please enter your phone number.');
        $validator->phone('phone', 'Please enter a valid phone number.');
        $validator->required('email', 'Please enter your email address.');
        $validator->email('email', 'Please enter a valid email address.');

        if ($validator->fails()) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', $_POST);
            redirect(route('seller.step1'));
        }

        // Persist to session
        $draft = Session::get('seller_intake', []);
        $step1Data = [
            'full_name'       => trim($_POST['full_name'] ?? ''),
            'contact_name'    => trim($_POST['full_name'] ?? ''),
            'phone'           => trim($_POST['phone'] ?? ''),
            'contact_phone'   => trim($_POST['phone'] ?? ''),
            'email'           => trim($_POST['email'] ?? ''),
            'contact_email'   => trim($_POST['email'] ?? ''),
            'address'         => trim($_POST['address'] ?? ''),
            'contact_address' => trim($_POST['address'] ?? ''),
        ];
        $draft['step1'] = $step1Data;
        Session::set('seller_intake', $draft);

        // Also save/update draft in DB if connected
        try {
            $dbDraft = $this->sellerModel->getOrCreateDraft([
                'contact_name'    => $step1Data['contact_name'],
                'contact_phone'   => $step1Data['contact_phone'],
                'contact_email'   => $step1Data['contact_email'],
                'contact_address' => $step1Data['contact_address'],
            ]);
            $this->sellerModel->saveStep1((int)$dbDraft['id'], $step1Data);
            Session::set('seller_request_id', (int)$dbDraft['id']);
        } catch (\Throwable $e) {
            // Non-blocking: session persistence ensures uninterrupted user experience
        }

        if (($_POST['action'] ?? '') === 'save') {
            Session::flash('notice', 'Your contact details have been safely saved.');
            redirect(route('seller.step1'));
        }

        redirect(route('seller.step2'));
    }

    /**
     * Process Step 2 submission: Details & Documents.
     */
    public function handleStep2(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(route('seller.step2'));
        }

        if (isset($_POST['_csrf'])) {
            Session::validateCsrf($_POST['_csrf']);
        } elseif (isset($_POST['_csrf_token'])) {
            Session::validateCsrf($_POST['_csrf_token']);
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
            'locality'          => trim($_POST['locality'] ?? ''),
            'city'              => trim($_POST['city'] ?? ''),
            'pincode'           => trim($_POST['pincode'] ?? ''),
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
            try {
                $this->sellerModel->saveStep2((int)$requestId, $step2Data);

                // Handle file uploads if any
                $filesInput = !empty($_FILES['document_files']['name'][0]) ? $_FILES['document_files'] : (!empty($_FILES['files']['name'][0]) ? $_FILES['files'] : null);
                if ($filesInput) {
                    $uploader = new FileUpload(APP_ROOT . '/uploads/documents');
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
                }
            } catch (\Throwable $e) {
                Session::flash('error', 'Notice: ' . $e->getMessage());
            }
        }

        if (($_POST['action'] ?? '') === 'save') {
            Session::flash('notice', 'Your progress has been saved.');
            redirect(route('seller.step2'));
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

        $draft = Session::get('seller_intake', []);
        $step1 = $draft['step1'] ?? [];
        $step2 = $draft['step2'] ?? [];

        $requestId = Session::get('seller_request_id');
        $reference = 'HS-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));

        if ($requestId) {
            try {
                if (!empty($step1)) {
                    $this->sellerModel->saveStep1((int)$requestId, $step1);
                }
                if (!empty($step2)) {
                    $this->sellerModel->saveStep2((int)$requestId, $step2);
                }
                $this->sellerModel->submit((int)$requestId);
                $record = $this->sellerModel->find((int)$requestId);
                if ($record && !empty($record['reference'])) {
                    $reference = $record['reference'];
                }
                log_activity('seller_mandate_submitted', 'seller_requests', (int)$requestId, "Mandate {$reference} submitted for advisory evaluation");
            } catch (\Throwable $e) {}
        } else {
            // Create record if not already created
            try {
                $createdId = $this->sellerModel->create([
                    'contact_name'    => $step1['contact_name'] ?? $step1['full_name'] ?? '',
                    'contact_phone'   => $step1['contact_phone'] ?? $step1['phone'] ?? '',
                    'contact_email'   => $step1['contact_email'] ?? $step1['email'] ?? '',
                    'contact_address' => $step1['contact_address'] ?? $step1['address'] ?? '',
                    'address'         => $step2['address'] ?? '',
                    'locality'        => $step2['locality'] ?? '',
                    'city'            => $step2['city'] ?? '',
                    'pincode'         => $step2['pincode'] ?? '',
                ]);
                if (!empty($step2)) {
                    $this->sellerModel->saveStep2($createdId, $step2);
                }
                $this->sellerModel->submit($createdId);
                $record = $this->sellerModel->find($createdId);
                if ($record && !empty($record['reference'])) {
                    $reference = $record['reference'];
                }
            } catch (\Throwable $e) {}
        }

        // Set last submitted ref before clearing draft
        Session::set('last_submitted_ref', $reference);
        Session::remove('seller_intake');
        Session::remove('seller_request_id');

        redirect(route('seller.step3') . '?state=submitted&ref=' . urlencode($reference));
    }
}
