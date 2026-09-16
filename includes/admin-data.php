<?php
/**
 * HS Properties — Admin console data.
 *
 * Every record here is transcribed from the Stitch screens so the console
 * renders the blueprint's own content rather than invented stand-ins:
 *   • "HS Properties — Operations Center (Admin Overview)"
 *   • "HS Properties — Requests | Customer Request Inbox"
 *   • "HS Properties — Admin Request Detail View"
 *
 * Frontend stage only — nothing here reads or writes storage. Each function is
 * the shape of the query that will replace it.
 */

declare(strict_types=1);

require_once __DIR__ . '/admin-store.php';

/* ==========================================================================
   SHELL — sidebar, console identity
   ========================================================================== */

/**
 * Sidebar navigation, with the counts the blueprint shows against each item.
 * `route` names an entry in the route table; items without one are not built
 * yet and render as disabled, exactly as in the design.
 */
function admin_nav(): array
{
    return [
        ['key' => 'overview',     'label' => 'Overview',     'icon' => 'tune',         'route' => 'admin.dashboard'],
        ['key' => 'requests',     'label' => 'Requests',     'icon' => 'fact-check',   'route' => 'admin.requests', 'count' => 20],
        ['key' => 'buyers',       'label' => 'Buyers',       'icon' => 'user',         'route' => 'admin.requests', 'query' => 'tab=buyers', 'count' => 12],
        ['key' => 'sellers',      'label' => 'Sellers',      'icon' => 'villa',        'route' => 'admin.requests', 'query' => 'tab=sellers', 'count' => 8],
        ['key' => 'properties',   'label' => 'Properties',   'icon' => 'home',         'route' => 'admin.properties', 'count' => admin_property_counts()['all']],
        ['key' => 'matches',      'label' => 'Matches',      'icon' => 'key',          'count' => 14],
        ['key' => 'appointments', 'label' => 'Appointments', 'icon' => 'contact-phone','count' => 5],
        ['key' => 'documents',    'label' => 'Documents',    'icon' => 'document',     'count' => 6],
        ['key' => 'activity',     'label' => 'Activity',     'icon' => 'rule'],
        ['key' => 'reports',      'label' => 'Reports',      'icon' => 'architecture'],
    ];
}

/** The sidebar's lower utility group. */
function admin_nav_utility(): array
{
    return [
        ['key' => 'settings', 'label' => 'Settings',       'icon' => 'tune'],
        ['key' => 'help',     'label' => 'Help & Support', 'icon' => 'info'],
    ];
}

/** The signed-in operator shown in the sidebar and topbar. */
function admin_operator(): array
{
    return [
        'initials' => 'VK',
        'name'     => 'Vikram K',
        'role'     => 'Administrator',
    ];
}

/* ==========================================================================
   STATUS VOCABULARY
   ========================================================================== */

/** The inbox statuses, in the order the "Change Status" menu lists them. */
function admin_statuses(): array
{
    return [
        'new'        => ['label' => 'New',         'tone' => 'new'],
        'reviewed'   => ['label' => 'Reviewed',    'tone' => 'review'],
        'contacted'  => ['label' => 'Contacted',   'tone' => 'contacted'],
        'progress'   => ['label' => 'In Progress', 'tone' => 'progress'],
        'completed'  => ['label' => 'Completed',   'tone' => 'complete'],
        'hold'       => ['label' => 'On Hold',     'tone' => 'hold'],
    ];
}

function admin_status(string $key): array
{
    return admin_statuses()[$key] ?? ['label' => $key, 'tone' => 'hold'];
}

/** Mandate types. */
function admin_flows(): array
{
    return [
        'buyer'  => ['label' => 'Buyer',  'long' => 'Buyer Request',  'client' => 'Private Client'],
        'seller' => ['label' => 'Seller', 'long' => 'Seller Request', 'client' => 'Seller Submission'],
    ];
}

/* ==========================================================================
   DASHBOARD — "Operations Center"
   ========================================================================== */

/** The greeting block. */
function admin_greeting(): array
{
    return [
        'eyebrow' => 'Operations',
        'systems' => 'All Systems Operational',
        'title'   => 'Good morning.',
        'lede'    => "Here's what needs your attention today across buyers, sellers, and mandates.",
        'day'     => 'Thursday, 11 Sep 2026',
        'time'    => '10:14 IST',
    ];
}

