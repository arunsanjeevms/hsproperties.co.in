<?php
/**
 * HS Properties — Presentation data.
 *
 * These arrays are deliberately shaped like the database rows that will back
 * them later (one associative array per record, snake_case keys). When the
 * MySQL layer lands, each function here is replaced by a repository query and
 * the templates that consume them do not change.
 *
 * Nothing here is a permanent static table — it is a typed stand-in for the
 * `properties`, `advisory_stages`, and related entities.
 */

declare(strict_types=1);

/**
 * Closed mandates shown in the representative portfolio.
 * Future source: `properties` joined to `requests` where status = 'closed'.
 */
function portfolio_properties(): array
{
    return [
        [
            'reference'   => '#BLR-402',
            'status'      => 'Sold & Concluded',
            'location'    => 'Whitefield, Bengaluru',
            'title'       => 'The Sandstone Pavilion Villa',
            'mandate'     => 'Buyer Representation & Acquisition',
            'spec'        => '4 BHK Cantilevered Residence · 8,200 sq.ft Grounds',
            'summary'     => 'Discreet acquisition advisory completed for private family office; cantilevered limestone architecture with dual reflecting pools.',
            'type'        => 'Independent Estate',
            'image'       => 'assets/images/property-sandstone-pavilion.jpg',
            'image_alt'   => 'The Sandstone Pavilion Villa in Whitefield, Bengaluru — a cantilevered limestone residence lit at dusk beside a reflecting pool.',
        ],
        [
            'reference'   => '#CHE-109',
            'status'      => 'Closed & Delivered',
            'location'    => 'Alwarpet, Chennai',
            'title'       => 'The Louvered Urban Penthouse',
            'mandate'     => 'Exclusive Seller Disposition',
            'spec'        => '4 BHK Floor-Through Residence · 4,800 sq.ft',
            'summary'     => 'Off-market seller representation advised to structured deed closing; low-density vertical estate with biometric direct lift vestibule.',
            'type'        => 'Urban Duplex',
            'image'       => 'assets/images/property-louvered-penthouse.jpg',
            'image_alt'   => 'The Louvered Urban Penthouse in Alwarpet, Chennai — a limestone facade with teak louvers and deep balconies at twilight.',
        ],
        [
            'reference'   => '#ECR-880',
            'status'      => 'Landmark Sale',
            'location'    => 'ECR, Chennai',
            'title'       => 'The Oceanfront Pavilion Estate',
            'mandate'     => 'Bespoke Transaction Advisory',
            'spec'        => '5 BHK Coastal Sanctuary · 20,000 sq.ft Plot',
            'summary'     => 'Discreet coastal acquisition diligence, coastal clearance audits, and bespoke escrow closure for an overseas principal.',
            'type'        => 'Coastal Retreat',
            'image'       => 'assets/images/property-oceanfront-estate.jpg',
            'image_alt'   => 'The Oceanfront Pavilion Estate on ECR, Chennai — a single-storey glass pavilion on a lawn overlooking the sea at dusk.',
        ],
    ];
}

/**
 * The four trust pillars directly beneath the hero.
 */
function trust_pillars(): array
{
    return [
        [
            'icon'  => 'lock',
            'title' => 'Private By Design',
            'text'  => 'Your mandate criteria and asset identity are kept strictly confidential.',
        ],
        [
            'icon'  => 'filter',
            'title' => 'Curated, Not Crowded',
            'text'  => 'We focus on high-fidelity opportunities instead of algorithmic inventory bulk.',
        ],
        [
            'icon'  => 'advisor',
            'title' => 'Advisory-Led',
            'text'  => 'A dedicated senior partner coordinates every negotiation and legal touchpoint.',
        ],
        [
            'icon'  => 'shield-check',
            'title' => 'Verified Opportunities',
            'text'  => 'Titles, municipal clearances, and owner mandates audited prior to private introduction.',
        ],
    ];
}

/**
 * The four-stage advisory mandate band.
 * Future source: `advisory_stages` ordered by sequence.
 */
function advisory_stages(): array
{
    return [
        [
            'number' => '01',
            'label'  => 'Stage 01',
            'title'  => 'Requirement Discovery',
            'text'   => "We decode your family's spatial objectives, neighborhood inclinations, tax horizons, and long-term liquidity plans.",
            'tag'    => 'Consultation',
        ],
        [
            'number' => '02',
            'label'  => 'Stage 02',
            'title'  => 'Property Curation',
            'text'   => 'We filter our vetted register and private off-market relationships to present only genuinely compatible opportunities.',
            'tag'    => 'Sourcing',
        ],
        [
            'number' => '03',
            'label'  => 'Stage 03',
            'title'  => 'Verification & Audit',
            'text'   => 'Thorough title vetting, patta verification, structural sanction checks, and fair market baseline valuation prior to offers.',
            'tag'    => 'Legal Audit',
        ],
        [
            'number' => '04',
            'label'  => 'Stage 04',
            'title'  => 'Transaction Advisory',
            'text'   => 'Advisors oversee price negotiation, escrow modalities, draft documentation, and private deed registration.',
            'tag'    => 'Closing',
        ],
    ];
}

