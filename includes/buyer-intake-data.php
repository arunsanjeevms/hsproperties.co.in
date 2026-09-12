<?php
/**
 * HS Properties — Buyer intake option sets and review data.
 *
 * Mirrors includes/seller-intake-data.php: every list is a lookup table in
 * waiting, kept as `value => label` records so the markup already renders
 * whatever the database will later return, and the submitted `value` is the
 * column value.
 *
 * Frontend stage only — nothing here reads or writes storage.
 */

declare(strict_types=1);

/** Shared lookups — measurement units are identical on both sides of a deal. */
require_once __DIR__ . '/seller-intake-data.php';

/** The three stages of the buyer intake, used by the progress rail. */
function buyer_intake_steps(): array
{
    return [
        ['label' => 'Profile',              'href' => route('buyer.step1')],
        ['label' => 'Preferences & Budget', 'href' => route('buyer.step2')],
        ['label' => 'Review & Submit',      'href' => route('buyer.step3')],
    ];
}

/* ==========================================================================
   STEP 01 — Buyer profile
   ========================================================================== */

function buyer_profiles(): array
{
    return [
        ['value' => 'individual', 'label' => 'Private Individual',     'note' => 'Principal purchasing personally'],
        ['value' => 'family',     'label' => 'Family Office',          'note' => 'Multi-generational wealth vehicle'],
        ['value' => 'corporate',  'label' => 'Corporate / LLP',        'note' => 'Company-held acquisition'],
        ['value' => 'nri',        'label' => 'NRI / Overseas Buyer',   'note' => 'Cross-border principal'],
        ['value' => 'investor',   'label' => 'Institutional Investor', 'note' => 'Portfolio or fund mandate'],
        ['value' => 'trust',      'label' => 'Trust / Estate',         'note' => 'Fiduciary or executor custody'],
    ];
}

function buyer_intents(): array
{
    return [
        ['value' => 'primary',     'label' => 'Primary Residence'],
        ['value' => 'second-home', 'label' => 'Second Home'],
        ['value' => 'investment',  'label' => 'Investment'],
        ['value' => 'rental',      'label' => 'Rental Yield'],
        ['value' => 'land-bank',   'label' => 'Land Banking'],
        ['value' => 'other',       'label' => 'Other'],
    ];
}

function contact_channels(): array
{
    return [
        ['value' => 'phone',    'label' => 'Private Call'],
        ['value' => 'whatsapp', 'label' => 'WhatsApp'],
        ['value' => 'email',    'label' => 'Encrypted Email'],
        ['value' => 'inperson', 'label' => 'In Person'],
    ];
}

function contact_windows(): array
{
    return [
        ['value' => 'morning',   'label' => 'Morning (9–12)'],
        ['value' => 'afternoon', 'label' => 'Afternoon (12–4)'],
        ['value' => 'evening',   'label' => 'Evening (4–8)'],
        ['value' => 'anytime',   'label' => 'Anytime'],
    ];
}

function representation_types(): array
{
    return [
        ['value' => 'direct',    'label' => 'Acting Directly',        'note' => 'You are the principal purchaser'],
        ['value' => 'counsel',   'label' => 'Represented by Counsel', 'note' => 'A wealth manager or lawyer acts for you'],
        ['value' => 'broker',    'label' => 'Represented by Broker',  'note' => 'An appointed channel partner acts for you'],
        ['value' => 'anonymous', 'label' => 'Anonymous Mandate',      'note' => 'Identity disclosed strictly post-NDA'],
    ];
}

/* ==========================================================================
   STEP 02 — Preferences & budget
   ========================================================================== */

function buyer_property_types(): array
{
    return [
        ['value' => 'apartment',  'label' => 'Apartment',          'note' => 'High-rise / Penthouse'],
        ['value' => 'villa',      'label' => 'Villa',              'note' => 'Gated Luxury Enclave'],
        ['value' => 'house',      'label' => 'Independent House',  'note' => 'Standalone Bungalow'],
        ['value' => 'plot',       'label' => 'Plot / Land',        'note' => 'Development Parcel'],
        ['value' => 'commercial', 'label' => 'Commercial',         'note' => 'Prime Retail / Office'],
        ['value' => 'estate',     'label' => 'Estate / Farmhouse', 'note' => 'Country Retreat / Acreage'],
    ];
}

function budget_bands(): array
{
    return [
        ['value' => '5-10cr',  'label' => '₹5–10 Cr'],
        ['value' => '10-20cr', 'label' => '₹10–20 Cr'],
        ['value' => '20-35cr', 'label' => '₹20–35 Cr'],
        ['value' => '35-50cr', 'label' => '₹35–50 Cr'],
        ['value' => '50cr',    'label' => '₹50 Cr+'],
        ['value' => 'open',    'label' => 'Open / Advise Me'],
    ];
}

