<?php
/**
 * HS Properties — Buyer Request Controller.
 *
 * Handles buyer inquiry submissions from the website.
 */

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../models/BuyerRequest.php';

class BuyerRequestController
{
    private BuyerRequest $model;

    public function __construct()
    {
        $this->model = new BuyerRequest();
    }

    /**
     * Handle buyer inquiry submission.
     */
    public function handle(): void
    {
        $isJson = isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json');
        $isAjax = $isJson || is_ajax();
        $input  = $isJson ? json_decode(file_get_contents('php://input'), true) ?? [] : $_POST;

        $validator = new Validator($input);
        $validator->required('full_name', 'Full name is required.');
        $validator->email('email', 'A valid email address is required.');

        if ($validator->fails()) {
            if ($isAjax) {
                json_response(['success' => false, 'errors' => $validator->errors()], 422);
            }
            Session::flash('errors', $validator->errors());
            Session::flash('old', $input);
            redirect(url('index.php#contact'));
        }

        $reference = generate_reference($input['city'] ?? 'BYR');

        $data = [
            'reference'            => $reference,
            'full_name'            => trim($input['full_name']),
            'email'                => trim($input['email']),
            'phone'                => trim($input['phone'] ?? ''),
            'property_type'        => $input['property_type'] ?? null,
            'city'                 => trim($input['city'] ?? ''),
            'locality'             => trim($input['locality'] ?? ''),
            'budget_min'           => $input['budget_min'] ?? null,
            'budget_max'           => $input['budget_max'] ?? null,
            'bedrooms'             => $input['bedrooms'] ?? null,
            'timeline'             => $input['timeline'] ?? null,
            'special_requirements' => trim($input['special_requirements'] ?? $input['message'] ?? ''),
            'status'               => 'new',
            'created_at'           => date('Y-m-d H:i:s'),
        ];

        $id = $this->model->create($data);

        log_activity('buyer_request_created', 'buyer_requests', $id, "New buyer inquiry from {$data['full_name']}");

        if ($isAjax) {
            json_response([
                'success'   => true,
                'message'   => 'Thank you. Your mandate request has been logged confidentially.',
                'reference' => $reference,
            ]);
        }

        Session::flash('success', "Your inquiry has been registered. Reference: {$reference}");
        redirect(url('index.php#contact'));
    }
}