/** The five headline tiles. */
function admin_metrics(): array
{
    return [
        ['label' => 'New Buyer Requests',  'value' => '12', 'note' => '4 more than yesterday',          'tone' => 'gold'],
        ['label' => 'New Seller Mandates', 'value' => '8',  'note' => '2 lodged today',                 'tone' => 'plain'],
        ['label' => 'Awaiting Your Review','value' => '6',  'note' => 'Needs a partner sign-off',        'tone' => 'warn'],
        ['label' => 'Active Matches',      'value' => '14', 'note' => 'At bilateral NDA stage',         'tone' => 'plain'],
        ['label' => 'Appointments Today',  'value' => '5',  'note' => 'Next at 10:00 AM, Whitefield',    'tone' => 'plain'],
    ];
}

/** "Needs Your Attention" — four items requiring immediate action. */
function admin_attention(): array
{
    return [
        [
            'priority' => 'HIGH',
            'title'    => 'Whitefield Villa Mandate',
            'reference'=> 'HS-S-20260911-0043',
            'flow'     => 'seller',
            'text'     => 'Sale deed and encumbrance certificate staged — requires legal checksum verification',
            'meta'     => 'Bengaluru desk',
            'received' => 'Received: 2h ago',
            'action'   => 'Review',
        ],
        [
            'priority' => 'NORMAL',
            'title'    => 'Luxury Penthouse Search (₹15 CR)',
            'reference'=> 'HS-B-20260911-0042',
            'flow'     => 'buyer',
            'text'     => 'Whitefield / Bengaluru • Gated enclave preference • Not yet picked up',
            'meta'     => 'Not yet picked up',
            'received' => 'Received: 3h ago',
            'action'   => 'Assign',
        ],
        [
            'priority' => 'NORMAL',
            'title'    => 'ECR Coastal Residence Verification',
            'reference'=> 'HS-S-20260910-0038',
            'flow'     => 'seller',
            'text'     => 'Coastal CRZ approval and khata certificate pending final legal audit',
            'meta'     => 'Chennai desk',
            'received' => 'Lodged: 5h ago',
            'action'   => 'Review',
        ],
        [
            'priority' => 'FOLLOW-UP',
            'title'    => 'Chennai Institutional Mandate',
            'reference'=> 'HS-B-20260910-0035',
            'flow'     => 'buyer',
            'text'     => 'Follow-up due on curated off-market portfolio dispatch',
            'meta'     => 'Chennai desk',
            'received' => 'Due: 2:00 PM Today',
            'action'   => 'Contact',
        ],
    ];
}

/** The two funnels in the "Active Funnel" card. */
function admin_pipeline(): array
{
    return [
        [
            'title'  => '48 buyer mandates',
            'note'   => '64% convert',
            'stages' => [
                ['label' => 'New enquiry',      'count' => 12],
                ['label' => 'In review',        'count' => 8],
                ['label' => 'Qualifying buyer', 'count' => 5],
                ['label' => 'Matching',         'count' => 14],
                ['label' => 'Viewings',         'count' => 6],
                ['label' => 'Negotiation',      'count' => 3],
            ],
        ],
        [
            'title'  => '33 seller mandates',
            'note'   => 'Strict vetting',
            'stages' => [
                ['label' => 'New mandate',    'count' => 8],
                ['label' => 'In review',      'count' => 6],
                ['label' => 'Legal audit',    'count' => 4],
                ['label' => 'Valuation',      'count' => 3],
                ['label' => 'Curated',        'count' => 5],
                ['label' => 'Matched',        'count' => 7],
            ],
        ],
    ];
}

/** "Shortcuts" quick actions. */
function admin_quick_actions(): array
{
    return [
        ['label' => 'New buyer brief',     'route' => 'buyer.step1'],
        ['label' => 'New seller mandate',  'route' => 'seller.step1'],
        ['label' => 'Add a property',      'route' => 'admin.property.new'],
        ['label' => 'Schedule a meeting',  'route' => null],
    ];
}

