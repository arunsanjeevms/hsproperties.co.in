<?php
/**
 * HS Properties — Post-submission (Request Submitted & Review) data.
 *
 * One page serves both intakes. Everything that differs between the buyer and
 * seller mandate is declared here as a single record per flow, so the template
 * stays free of conditionals and a third flow is a data change only.
 *
 * Frontend stage only — nothing here reads or writes storage.
 */

declare(strict_types=1);

require_once __DIR__ . '/seller-intake-data.php';
require_once __DIR__ . '/buyer-intake-data.php';

/**
 * Normalise the requested flow to one we actually serve.
 *
 * Future source: the submitted `requests` row, which carries its own type.
 */
function submission_flow(?string $flow): string
{
    return $flow === 'seller' ? 'seller' : 'buyer';
}

/**
 * The whole page, per flow: copy, reference block, and review stages.
 */
function submission_view(string $flow): array
{
    $views = [
        'buyer' => [
            'flow_label'   => 'Buyer Intake',
            'eyebrow'      => 'Private Acquisition Mandate',
            'aside_title'  => 'Your brief is with',
            'aside_accent' => 'our client desk.',
            'aside_lede'   => 'Nothing further is required from you right now. Our team '
                            . 'qualifies the mandate, then contacts you with the properties worth '
                            . 'your attention — never a list, always a shortlist.',
            'protocol'     => 'Your identity and budget are withheld from sellers until you '
                            . 'instruct otherwise. The brief is never circulated to broker networks.',
            'assurances'   => [
                'Mandate qualified by a Senior Managing Partner',
                'Budget and identity held confidentially',
                'Matched only against off-market mandates',
            ],
            'phase'        => 'Phase: Qualification & Matching',
            'title'        => 'Request submitted.',
            'title_accent' => 'Review has begun.',
            'text'         => 'We&rsquo;ve received your acquisition brief. The HS Properties team '
                            . 'will qualify the mandate and contact you regarding the next '
                            . 'steps within one business day.',
            'reference'    => buyer_submission_reference(),
            'summary'      => buyer_review_sections(),
            'edit_route'   => 'buyer.step3',
            'restart'      => 'buyer.step1',
            'restart_label'=> 'Start another brief',
            'stages'       => [
                ['label' => 'Brief received',            'value' => 'Complete',    'state' => 'done'],
                ['label' => 'Qualification review',      'value' => 'In progress', 'state' => 'current'],
                ['label' => 'Introduction call', 'value' => 'Within 1 business day', 'state' => 'todo'],
                ['label' => 'Curated shortlist issued',  'value' => 'Post-NDA',    'state' => 'todo'],
                ['label' => 'Private viewings arranged', 'value' => 'By mutual appointment', 'state' => 'todo'],
            ],
            'desk'         => [
                ['label' => 'Desk',            'value' => 'Private Acquisition Desk'],
                ['label' => 'Assigned Partner','value' => 'Senior Managing Partner, Bengaluru'],
                ['label' => 'Response Window', 'value' => 'Within 1 business day'],
            ],
        ],

        'seller' => [
            'flow_label'   => 'Seller Intake',
            'eyebrow'      => 'Private Property Mandate',
            'aside_title'  => 'Your property is with',
            'aside_accent' => 'our client desk.',
            'aside_lede'   => 'Nothing further is required from you right now. Our team '
                            . 'reviews the property and documentation, then contacts you to agree '
                            . 'valuation and how the mandate should be positioned.',
            'protocol'     => 'Your submission is reviewed by the HS Properties team '
                            . 'alone. It will not automatically become a public listing.',
            'assurances'   => [
                'Property reviewed by a Senior Managing Partner',
                'Documentation handled under encrypted custody',
                'No public listing or portal syndication',
            ],
            'phase'        => 'Phase: Valuation & Positioning',
            'title'        => 'Request submitted.',
            'title_accent' => 'Review has begun.',
            'text'         => 'We&rsquo;ve received your submission. The HS Properties team '
                            . 'will review the property and contact you regarding the next steps '
                            . 'within one business day.',
            'reference'    => seller_submission_reference(),
            'summary'      => seller_review_sections(),
            'edit_route'   => 'seller.step3',
            'restart'      => 'seller.step1',
            'restart_label'=> 'Submit another property',
            'stages'       => [
                ['label' => 'Submission received',        'value' => 'Complete',    'state' => 'done'],
                ['label' => 'Documentation scrutiny',     'value' => 'In progress', 'state' => 'current'],
                ['label' => 'Valuation call',     'value' => 'Within 1 business day', 'state' => 'todo'],
                ['label' => 'Mandate terms agreed',       'value' => 'Post-NDA',    'state' => 'todo'],
                ['label' => 'Discreet buyer outreach',    'value' => 'On your instruction', 'state' => 'todo'],
            ],
            'desk'         => [
                ['label' => 'Desk',            'value' => 'Private Disposal Desk'],
                ['label' => 'Assigned Partner','value' => 'Senior Managing Partner, Bengaluru'],
                ['label' => 'Response Window', 'value' => 'Within 1 business day'],
            ],
        ],
    ];

    return $views[$flow];
}

/**
 * The progress rail for the flow, with a fourth completed stage appended so
 * the submitted state reads as finished rather than stalled on Step 03.
 */
function submission_steps(string $flow): array
{
    $steps = $flow === 'seller' ? intake_steps() : buyer_intake_steps();
    $steps[] = ['label' => 'Submitted', 'href' => route('intake.submitted')];

    return $steps;
}
