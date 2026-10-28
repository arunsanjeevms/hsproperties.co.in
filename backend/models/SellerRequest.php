<?php
/**
 * HS Properties — Seller Request Model.
 *
 * Manages the multi-step seller intake submissions, from draft through
 * submission to review. Maps directly to the `seller_requests` table.
 */

declare(strict_types=1);

class SellerRequest
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new draft seller request.
     */
    public function create(array $data): int
    {
        $data['reference'] = $data['reference'] ?? generate_reference($data['city'] ?? '');
        $data['status']    = 'draft';
        $data['session_id'] = session_id();

        return $this->db->insert('seller_requests', $data);
    }

    /**
     * Find the current draft for this session, if any.
     */
    public function findDraftBySession(): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM `seller_requests`
             WHERE `session_id` = :sid AND `status` = 'draft'
             ORDER BY `created_at` DESC LIMIT 1",
            ['sid' => session_id()]
        );
    }

    /**
     * Get or create a draft for the current session.
     */
    public function getOrCreateDraft(array $initialData = []): array
    {
        $draft = $this->findDraftBySession();

        if ($draft) {
            return $draft;
        }

        $id = $this->create($initialData);
        return $this->find($id);
    }

    /**
     * Find a seller request by ID.
     */
    public function find(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM `seller_requests` WHERE `id` = :id',
            ['id' => $id]
        );
    }

    /**
     * Find by reference number.
     */
    public function findByReference(string $reference): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM `seller_requests` WHERE `reference` = :ref',
            ['ref' => $reference]
        );
    }

    /**
     * Update step 1 data: Contact Details.
     */
    public function saveStep1(int $id, array $data): void
    {
        $update = [
            'contact_name'  => $data['contact_name'] ?? $data['full_name'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? $data['phone'] ?? null,
            'contact_email' => $data['contact_email'] ?? $data['email'] ?? null,
            'current_step'  => max(2, $this->getCurrentStep($id)),
        ];

        $addr = $data['contact_address'] ?? $data['address'] ?? null;
        if ($addr !== null) {
            $update['contact_address'] = $addr;
        }

        $this->db->update('seller_requests', $update, '`id` = :id', ['id' => $id]);
    }

    /**
     * Update step 2 data: Property Details & Positioning.
     */
    public function saveStep2(int $id, array $data): void
    {
        $this->db->update('seller_requests', [
            'address'             => $data['address'] ?? null,
            'locality'            => $data['locality'] ?? null,
            'city'                => $data['city'] ?? null,
            'pincode'             => $data['pincode'] ?? null,
            'condition_status'    => $data['condition'] ?? null,
            'year_built'          => $data['year_built'] ?? null,
            'furnishing'          => $data['furnishing'] ?? null,
            'target_price'        => $data['target_price'] ?? null,
            'timeline'            => $data['timeline'] ?? null,
            'mandate_structure'   => $data['mandate'] ?? null,
            'documents_available' => !empty($data['documents']) ? json_encode($data['documents']) : null,
            'current_step'        => max(3, $this->getCurrentStep($id)),
        ], '`id` = :id', ['id' => $id]);
    }

    /**
     * Submit the request (final step).
     */
    public function submit(int $id): void
    {
        $this->db->update('seller_requests', [
            'status'       => 'submitted',
            'declaration'  => 1,
            'submitted_at' => date('Y-m-d H:i:s'),
        ], '`id` = :id', ['id' => $id]);
    }

    /**
     * Get the current step of a request.
     */
    private function getCurrentStep(int $id): int
    {
        return (int) ($this->db->fetchColumn(
            'SELECT `current_step` FROM `seller_requests` WHERE `id` = :id',
            ['id' => $id]
        ) ?? 1);
    }

    /**
     * Get all seller requests with optional filtering and pagination.
     */
    public function getAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]           = '`status` = :status';
            $params['status']  = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[]          = '(`reference` LIKE :search OR `city` LIKE :search OR `locality` LIKE :search OR `contact_name` LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $whereClause = implode(' AND ', $where);
        $offset      = ($page - 1) * $perPage;

        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM `seller_requests` WHERE $whereClause",
            $params
        );

        $rows = $this->db->fetchAll(
            "SELECT * FROM `seller_requests` WHERE $whereClause ORDER BY `created_at` DESC LIMIT $perPage OFFSET $offset",
            $params
        );

        return [
            'data'        => $rows,
            'total'       => (int) $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Update the status of a request.
     */
    public function updateStatus(int $id, string $status, ?int $assignedTo = null, ?string $notes = null): void
    {
        $data = ['status' => $status];

        if ($assignedTo !== null) {
            $data['assigned_to'] = $assignedTo;
        }
        if ($notes !== null) {
            $data['admin_notes'] = $notes;
        }
        if ($status === 'under_review') {
            $data['reviewed_at'] = date('Y-m-d H:i:s');
        }

        $this->db->update('seller_requests', $data, '`id` = :id', ['id' => $id]);
    }

    /**
     * Get documents for a seller request.
     */
    public function getDocuments(int $requestId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM `documents` WHERE `seller_request_id` = :rid ORDER BY `uploaded_at` DESC',
            ['rid' => $requestId]
        );
    }

    /**
     * Count requests by status.
     */
    public function countByStatus(): array
    {
        $rows = $this->db->fetchAll(
            'SELECT `status`, COUNT(*) as `count` FROM `seller_requests` GROUP BY `status`'
        );

        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['count'];
        }
        return $counts;
    }

    /**
     * Get recent requests.
     */
    public function getRecent(int $limit = 5): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM `seller_requests` WHERE `status` != 'draft' ORDER BY `created_at` DESC LIMIT $limit"
        );
    }

    /**
     * Build review data structure from a saved request (for step 3 display).
     */
    public function buildReviewData(array $request): array
    {
        $db = Database::getInstance();

        // Contact details from Step 1
        $contactName  = $request['contact_name'] ?? $request['full_name'] ?? '—';
        $contactPhone = $request['contact_phone'] ?? $request['phone'] ?? '—';
        $contactEmail = $request['contact_email'] ?? $request['email'] ?? '—';
        $contactAddr  = $request['contact_address'] ?? (!empty($request['address']) && empty($request['condition_status']) ? $request['address'] : '') ?: 'Not provided';

        // Property details from Step 2
        $propertyAddress = $request['address'] ?? '—';
        $locationParts = array_filter([
            $request['locality'] ?? '',
            $request['city'] ?? '',
        ]);
        $location = implode(', ', $locationParts);
        if (!empty($request['pincode'])) {
            $location .= " (PIN: {$request['pincode']})";
        }

        $conditionKey = $request['condition_status'] ?? $request['condition'] ?? '';
        $conditionLabel = '';
        if ($conditionKey) {
            try {
                $conditionLabel = $db->fetchColumn(
                    "SELECT `label` FROM `lookup_conditions` WHERE `value` = :v",
                    ['v' => $conditionKey]
                );
            } catch (\Throwable $e) {}
        }
        $conditionLabel = $conditionLabel ?: ($conditionKey ?: '—');

        $priceDisplay = 'Discretionary / Unstated';
        if (!empty($request['target_price'])) {
            $numPrice = (float)str_replace([',', ' '], '', (string)$request['target_price']);
            $priceDisplay = $numPrice > 0 ? '₹ ' . number_format($numPrice) : '₹ ' . $request['target_price'];
        }

        $timelineKey = $request['timeline'] ?? '';
        $timelineLabel = '';
        if ($timelineKey) {
            try {
                $timelineLabel = $db->fetchColumn(
                    "SELECT `label` FROM `lookup_timelines` WHERE `value` = :v",
                    ['v' => $timelineKey]
                );
            } catch (\Throwable $e) {}
        }
        $timelineLabel = $timelineLabel ?: ($timelineKey ?: '—');

        $mandateKey = $request['mandate_structure'] ?? $request['mandate'] ?? '';
        $mandateLabel = '';
        if ($mandateKey) {
            try {
                $mandateLabel = $db->fetchColumn(
                    "SELECT CONCAT(`label`, ' (', COALESCE(`note`, ''), ')') FROM `lookup_mandate_structures` WHERE `value` = :v",
                    ['v' => $mandateKey]
                );
            } catch (\Throwable $e) {}
        }
        $mandateLabel = $mandateLabel ?: ($mandateKey ?: '—');

        return [
            [
                'title'    => 'Contact Details',
                'edit_url' => route('seller.step1'),
                'rows'     => [
                    ['label' => 'Full Name',     'value' => $contactName],
                    ['label' => 'Phone Number',  'value' => $contactPhone],
                    ['label' => 'Email Address', 'value' => $contactEmail],
                    ['label' => 'Address',       'value' => $contactAddr],
                ],
            ],
            [
                'title'    => 'Property Details & Positioning',
                'edit_url' => route('seller.step2'),
                'rows'     => [
                    ['label' => 'Property Address',        'value' => $propertyAddress],
                    ['label' => 'Location',                'value' => $location ?: '—'],
                    ['label' => 'Property Condition',      'value' => $conditionLabel, 'accent' => true],
                    ['label' => 'Indicative Asking Price', 'value' => $priceDisplay],
                    ['label' => 'Sale Timeline',           'value' => $timelineLabel],
                    ['label' => 'Preferred Mandate',       'value' => $mandateLabel],
                ],
            ],
        ];
    }
}