/** Dashboard "Recent Requests" table — five rows, as the blueprint shows. */
function admin_recent_requests(): array
{
    return [
        ['reference' => 'HS-B-20260911-0042', 'flow' => 'buyer',  'client' => 'Private Client (HNW)',     'location' => 'Whitefield, BLR',  'desk' => 'Bengaluru', 'status' => 'In Review',       'tone' => 'review',    'updated' => '10 min ago'],
        ['reference' => 'HS-S-20260911-0043', 'flow' => 'seller', 'client' => 'Private Estate Owner',     'location' => 'Whitefield, BLR',  'desk' => 'Bengaluru', 'status' => 'Docs Pending',    'tone' => 'new',       'updated' => '24 min ago'],
        ['reference' => 'HS-B-20260911-0041', 'flow' => 'buyer',  'client' => 'Family Office Principal',  'location' => 'Alwarpet, CHN',    'desk' => 'Chennai',   'status' => 'Matching Active', 'tone' => 'progress',  'updated' => '1h ago'],
        ['reference' => 'HS-S-20260910-0040', 'flow' => 'seller', 'client' => 'Corporate Holding LLP',    'location' => 'ECR Coastal, CHN', 'desk' => 'Chennai',   'status' => 'Valuation Audit', 'tone' => 'review',    'updated' => '3h ago'],
        ['reference' => 'HS-B-20260910-0039', 'flow' => 'buyer',  'client' => 'NR Principal Trust',       'location' => 'Indiranagar, BLR', 'desk' => 'Bengaluru', 'status' => 'Viewing Staged',  'tone' => 'contacted', 'updated' => '5h ago'],
    ];
}

/** "Today (5 sessions)" schedule. */
function admin_schedule(): array
{
    return [
        ['time' => '10:00', 'title' => 'Buyer Consultation',  'state' => 'Confirmed', 'note' => 'Whitefield requirement review • A. Kumar'],
        ['time' => '12:30', 'title' => 'Property Viewing',    'state' => 'Confirmed', 'note' => 'ECR Residence on-site preview • S. Nair'],
        ['time' => '15:00', 'title' => 'Seller Consultation', 'state' => 'Pending',   'note' => 'Independent Villa valuation • R. Menon'],
        ['time' => '18:00', 'title' => 'Property Viewing',    'state' => 'Confirmed', 'note' => 'Alwarpet Penthouse bilateral • A. Kumar'],
    ];
}

/** "Recent Activity" — the immutable audit log. */
function admin_activity(): array
{
    return [
        ['time' => '09:42', 'actor' => 'A. Kumar',   'text' => 'updated buyer requirement budget for', 'reference' => 'HS-B-20260911-0042'],
        ['time' => '09:31', 'actor' => 'S. Nair',    'text' => 'completed checksum verification on Sale Deed for', 'reference' => 'HS-S-20260911-0043'],
        ['time' => '09:18', 'actor' => 'Admin Desk', 'text' => 'moved to matching:', 'reference' => 'HS-B-20260911-0041'],
        ['time' => '09:04', 'actor' => 'R. Menon',   'text' => 'scheduled private viewing for mandate', 'reference' => 'HS-P-00038'],
    ];
}

/* ==========================================================================
   REQUESTS INBOX
   ========================================================================== */

/** The four summary tiles above the inbox. */
function admin_inbox_stats(): array
{
    return [
        ['label' => 'All Requests',   'value' => '28', 'note' => 'In the inbox',          'tone' => 'gold'],
        ['label' => 'New',            'value' => '8',  'note' => 'Not yet opened',        'tone' => 'new'],
        ['label' => 'Needs Review',   'value' => '6',  'note' => 'Waiting on the desk',  'tone' => 'review'],
        ['label' => 'Contacted',      'value' => '14', 'note' => 'Follow-up due',         'tone' => 'contacted'],
    ];
}

/** The inbox tabs. */
function admin_inbox_tabs(): array
{
    return [
        ['key' => 'all',      'label' => 'All',      'count' => 28],
        ['key' => 'buyers',   'label' => 'Buyers',   'count' => 16],
        ['key' => 'sellers',  'label' => 'Sellers',  'count' => 12],
        ['key' => 'archived', 'label' => 'Archived', 'count' => 4],
    ];
}

/** The filter row. Each filter is a select whose first option is the default. */
function admin_inbox_filters(): array
{
    return [
        ['name' => 'status',   'label' => 'Status',   'options' => ['All', 'New', 'Reviewed', 'Contacted', 'In Progress', 'Completed']],
        ['name' => 'location', 'label' => 'Location', 'options' => ['All', 'Bengaluru', 'Chennai']],
        ['name' => 'date',     'label' => 'Date',     'options' => ['Any time', 'Today', 'Last 7 days', 'This month']],
        ['name' => 'sort',     'label' => 'Sort',     'options' => ['Newest first', 'Oldest first', 'Reference ID']],
    ];
}

/**
 * The inbox rows.
 *
 * `archived` marks rows that belong only to the Archived tab, so the tab counts
 * in the design stay meaningful without inventing a second table.
 */
