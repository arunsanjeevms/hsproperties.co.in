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
     * Update step 1 data.
     */
    public function saveStep1(int $id, array $data): void
    {
        $this->db->update('seller_requests', [
            'property_type'    => $data['property_type'] ?? null,
            'property_status'  => $data['property_status'] ?? null,
            'city'             => $data['city'] ?? null,
            'locality'         => $data['locality'] ?? null,
            'pincode'          => $data['pincode'] ?? null,
            'extent'           => $data['extent'] ?? null,
            'measurement_unit' => $data['unit'] ?? null,
            'bedrooms'         => $data['bedrooms'] ?? null,
            'ownership'        => $data['ownership'] ?? null,
            'current_step'     => max(2, $this->getCurrentStep($id)),
        ], '`id` = :id', ['id' => $id]);
    }

    /**
     * Update step 2 data.
     */
    public function saveStep2(int $id, array $data): void
    {
        $this->db->update('seller_requests', [
            'address'           => $data['address'] ?? null,
            'condition_status'  => $data['condition'] ?? null,
            'year_built'        => $data['year_built'] ?? null,
            'furnishing'        => $data['furnishing'] ?? null,
            'target_price'      => $data['target_price'] ?? null,
            'timeline'          => $data['timeline'] ?? null,
            'mandate_structure' => $data['mandate'] ?? null,
            'documents_available' => !empty($data['documents']) ? json_encode($data['documents']) : null,
            'current_step'      => max(3, $this->getCurrentStep($id)),
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

        // Resolve labels from lookup tables
        $propertyTypeLabel = $db->fetchColumn(
            "SELECT CONCAT(`label`, ' (', COALESCE(`note`, ''), ')') FROM `lookup_property_types` WHERE `value` = :v",
            ['v' => $request['property_type'] ?? '']
        ) ?: ($request['property_type'] ?? '—');

        $ownershipLabel = $db->fetchColumn(
            "SELECT `label` FROM `lookup_ownership_types` WHERE `value` = :v",
            ['v' => $request['ownership'] ?? '']
        ) ?: ($request['ownership'] ?? '—');

        $conditionKey = $request['condition_status'] ?? $request['condition'] ?? '';
        $conditionLabel = $db->fetchColumn(
            "SELECT `label` FROM `lookup_conditions` WHERE `value` = :v",
            ['v' => $conditionKey]
        ) ?: ($conditionKey ?: '—');

        $mandateKey = $request['mandate_structure'] ?? $request['mandate'] ?? '';
        $mandateLabel = $db->fetchColumn(
            "SELECT CONCAT(`label`, ' (', COALESCE(`note`, ''), ')') FROM `lookup_mandate_structures` WHERE `value` = :v",
            ['v' => $mandateKey]
        ) ?: ($mandateKey ?: '—');

        $timelineKey = $request['timeline'] ?? '';
        $timelineLabel = $db->fetchColumn(
            "SELECT `label` FROM `lookup_timelines` WHERE `value` = :v",
            ['v' => $timelineKey]
        ) ?: ($timelineKey ?: '—');

        $bedroomKey = $request['bedrooms'] ?? '';
        $bedroomLabel = $db->fetchColumn(
            "SELECT `label` FROM `lookup_bedroom_options` WHERE `value` = :v",
            ['v' => $bedroomKey]
        ) ?: ($bedroomKey ? $bedroomKey . ' BHK' : '—');

        $unitKey = $request['measurement_unit'] ?? $request['unit'] ?? '';
        $unitLabel = $db->fetchColumn(
            "SELECT `label` FROM `lookup_measurement_units` WHERE `value` = :v",
            ['v' => $unitKey]
        ) ?: ($unitKey ?: 'sq ft');

        $priceDisplay = 'Discretionary / Unstated';
        if (!empty($request['target_price'])) {
            $numPrice = (float)str_replace([',', ' '], '', (string)$request['target_price']);
            $priceDisplay = $numPrice > 0 ? '₹ ' . number_format($numPrice) : '₹ ' . $request['target_price'];
        }

        $locationParts = array_filter([
            $request['locality'] ?? '',
            $request['city'] ?? '',
        ]);
        $location = implode(', ', $locationParts);
        if (!empty($request['pincode'])) {
            $location .= " (PIN: {$request['pincode']})";
        }

        $sizeDisplay = ($request['extent'] ?? '—') . ' ' . $unitLabel . ' (Built-up Area)';

        return [
            [
                'title'    => 'Property',
                'edit_url' => route('seller.step1'),
                'rows'     => [
                    ['label' => 'Property Type',      'value' => $propertyTypeLabel],
                    ['label' => 'Location',           'value' => $location ?: '—'],
                    ['label' => 'Property Size',      'value' => $sizeDisplay],
                    ['label' => 'Bedrooms',           'value' => $bedroomLabel],
                    ['label' => 'Property Condition',  'value' => $conditionLabel, 'accent' => true],
                    ['label' => 'Ownership',          'value' => $ownershipLabel],
                ],
            ],
            [
                'title'    => 'Sale Positioning',
                'edit_url' => route('seller.step2'),
                'rows'     => [
                    ['label' => 'Indicative Asking Price', 'value' => $priceDisplay],
                    ['label' => 'Sale Timeline',           'value' => $timelineLabel],
                    ['label' => 'Preferred Mandate',       'value' => $mandateLabel],
                ],
            ],
        ];
    }
}