/**
 * Buyer journey steps (vertical journey line).
 */
function buyer_journey(): array
{
    return [
        [
            'index' => '01',
            'title' => 'Share Mandate',
            'text'  => 'Specify topology, micro-market, budget scope, architectural taste, and timelines through our confidential intake channel.',
        ],
        [
            'index' => '02',
            'title' => 'Advisor Review',
            'text'  => 'A dedicated director connects for a structured, confidential briefing call to validate parameters and align timing expectations.',
        ],
        [
            'index' => '03',
            'title' => 'Curated Matches',
            'text'  => 'Selected property portfolios and private off-market dossiers presented directly, complete with audited ownership abstracts.',
        ],
        [
            'index' => '04',
            'title' => 'Viewing & Closing',
            'text'  => 'Private chauffeured tours, full diligence disclosure, price navigation, and structured escrow legal settlement.',
        ],
    ];
}

/**
 * Seller journey steps (horizontal timeline).
 */
function seller_journey(): array
{
    return [
        [
            'index' => '01',
            'label' => 'Step 01',
            'title' => 'Submit Privately',
            'text'  => 'Provide high-level specs, location, and photos via our encrypted intake channel.',
        ],
        [
            'index' => '02',
            'label' => 'Step 02',
            'title' => 'Partner Appraisal',
            'text'  => 'Our team assesses realistic micro-market valuations, uniqueness, and target demographics.',
        ],
        [
            'index' => '03',
            'label' => 'Step 03',
            'title' => 'Verification & Brief',
            'text'  => 'We organize legal title abstracts and build a confidential, presentation-grade dossier.',
        ],
        [
            'index' => '04',
            'label' => 'Step 04',
            'title' => 'Curated Buyer Access',
            'text'  => 'Discreet exposure exclusively to pre-qualified high-net-worth buyers and family offices.',
        ],
    ];
}

/**
 * The four distinction points in the "why HS Properties" editorial section.
 */
function distinction_points(): array
{
    return [
        [
            'icon'  => 'key',
            'title' => 'Discretion',
            'text'  => "Principals' identities, financial arrangements, and private addresses remain shielded.",
        ],
        [
            'icon'  => 'tune',
            'title' => 'Relevance',
            'text'  => 'Zero unfiltered inventory clutter. Every property introduced matches the agreed brief.',
        ],
        [
            'icon'  => 'architecture',
            'title' => 'Expertise',
            'text'  => 'Direct oversight by senior partners with extensive architectural and legal pedigree.',
        ],
        [
            'icon'  => 'fact-check',
            'title' => 'Trust',
            'text'  => 'Complete disclosure, transparent advisory fee structures, and zero hidden markups.',
        ],
    ];
}

/**
 * Institutional governance pillars.
 */
function governance_pillars(): array
{
    return [
        [
            'icon'  => 'verified',
            'title' => 'Strict Confidentiality',
            'text'  => 'Rigorous bilateral non-disclosure agreements safeguard high-profile buyers, corporate directors, and prominent landholders from speculative exposure.',
        ],
        [
            'icon'  => 'rule',
            'title' => 'Transparent Process',
            'text'  => 'Clear transaction roadmaps, pre-vetted legal documentation, verified ownership lineage, and no deceptive bidding practices or fake inventory.',
        ],
        [
            'icon'  => 'contact-phone',
            'title' => 'Advisor Accountability',
            'text'  => 'A single dedicated senior advisor serves as your direct fiduciary liaison from initial requirement discovery through to final deed execution and handoff.',
        ],
    ];
}

/**
 * Footer link groups.
 *
 * Each link carries an optional `route` naming an entry in the route table.
 * Links without one are placeholders until their page exists, so the seller
 * services point at the intake while the buyer-side ones wait for theirs.
 */
function footer_groups(): array
{
    return [
        [
            'heading' => 'Portfolio',
            'links'   => [
                ['label' => 'Private Residences'],
                ['label' => 'Heritage Penthouses'],
                ['label' => 'Country Estates'],
                ['label' => 'Off-Market Register'],
            ],
        ],
        [
            'heading' => 'Advisory',
            'links'   => [
                ['label' => 'Acquisition Mandate'],
                ['label' => 'Discreet Liquidation',  'route' => 'seller.step1'],
                ['label' => 'Valuation & Audit',     'route' => 'seller.step1'],
                ['label' => 'Family Office Services'],
            ],
        ],
    ];
}