function admin_requests(): array
{
    return [
        [
            'reference' => 'HS-B-20260911-0042', 'flow' => 'buyer', 'received' => 'Received 35 min ago',
            'customer' => 'Meera & Rajesh Singhal', 'location' => 'Whitefield, Bengaluru',
            'status' => 'new', 'desk' => 'Bengaluru', 'archived' => false,
        ],
        [
            'reference' => 'HS-S-20260911-0043', 'flow' => 'seller', 'received' => 'Received 1 hour ago',
            'customer' => 'Whitefield Villa', 'location' => 'Whitefield, Bengaluru',
            'status' => 'progress', 'desk' => 'Bengaluru', 'archived' => false,
        ],
        [
            'reference' => 'HS-B-20260911-0041', 'flow' => 'buyer', 'received' => 'Received 2 hours ago',
            'customer' => 'Kavita Krishnan', 'location' => 'Alwarpet, Chennai',
            'status' => 'contacted', 'desk' => 'Chennai', 'archived' => false,
        ],
        [
            'reference' => 'HS-S-20260910-0040', 'flow' => 'seller', 'received' => 'Received 3 hours ago',
            'customer' => 'ECR Beachfront Villa', 'location' => 'ECR, Chennai',
            'status' => 'reviewed', 'desk' => 'Chennai', 'archived' => false,
        ],
        [
            'reference' => 'HS-B-20260910-0039', 'flow' => 'buyer', 'received' => 'Received 5 hours ago',
            'customer' => 'Tarun Chawla', 'location' => 'Boat Club, Chennai',
            'status' => 'completed', 'desk' => 'Chennai', 'archived' => false,
        ],
        [
            'reference' => 'HS-S-20260910-0038', 'flow' => 'seller', 'received' => 'Received 7 hours ago',
            'customer' => 'Sadashivanagar Villa', 'location' => 'Sadashivanagar, Bengaluru',
            'status' => 'new', 'desk' => 'Bengaluru', 'archived' => false,
        ],
        [
            'reference' => 'HS-B-20260909-0037', 'flow' => 'buyer', 'received' => 'Received 1 day ago',
            'customer' => 'Sundaram Capital', 'location' => 'Indiranagar, Bengaluru',
            'status' => 'progress', 'desk' => 'Bengaluru', 'archived' => false,
        ],
    ];
}

/** Rows for one tab. */
function admin_tab_requests(string $tab): array
{
    $rows = admin_requests();

    if ($tab === 'buyers' || $tab === 'sellers') {
        $flow = $tab === 'buyers' ? 'buyer' : 'seller';
        $rows = array_values(array_filter($rows, function (array $r) use ($flow) {
            return $r['flow'] === $flow;
        }));
    }

    if ($tab === 'archived') {
        $rows = array_values(array_filter($rows, function (array $r) {
            return $r['archived'];
        }));
    }

    return $rows;
}

/** The "Change Status" menu. */
function admin_status_menu(): array
{
    return [
        'Mark as New', 'Mark as Reviewed', 'Mark as Contacted',
        'Mark as In Progress', 'Mark as Completed', 'Put on Hold',
    ];
}

/** Link to one request's detail view. */
function admin_request_url(string $reference): string
{
    return route('admin.request') . '?ref=' . urlencode($reference);
}

/** A single inbox row by reference. */
function admin_request(?string $reference): ?array
{
    foreach (admin_requests() as $request) {
        if ($request['reference'] === $reference) {
            return $request;
        }
    }

    return null;
}

/** The row the detail view opens on by default — the newest. */
function admin_default_request(): array
{
    return admin_requests()[0];
}

/* ==========================================================================
   REQUEST DETAIL VIEW
   ========================================================================== */

/** The customer dossier header. */
function admin_dossier(array $request): array
{
    $buyer = $request['flow'] === 'buyer';

    return [
        'submitted' => 'Submitted: 11 Sep 2026, 10:42 AM',
        'initials'  => $buyer ? 'MS' : 'WV',
        'name'      => $buyer ? 'Meera & Rajesh Singhal' : 'Whitefield Villa',
        'kind'      => admin_flows()[$request['flow']]['client'],
        'phone'     => '+91 98450 12849',
        'email'     => 'm.singhal@outlook.com',
        'phone_label' => 'Phone',
        'email_label' => 'Email',
    ];
}