function funding_routes(): array
{
    return [
        ['value' => 'self',      'label' => 'Self-Funded',         'note' => 'Liquid funds available on mandate'],
        ['value' => 'mixed',     'label' => 'Part Financed',       'note' => 'Blend of own funds and facility'],
        ['value' => 'facility',  'label' => 'Lender Facility',     'note' => 'Sanctioned or in-principle approval'],
        ['value' => 'undecided', 'label' => 'Structure Undecided', 'note' => 'To be advised during consultation'],
    ];
}

function possession_windows(): array
{
    return [
        ['value' => 'immediate', 'label' => 'Immediate'],
        ['value' => '3m',        'label' => 'Within 3 Months'],
        ['value' => '3-6m',      'label' => '3–6 Months'],
        ['value' => '6-12m',     'label' => '6–12 Months'],
        ['value' => 'flexible',  'label' => 'Flexible'],
    ];
}

function buyer_bedroom_options(): array
{
    return [
        ['value' => '2',     'label' => '2 BHK'],
        ['value' => '3',     'label' => '3 BHK'],
        ['value' => '4',     'label' => '4 BHK'],
        ['value' => '5plus', 'label' => '5+ BHK'],
        ['value' => 'any',   'label' => 'Open'],
        ['value' => 'na',    'label' => 'N/A (Plot)'],
    ];
}

function buyer_amenities(): array
{
    return [
        ['value' => 'private-pool', 'label' => 'Private Pool',          'status' => 'Preferred', 'staged' => true],
        ['value' => 'garden',       'label' => 'Landscaped Garden',     'status' => 'Preferred', 'staged' => true],
        ['value' => 'staff',        'label' => 'Staff Quarters',        'status' => 'Optional',  'staged' => false],
        ['value' => 'views',        'label' => 'Skyline / Sea Views',   'status' => 'Optional',  'staged' => false],
        ['value' => 'security',     'label' => 'Private Security',      'status' => 'Preferred', 'staged' => true],
        ['value' => 'parking',      'label' => 'Covered Parking (3+)',  'status' => 'Optional',  'staged' => false],
        ['value' => 'smart-home',   'label' => 'Smart Home Systems',    'status' => 'Optional',  'staged' => false],
        ['value' => 'vastu',        'label' => 'Vastu Compliant',       'status' => 'Optional',  'staged' => false],
    ];
}

/* ==========================================================================
   STEP 03 — Review
   ========================================================================== */

/**
 * The mandate as it will be read back for confirmation.
 *
 * Future source: the in-progress `requests` row plus its related records.
 * `accent` marks a value that carries the champagne pip in the design.
 */
function buyer_review_sections(): array
{
    return [
        [
            'title'    => 'Buyer Profile',
            'edit_url' => route('buyer.step1'),
            'rows'     => [
                ['label' => 'Principal',          'value' => 'Private Individual'],
                ['label' => 'Acquisition Intent', 'value' => 'Primary Residence'],
                ['label' => 'Contact',            'value' => '+91 98450 00000', 'muted' => '(Private Call)'],
                ['label' => 'Preferred Window',   'value' => 'Evening (4–8)'],
                ['label' => 'Representation',     'value' => 'Acting Directly', 'accent' => true],
            ],
        ],
        [
            'title'    => 'Preferences & Budget',
            'edit_url' => route('buyer.step2'),
            'rows'     => [
                ['label' => 'Property Type',  'value' => 'Villa (Gated Luxury Enclave)'],
                ['label' => 'Target Markets', 'value' => 'Whitefield, Bengaluru · Koregaon Park, Pune'],
                ['label' => 'Configuration',  'value' => '4 BHK · 6,000 sq ft minimum'],
                ['label' => 'Budget Band',    'value' => '₹20–35 Cr', 'muted' => '(Indicative)', 'accent' => true],
                ['label' => 'Funding Route',  'value' => 'Self-Funded'],
                ['label' => 'Possession',     'value' => '3–6 Months'],
            ],
        ],
    ];
}

/** Amenity priorities echoed back on the review step. */
function buyer_priority_summary(): array
{
    return [
        ['label' => 'Private Pool',      'status' => 'Preferred'],
        ['label' => 'Landscaped Garden', 'status' => 'Preferred'],
        ['label' => 'Private Security',  'status' => 'Preferred'],
    ];
}

/**
 * Reference details issued once the buyer mandate is received.
 * Future source: the created `requests` row.
 */
function buyer_submission_reference(): array
{
    return [
        ['label' => 'Reference',    'value' => 'HS-2025-BUY-3317', 'accent' => true],
        ['label' => 'Status',       'value' => 'Qualification Review'],
        ['label' => 'Mandate Type', 'value' => 'Private Acquisition Desk'],
    ];
}
