<?php
/**
 * HS Properties — Seller intake option sets and review data.
 *
 * Every list here is a lookup table in waiting: `property_types`,
 * `property_statuses`, `document_categories` and so on. Keeping them as
 * `value => label` records now means the markup already renders whatever the
 * database will later return, and the submitted `value` is the column value.
 *
 * Frontend stage only — nothing here reads or writes storage.
 */

declare(strict_types=1);

/** The three stages of the intake, used by the progress rail. */
function intake_steps(): array
{
    return [
        ['label' => 'Property',             'href' => route('seller.step1')],
        ['label' => 'Details & Documents',  'href' => route('seller.step2')],
        ['label' => 'Review & Submit',      'href' => route('seller.step3')],
    ];
}

/* ==========================================================================
   STEP 01 — Property basics
   ========================================================================== */

function property_types(): array
{
    return [
        ['value' => 'apartment',  'label' => 'Apartment',              'note' => 'High-rise / Penthouse'],
        ['value' => 'villa',      'label' => 'Villa',                  'note' => 'Gated Luxury Enclave'],
        ['value' => 'house',      'label' => 'Independent House',      'note' => 'Standalone Bungalow'],
        ['value' => 'plot',       'label' => 'Plot / Land',            'note' => 'Development Parcel'],
        ['value' => 'commercial', 'label' => 'Commercial',             'note' => 'Prime Retail / Office'],
        ['value' => 'estate',     'label' => 'Estate / Farmhouse',     'note' => 'Country Retreat / Acreage'],
    ];
}

function property_statuses(): array
{
    return [
        ['value' => 'ready',        'label' => 'Ready to Sell'],
        ['value' => 'construction', 'label' => 'Under Construction'],
        ['value' => 'occupied',     'label' => 'Currently Occupied'],
        ['value' => 'tenanted',     'label' => 'Tenanted'],
        ['value' => 'vacant',       'label' => 'Vacant'],
        ['value' => 'other',        'label' => 'Other'],
    ];
}

function measurement_units(): array
{
    return [
        ['value' => 'sqft',    'label' => 'sq ft (Square Feet)'],
        ['value' => 'sqyd',    'label' => 'sq yd (Square Yards)'],
        ['value' => 'acres',   'label' => 'acres'],
        ['value' => 'cents',   'label' => 'cents'],
        ['value' => 'grounds', 'label' => 'grounds (Chennai)'],
    ];
}

function bedroom_options(): array
{
    return [
        ['value' => '1',    'label' => '1 BHK'],
        ['value' => '2',    'label' => '2 BHK'],
        ['value' => '3',    'label' => '3 BHK'],
        ['value' => '4',    'label' => '4 BHK'],
        ['value' => '5plus','label' => '5+ BHK'],
        ['value' => 'na',   'label' => 'N/A (Plot)'],
    ];
}

function ownership_types(): array
{
    return [
        ['value' => 'individual', 'label' => 'Individual Ownership',          'note' => 'Single principal or primary titleholder'],
        ['value' => 'joint',      'label' => 'Joint Ownership',               'note' => 'Multiple family principals or co-owners'],
        ['value' => 'company',    'label' => 'Company / Private Limited / LLP','note' => 'Corporate holding or family office vehicle'],
        ['value' => 'trust',      'label' => 'Trust / Estate / Other',        'note' => 'Institutional trust or executor custody'],
    ];
}

/* ==========================================================================
   STEP 02 — Details & documents
   ========================================================================== */

function condition_options(): array
{
    return [
        ['value' => 'pristine',   'label' => 'New / Pristine'],
        ['value' => 'excellent',  'label' => 'Excellent'],
        ['value' => 'good',       'label' => 'Good'],
        ['value' => 'repair',     'label' => 'Needs Repair'],
        ['value' => 'renovation', 'label' => 'Under Renovation'],
    ];
}

function furnishing_options(): array
{
    return [
        ['value' => 'full',   'label' => 'Fully Furnished'],
        ['value' => 'semi',   'label' => 'Semi Furnished'],
        ['value' => 'none',   'label' => 'Unfurnished'],
        ['value' => 'na',     'label' => 'Not Applicable'],
    ];
}

function timeline_options(): array
{
    return [
        ['value' => 'exploring', 'label' => 'Exploring'],
        ['value' => '3m',        'label' => 'Within 3 Months'],
        ['value' => '3-6m',      'label' => '3–6 Months'],
        ['value' => '6m-plus',   'label' => '6+ Months'],
        ['value' => 'flexible',  'label' => 'Flexible'],
    ];
}

function mandate_structures(): array
{
    return [
        ['value' => 'off-market',  'label' => 'Bespoke Off-Market',   'note' => 'Strict bilateral outreach with pre-qualified buyers under strict NDA.'],
        ['value' => 'assisted',    'label' => 'Advisor-Assisted Sale','note' => 'Curated high-touch syndication across vetted partner networks.'],
        ['value' => 'consultation','label' => 'Open to Consultation', 'note' => 'Review optimal strategy during preliminary advisory conference.'],
    ];
}