/** Buyer-mode panels: requirement, budget, priorities, free text. */
function admin_buyer_panels(): array
{
    return [
        'requirement' => [
            'title' => 'Property Requirement',
            'rows'  => [
                ['label' => 'Property Type',         'value' => 'Villa (Gated Luxury Enclave)'],
                ['label' => 'Preferred Location',    'value' => 'Whitefield, Bengaluru'],
                ['label' => 'Alternative Locations', 'value' => 'Sarjapur, Indiranagar'],
                ['label' => 'Bedrooms',              'value' => '4+ BHK'],
                ['label' => 'Preferred Area',        'value' => '2,500 – 4,500 sq ft'],
                ['label' => 'Intended Use',          'value' => 'Primary Residence'],
            ],
        ],
        'budget' => [
            'title' => 'Budget & Timeline',
            'rows'  => [
                ['label' => 'Budget',            'value' => '₹1.5 Cr — ₹2.0 Cr', 'accent' => true],
                ['label' => 'Purchase Timeline', 'value' => '3–6 Months'],
                ['label' => 'Purchase Approach', 'value' => 'Home Loan (Pre-approved)'],
            ],
        ],
        'priorities' => ['Location', 'Privacy', 'Space', 'Connectivity', 'Architecture'],
        'note'       => 'Looking for a quiet family residence with good school access, parking, and a preference for a gated community.',
    ];
}

/** Seller-mode panels: property details and sale expectations. */
function admin_seller_panels(): array
{
    return [
        'details' => [
            'title' => 'Property Details',
            'rows'  => [
                ['label' => 'Property Name',      'value' => 'Whitefield Executive Villa'],
                ['label' => 'Built-up Area',      'value' => '4,200 sq ft'],
                ['label' => 'Bedrooms / Layout',  'value' => '4 BHK + Private Lawn'],
                ['label' => 'Property Condition', 'value' => 'Excellent (Renovated)'],
                ['label' => 'Year Built',         'value' => '2021'],
                ['label' => 'Furnishing',         'value' => 'Fully Furnished'],
            ],
        ],
        'expectations' => [
            'title' => 'Sale Expectations',
            'rows'  => [
                ['label' => 'Asking Price',   'value' => '₹3.2 Cr', 'accent' => true],
                ['label' => 'Sale Timeline',  'value' => '3–6 Months'],
                ['label' => 'Sale Model',    'value' => 'Managed Sale'],
            ],
        ],
    ];
}

/** "Documents Provided". */
function admin_detail_documents(): array
{
    return [
        ['name' => 'Sale Deed.pdf',         'meta' => 'Verified • 3.2 MB'],
        ['name' => 'Tax Receipt.pdf',       'meta' => 'FY 2025-26 • 1.1 MB'],
        ['name' => 'Encumbrance Cert.pdf',  'meta' => 'Nil Encumbrance • 1.8 MB'],
        ['name' => 'Approved Plan.pdf',     'meta' => 'BDA Sanctioned • 5.4 MB'],
    ];
}

/** Internal notes — team only, never shown to the customer. */
function admin_internal_notes(): array
{
    return [
        [
            'author' => 'Admin Desk',
            'time'   => '11 Sep 2026 · 11:20 AM',
            'text'   => 'Customer prefers properties close to international schools.',
        ],
    ];
}

/** The status rail. */
function admin_detail_status(array $request): array
{
    return [
        ['label' => 'Status',    'value' => admin_status($request['status'])['label'], 'pill' => true],
        ['label' => 'Submitted', 'value' => '11 Sep 2026 · 10:42 AM'],
        ['label' => 'Elapsed',   'value' => $request['received']],
    ];
}

/** The detail page's own activity trail. */
function admin_detail_activity(): array
{
    return [
        ['text' => 'Request received via Private Web Intake',    'time' => '11 Sep · 10:42 AM'],
        ['text' => 'Automated pre-qualification check passed',   'time' => '11 Sep · 10:50 AM'],
        ['text' => 'Staged for review',                          'time' => '11 Sep · 11:15 AM'],
    ];
}

/* ==========================================================================
   PROPERTIES — the registry list and one property's detail view

   Written in plain words rather than trade language: the console is operated
   by desk staff, not only by agents, so "Who owns it" beats "Vendor" and
   "Buyers who might want this" beats "Match pipeline".
   ========================================================================== */

/**
 * Property statuses.
 *
 * Deliberately a separate vocabulary from the request statuses: a property is
 * available or sold, a request is new or contacted. Sharing one list would
 * force both into words that fit neither. `tone` reuses the console's existing
 * status colours, so no new CSS is needed. `plain` is the one-sentence
 * explanation shown on the detail page, for staff who do not know the trade
 * shorthand.
 */
