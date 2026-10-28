<?php
/**
 * HS Properties — Contact Controller.
 *
 * Handles general contact form inquiries.
 */

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

class ContactController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Process contact message.
     */
    public function handle(): void
    {
        $isJson = isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json');
        $isAjax = $isJson || is_ajax();
        $input  = $isJson ? json_decode(file_get_contents('php://input'), true) ?? [] : $_POST;

        $validator = new Validator($input);
        $validator->required('full_name', 'Please provide your full name.');
        $validator->email('email', 'A valid email address is required.');
        $validator->required('message', 'Please enter your message or advisory requirement.');

        if ($validator->fails()) {
            if ($isAjax) {
                json_response(['success' => false, 'errors' => $validator->errors()], 422);
            }
            Session::flash('errors', $validator->errors());
            Session::flash('old', $input);
            redirect(url('index.php#contact'));
        }

        $id = $this->db->insert('contact_messages', [
            'full_name'  => trim($input['full_name']),
            'email'      => trim($input['email']),
            'phone'      => trim($input['phone'] ?? ''),
            'subject'    => trim($input['subject'] ?? 'General Advisory Inquiry'),
            'message'    => trim($input['message']),
            'status'     => 'unread',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        log_activity('contact_received', 'contact_messages', $id, "Message from {$input['full_name']}");

        if ($isAjax) {
            json_response([
                'success' => true,
                'message' => 'Thank you for reaching out. A Senior Advisor will respond discreetly within 24 hours.',
            ]);
        }

        Session::flash('success', 'Thank you for reaching out. A Senior Advisor will respond discreetly within 24 hours.');
        redirect(url('index.php#contact'));
    }
}