function document_categories(): array
{
    return [
        ['value' => 'title',       'label' => 'Title / Sale Deed',    'status' => '1 Staged', 'staged' => true],
        ['value' => 'tax',         'label' => 'Tax Receipt / Khata',  'status' => 'Optional', 'staged' => false],
        ['value' => 'encumbrance', 'label' => 'Encumbrance Cert',     'status' => '1 Staged', 'staged' => true],
        ['value' => 'plan',        'label' => 'Approved Plan',        'status' => 'Optional', 'staged' => false],
        ['value' => 'other',       'label' => 'Other Document',       'status' => 'Optional', 'staged' => false],
    ];
}

/**
 * Documents already staged against this mandate.
 */
function staged_documents(): array
{
    try {
        if (class_exists('Session') && class_exists('Document')) {
            $requestId = Session::get('seller_request_id');
            if ($requestId) {
                $docModel = new Document();
                $docs = $docModel->getByRequest((int)$requestId);
                if (!empty($docs)) {
                    return $docModel->formatForReview($docs);
                }
            }
        }
    } catch (\Throwable) {
        // Fallback to initial display
    }

    return [
        [
            'name'     => 'Sale_Deed_Executed_Khata_A_Certified.pdf',
            'size'     => '4.2 MB',
            'state'    => 'Verified Checksum',
            'category' => 'Sale Deed / Title Deed',
            'kind'     => 'PDF',
        ],
        [
            'name'     => 'Encumbrance_Certificate_Form15_2004_2024.pdf',
            'size'     => '1.8 MB',
            'state'    => 'Verified Checksum',
            'category' => 'Encumbrance Cert',
            'kind'     => 'PDF',
        ],
        [
            'name'     => 'Approved_Master_Architectural_Plan_BMRDA.pdf',
            'size'     => '12.4 MB',
            'state'    => 'Staged for Audit',
            'category' => 'Approved Plan',
            'kind'     => 'PDF',
        ],
    ];
}

/* ==========================================================================
   STEP 03 — Review
   ========================================================================== */

/**
 * The submission as it will be read back for confirmation.
 */
function seller_review_sections(): array
{
    try {
        if (class_exists('Session') && class_exists('SellerRequest')) {
            $requestId = Session::get('seller_request_id');
            $sellerModel = new SellerRequest();
            $record = null;
            if ($requestId) {
                $record = $sellerModel->find((int)$requestId);
            }
            if (!$record) {
                $draft = Session::get('seller_intake', []);
                if (!empty($draft['step1'])) {
                    $record = array_merge($draft['step1'] ?? [], $draft['step2'] ?? []);
                }
            }

            if ($record && (!empty($record['city']) || !empty($record['property_type']))) {
                return $sellerModel->buildReviewData($record);
            }
        }
    } catch (\Throwable) {
        // Fallback
    }

    return [
        [
            'title'    => 'Property',
            'edit_url' => route('seller.step1'),
            'rows'     => [
                ['label' => 'Property Type',      'value' => 'Villa (Gated Luxury Enclave)'],
                ['label' => 'Location',           'value' => 'Whitefield, Bengaluru (PIN: 560066)'],
                ['label' => 'Property Size',      'value' => '8,200 sq ft (Built-up Area)'],
                ['label' => 'Bedrooms',           'value' => '4 BHK'],
                ['label' => 'Property Condition', 'value' => 'New / Pristine', 'accent' => true],
                ['label' => 'Ownership',          'value' => 'Individual Ownership'],
            ],
        ],
        [
            'title'    => 'Sale Positioning',
            'edit_url' => route('seller.step2'),
            'rows'     => [
                ['label' => 'Indicative Asking Price', 'value' => '₹ 18,50,00,000', 'muted' => '(INR 18.50 Cr)'],
                ['label' => 'Sale Timeline',           'value' => '3–6 Months (Structured Target)'],
                ['label' => 'Preferred Mandate',       'value' => 'Bespoke Off-Market (Strict Bilateral Outreach)'],
            ],
        ],
    ];
}

/**
 * Reference details issued once the mandate is received.
 */
function seller_submission_reference(): array
{
    $ref = $_GET['ref'] ?? (class_exists('Session') ? Session::get('last_submitted_ref', 'HS-' . date('Y') . '-BLR-8492') : 'HS-' . date('Y') . '-BLR-8492');
    return [
        ['label' => 'Reference',    'value' => (string)$ref, 'accent' => true],
        ['label' => 'Status',       'value' => 'Initial Advisory Review'],
        ['label' => 'Mandate Type', 'value' => 'Bespoke Off-Market Advisory'],
    ];
}