function admin_property_statuses(): array
{
    return [
        'available' => ['label' => 'Ready to show',        'tone' => 'complete',  'plain' => 'This property can be shown to buyers right now.'],
        'offer'     => ['label' => 'Offer received',       'tone' => 'review',    'plain' => 'A buyer has made an offer. The owner is considering it.'],
        'reserved'  => ['label' => 'Reserved',             'tone' => 'contacted', 'plain' => 'Held for one buyer while the paperwork is finished. Do not show it to anyone else.'],
        'checking'  => ['label' => 'Papers being checked', 'tone' => 'new',       'plain' => 'Do not show this property to buyers until the documents have been checked.'],
        'sold'      => ['label' => 'Sold',                 'tone' => 'hold',      'plain' => 'The sale is finished. This is kept here only for the record.'],
    ];
}

function admin_property_status(string $key): array
{
    return admin_property_statuses()[$key]
        ?? ['label' => $key, 'tone' => 'hold', 'plain' => ''];
}

/** How many properties sit at each status. */
function admin_property_counts(): array
{
    $counts = ['all' => 0, 'available' => 0, 'checking' => 0, 'offer' => 0, 'reserved' => 0, 'sold' => 0];

    foreach (admin_properties() as $property) {
        $counts['all']++;

        if (isset($counts[$property['status']])) {
            $counts[$property['status']]++;
        }
    }

    return $counts;
}

/** The four counts above the list. */
function admin_property_stats(): array
{
    $counts = admin_property_counts();

    return [
        ['label' => 'All properties',  'value' => (string) $counts['all'],       'note' => 'On our books',         'tone' => 'gold'],
        ['label' => 'Ready to show',   'value' => (string) $counts['available'], 'note' => 'Can be shown today',   'tone' => 'complete'],
        ['label' => 'Papers checking', 'value' => (string) $counts['checking'],  'note' => 'Waiting on documents', 'tone' => 'new'],
        ['label' => 'Offer received',  'value' => (string) $counts['offer'],     'note' => 'A buyer has offered',  'tone' => 'review'],
    ];
}

/** The list's tabs. Each key other than "all" is a status. */
function admin_property_tabs(): array
{
    $counts = admin_property_counts();

    return [
        ['key' => 'all',       'label' => 'All',           'count' => $counts['all']],
        ['key' => 'available', 'label' => 'Ready to show', 'count' => $counts['available']],
        ['key' => 'checking',  'label' => 'Being checked', 'count' => $counts['checking']],
        ['key' => 'sold',      'label' => 'Sold',          'count' => $counts['sold']],
    ];
}

/** The filter row, worded the way a person would ask the question. */
function admin_property_filters(): array
{
    return [
        ['name' => 'city',  'label' => 'City',  'options' => ['All', 'Bengaluru', 'Chennai']],
        ['name' => 'kind',  'label' => 'Type',  'options' => ['All', 'Villa', 'Apartment', 'Penthouse']],
        ['name' => 'price', 'label' => 'Price', 'options' => ['Any price', 'Under 2 Cr', '2 to 4 Cr', 'Above 4 Cr']],
        ['name' => 'sort',  'label' => 'Show',  'options' => ['Newest first', 'Oldest first', 'Most expensive first']],
    ];
}

/**
 * Everything on our books: properties added through the console first, then
 * the seeded ones. Newest at the top, as the list's default sort claims.
 */
function admin_properties(): array
{
    return array_merge(admin_stored_properties(), admin_seed_properties());
}

/**
 * The seeded registry.
 *
 * `code` is the reference an operator reads aloud on the phone, so it is the
 * key the detail view is addressed by.
 */
function admin_seed_properties(): array
{
    return [
        [
            'code' => 'HS-P-0142', 'name' => 'Whitefield Executive Villa',
            'kind' => 'Villa', 'city' => 'Bengaluru', 'area' => 'Whitefield',
            'price' => '₹3.2 Cr', 'size' => '4,200 sq ft', 'beds' => '4 BHK',
            'status' => 'available', 'owner' => 'Private Estate Owner',
            'owner_phone' => '+91 98450 12849', 'owner_email' => 'estate.whitefield@outlook.com',
            'listed' => '11 Sep 2026', 'matches' => 4,
            'image' => 'assets/images/hero-villa.jpg',
            'summary' => 'A renovated four-bedroom villa inside a gated enclave, with a private lawn and covered parking for three cars.',
        ],
        [
            'code' => 'HS-P-0141', 'name' => 'ECR Beachfront Residence',
            'kind' => 'Villa', 'city' => 'Chennai', 'area' => 'ECR',
            'price' => '₹5.8 Cr', 'size' => '6,100 sq ft', 'beds' => '5 BHK',
            'status' => 'checking', 'owner' => 'Corporate Holding LLP',
            'owner_phone' => '+91 98401 55210', 'owner_email' => 'desk@corporateholding.in',
            'listed' => '10 Sep 2026', 'matches' => 2,
            'image' => 'assets/images/property-oceanfront-estate.jpg',
            'summary' => 'A sea-facing house on the East Coast Road. The coastal approval and the khata certificate are still being checked.',
        ],
        [
            'code' => 'HS-P-0139', 'name' => 'Indiranagar Louvered Penthouse',
            'kind' => 'Penthouse', 'city' => 'Bengaluru', 'area' => 'Indiranagar',
            'price' => '₹4.4 Cr', 'size' => '3,800 sq ft', 'beds' => '4 BHK',
            'status' => 'offer', 'owner' => 'Sundaram Capital',
            'owner_phone' => '+91 99002 71184', 'owner_email' => 'property@sundaramcapital.in',
            'listed' => '09 Sep 2026', 'matches' => 5,
            'image' => 'assets/images/property-louvered-penthouse.jpg',
            'summary' => 'A top-floor home with a wrap-around terrace. One buyer has offered ₹4.2 Cr and the owner has not answered yet.',
        ],
        [
            'code' => 'HS-P-0137', 'name' => 'Alwarpet Sandstone Pavilion',
            'kind' => 'Apartment', 'city' => 'Chennai', 'area' => 'Alwarpet',
            'price' => '₹2.6 Cr', 'size' => '2,900 sq ft', 'beds' => '3 BHK',
            'status' => 'reserved', 'owner' => 'Kavita Krishnan',
            'owner_phone' => '+91 98403 66127', 'owner_email' => 'kavita.krishnan@outlook.com',
            'listed' => '07 Sep 2026', 'matches' => 1,
            'image' => 'assets/images/property-sandstone-pavilion.jpg',
            'summary' => 'Held for one buyer until 30 September while their home loan is approved. Do not show it to anyone else until then.',
        ],
        [
            'code' => 'HS-P-0134', 'name' => 'Sadashivanagar Garden Villa',
            'kind' => 'Villa', 'city' => 'Bengaluru', 'area' => 'Sadashivanagar',
            'price' => '₹6.5 Cr', 'size' => '5,400 sq ft', 'beds' => '5 BHK',
            'status' => 'available', 'owner' => 'NR Principal Trust',
            'owner_phone' => '+91 98866 40912', 'owner_email' => 'trust@nrprincipal.in',
            'listed' => '04 Sep 2026', 'matches' => 3,
            'image' => 'assets/images/villa-terrace.jpg',
            'summary' => 'A large corner-plot villa with a mature garden and a separate staff block. The owner prefers weekday viewings.',
        ],
        [
            'code' => 'HS-P-0128', 'name' => 'Boat Club Heritage Apartment',
            'kind' => 'Apartment', 'city' => 'Chennai', 'area' => 'Boat Club',
            'price' => '₹4.9 Cr', 'size' => '3,300 sq ft', 'beds' => '4 BHK',
            'status' => 'sold', 'owner' => 'Tarun Chawla',
            'owner_phone' => '+91 98410 30045', 'owner_email' => 'tarun.chawla@outlook.com',
            'listed' => '28 Aug 2026', 'matches' => 0,
            'image' => 'assets/images/property-sandstone-pavilion.jpg',
            'summary' => 'The sale was completed on 6 September 2026. It is kept on the list so the paperwork stays easy to find.',
        ],
    ];
}

/** Rows for one tab. "all" shows everything; every other tab is one status. */
function admin_tab_properties(string $tab): array
{
    $rows = admin_properties();

    if ($tab === 'all') {
        return $rows;
    }

    return array_values(array_filter($rows, function (array $p) use ($tab) {
        return $p['status'] === $tab;
    }));
}

/** Link to one property's detail view. */
function admin_property_url(string $code): string
{
    return route('admin.property') . '?code=' . urlencode($code);
}

/** A single property by code, or null when the code is unknown. */
function admin_property(?string $code): ?array
{
    foreach (admin_properties() as $property) {
        if ($property['code'] === $code) {
            return $property;
        }
    }

    return null;
}

/** The property the detail view opens on when no code is given. */
function admin_default_property(): array
{
    return admin_properties()[0];
}

/* --------------------------------------------------------------------------
   ONE PROPERTY — detail panels
   -------------------------------------------------------------------------- */

/** "What this property is" — the facts staff are asked for on the phone. */
function admin_property_facts(array $property): array
{
    return [
        ['label' => 'Type of property',   'value' => $property['kind']],
        ['label' => 'Bedrooms',           'value' => $property['beds']],
        ['label' => 'Size',               'value' => $property['size']],
        ['label' => 'Where it is',        'value' => $property['area'] . ', ' . $property['city']],
        ['label' => 'Asking price',       'value' => $property['price'], 'accent' => true],
        ['label' => 'On our books since', 'value' => $property['listed']],
    ];
}

/** "Who owns it" — the panel staff open before making a call. */
function admin_property_owner(array $property): array
{
    return [
        ['label' => 'Owner',        'value' => $property['owner']],
        ['label' => 'Phone number', 'value' => $property['owner_phone']],
        ['label' => 'Email',        'value' => $property['owner_email']],
    ];
}

/** Papers held for this property, and whether each one has been checked. */
function admin_property_documents(array $property): array
{
    $checked = $property['status'] !== 'checking';

    return [
        ['name' => 'Sale Deed.pdf',        'meta' => $checked ? 'Checked • 3.2 MB' : 'Not checked yet • 3.2 MB'],
        ['name' => 'Tax Receipt.pdf',      'meta' => 'Year 2025-26 • 1.1 MB'],
        ['name' => 'Encumbrance Cert.pdf', 'meta' => $checked ? 'No loan on the property • 1.8 MB' : 'Not checked yet • 1.8 MB'],
        ['name' => 'Approved Plan.pdf',    'meta' => 'Sanctioned plan • 5.4 MB'],
    ];
}

/**
 * Buyers who might want this property.
 *
 * `fit` is a plain percentage, and every row carries a sentence saying why:
 * a bare number invites staff to read it as a promise rather than a suggestion.
 */
function admin_property_matches(string $code): array
{
    $matches = [
        'HS-P-0142' => [
            [
                'name' => 'Meera & Rajesh Singhal', 'reference' => 'HS-B-20260911-0042',
                'budget' => '₹1.5 Cr — ₹2.0 Cr', 'fit' => 92, 'status' => 'new',
                'why' => 'They want a 4 BHK villa in a gated enclave in Whitefield. This is the closest match on our books.',
            ],
            [
                'name' => 'Sundaram Capital', 'reference' => 'HS-B-20260909-0037',
                'budget' => '₹3.0 Cr — ₹4.0 Cr', 'fit' => 78, 'status' => 'progress',
                'why' => 'The price and the size both fit. They asked for Indiranagar, so the area is a stretch.',
            ],
            [
                'name' => 'Kavita Krishnan', 'reference' => 'HS-B-20260911-0041',
                'budget' => '₹2.5 Cr — ₹3.5 Cr', 'fit' => 64, 'status' => 'contacted',
                'why' => 'The budget works. They are looking in Chennai, so call only if they will consider moving city.',
            ],
            [
                'name' => 'Tarun Chawla', 'reference' => 'HS-B-20260910-0039',
                'budget' => '₹3.0 Cr — ₹3.5 Cr', 'fit' => 55, 'status' => 'completed',
                'why' => 'They already bought a home through us in September. Listed here only for the record.',
            ],
        ],
    ];

    return $matches[$code] ?? [
        [
            'name' => 'Sundaram Capital', 'reference' => 'HS-B-20260909-0037',
            'budget' => '₹3.0 Cr — ₹4.0 Cr', 'fit' => 71, 'status' => 'progress',
            'why' => 'The budget and the size fit. Nobody has called them about this property yet.',
        ],
        [
            'name' => 'Kavita Krishnan', 'reference' => 'HS-B-20260911-0041',
            'budget' => '₹2.5 Cr — ₹3.5 Cr', 'fit' => 58, 'status' => 'contacted',
            'why' => 'A partial fit — the right price, but not the area they asked for.',
        ],
    ];
}

/** How well a match fits, said in words as well as a number. */
function admin_match_verdict(int $fit): string
{
    if ($fit >= 85) {
        return 'Very good match';
    }

    if ($fit >= 70) {
        return 'Good match';
    }

    return 'Partial match';
}

/** What has happened to this property, oldest first. */
function admin_property_activity(array $property): array
{
    return [
        ['text' => 'Owner signed the listing agreement', 'time' => $property['listed'] . ' · 10:20 AM'],
        ['text' => 'Photographs added to the file',      'time' => $property['listed'] . ' · 04:10 PM'],
        ['text' => 'Documents sent for legal checking',  'time' => '12 Sep 2026 · 11:05 AM'],
    ];
}
