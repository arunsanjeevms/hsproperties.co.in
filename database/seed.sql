-- ============================================================================
-- HS Properties — Seed Data
-- Populates lookup tables, initial admin user, and portfolio properties.
-- ============================================================================

USE `hsproperties`;

-- ----------------------------------------------------------------------------
-- Admin user (password: changeme123 — CHANGE AFTER FIRST LOGIN)
-- Password hash generated with password_hash('changeme123', PASSWORD_BCRYPT)
-- ----------------------------------------------------------------------------
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`) VALUES
('Admin', 'admin@hsproperties.com', '$2y$12$Jp3E0xyaTw.1YVC5Z9k7POhjPUgdrJz8gDPtULQW7LNYck/YrVJgy', 'admin');

-- ----------------------------------------------------------------------------
-- Lookup: Property Types
-- ----------------------------------------------------------------------------
INSERT INTO `lookup_property_types` (`value`, `label`, `note`, `sort`) VALUES
('apartment',  'Apartment',            'High-rise / Penthouse',            1),
('villa',      'Villa',                'Gated Luxury Enclave',             2),
('house',      'Independent House',    'Standalone Bungalow',              3),
('plot',       'Plot / Land',          'Development Parcel',               4),
('commercial', 'Commercial',           'Prime Retail / Office',            5),
('estate',     'Estate / Farmhouse',   'Country Retreat / Acreage',        6);

-- ----------------------------------------------------------------------------
-- Lookup: Property Statuses
-- ----------------------------------------------------------------------------
INSERT INTO `lookup_property_statuses` (`value`, `label`, `sort`) VALUES
('ready',        'Ready to Sell',        1),
('construction', 'Under Construction',   2),
('occupied',     'Currently Occupied',   3),
('tenanted',     'Tenanted',             4),
('vacant',       'Vacant',               5),
('other',        'Other',                6);

-- ----------------------------------------------------------------------------
-- Lookup: Ownership Types
-- ----------------------------------------------------------------------------
INSERT INTO `lookup_ownership_types` (`value`, `label`, `note`, `sort`) VALUES
('individual', 'Individual Ownership',           'Single principal or primary titleholder',                1),
('joint',      'Joint Ownership',                'Multiple family principals or co-owners',                2),
('company',    'Company / Private Limited / LLP','Corporate holding or family office vehicle',             3),
('trust',      'Trust / Estate / Other',         'Institutional trust or executor custody',                4);

-- ----------------------------------------------------------------------------
-- Lookup: Conditions
-- ----------------------------------------------------------------------------
INSERT INTO `lookup_conditions` (`value`, `label`, `sort`) VALUES
('pristine',   'New / Pristine',       1),
('excellent',  'Excellent',            2),
('good',       'Good',                 3),
('repair',     'Needs Repair',         4),
('renovation', 'Under Renovation',     5);

-- ----------------------------------------------------------------------------
-- Lookup: Furnishing
-- ----------------------------------------------------------------------------
INSERT INTO `lookup_furnishing` (`value`, `label`, `sort`) VALUES
('full',  'Fully Furnished',     1),
('semi',  'Semi Furnished',      2),
('none',  'Unfurnished',         3),
('na',    'Not Applicable',      4);

-- ----------------------------------------------------------------------------
-- Lookup: Timelines
-- ----------------------------------------------------------------------------
INSERT INTO `lookup_timelines` (`value`, `label`, `sort`) VALUES
('exploring', 'Exploring',           1),
('3m',        'Within 3 Months',     2),
('3-6m',      '3–6 Months',          3),
('6m-plus',   '6+ Months',           4),
('flexible',  'Flexible',            5);

-- ----------------------------------------------------------------------------
-- Lookup: Mandate Structures
-- ----------------------------------------------------------------------------
INSERT INTO `lookup_mandate_structures` (`value`, `label`, `note`, `sort`) VALUES
('off-market',   'Bespoke Off-Market',    'Strict bilateral outreach with pre-qualified buyers under strict NDA.',           1),
('assisted',     'Advisor-Assisted Sale', 'Curated high-touch syndication across vetted partner networks.',                  2),
('consultation', 'Open to Consultation',  'Review optimal strategy during preliminary advisory conference.',                 3);

-- ----------------------------------------------------------------------------
-- Lookup: Measurement Units
-- ----------------------------------------------------------------------------
INSERT INTO `lookup_measurement_units` (`value`, `label`, `sort`) VALUES
('sqft',    'sq ft (Square Feet)',   1),
('sqyd',    'sq yd (Square Yards)',  2),
('acres',   'acres',                 3),
('cents',   'cents',                 4),
('grounds', 'grounds (Chennai)',     5);

-- ----------------------------------------------------------------------------
-- Lookup: Bedroom Options
-- ----------------------------------------------------------------------------
INSERT INTO `lookup_bedroom_options` (`value`, `label`, `sort`) VALUES
('1',     '1 BHK',     1),
('2',     '2 BHK',     2),
('3',     '3 BHK',     3),
('4',     '4 BHK',     4),
('5plus', '5+ BHK',    5),
('na',    'N/A (Plot)', 6);

-- ----------------------------------------------------------------------------
-- Portfolio Properties (displayed on the home page)
-- ----------------------------------------------------------------------------
INSERT INTO `properties` (`reference`, `status`, `location`, `title`, `mandate`, `spec`, `summary`, `type`, `image`, `image_alt`, `is_portfolio`, `is_active`, `sort_order`) VALUES
(
  '#BLR-402',
  'Sold & Concluded',
  'Whitefield, Bengaluru',
  'The Sandstone Pavilion Villa',
  'Buyer Representation & Acquisition',
  '4 BHK Cantilevered Residence · 8,200 sq.ft Grounds',
  'Discreet acquisition advisory completed for private family office; cantilevered limestone architecture with dual reflecting pools.',
  'Independent Estate',
  'assets/images/property-sandstone-pavilion.jpg',
  'The Sandstone Pavilion Villa in Whitefield, Bengaluru — a cantilevered limestone residence lit at dusk beside a reflecting pool.',
  1, 1, 1
),
(
  '#CHE-109',
  'Closed & Delivered',
  'Alwarpet, Chennai',
  'The Louvered Urban Penthouse',
  'Exclusive Seller Disposition',
  '4 BHK Floor-Through Residence · 4,800 sq.ft',
  'Off-market seller representation advised to structured deed closing; low-density vertical estate with biometric direct lift vestibule.',
  'Urban Duplex',
  'assets/images/property-louvered-penthouse.jpg',
  'The Louvered Urban Penthouse in Alwarpet, Chennai — a limestone facade with teak louvers and deep balconies at twilight.',
  1, 1, 2
),
(
  '#ECR-880',
  'Landmark Sale',
  'ECR, Chennai',
  'The Oceanfront Pavilion Estate',
  'Bespoke Transaction Advisory',
  '5 BHK Coastal Sanctuary · 20,000 sq.ft Plot',
  'Discreet coastal acquisition diligence, coastal clearance audits, and bespoke escrow closure for an overseas principal.',
  'Coastal Retreat',
  'assets/images/property-oceanfront-estate.jpg',
  'The Oceanfront Pavilion Estate on ECR, Chennai — a single-storey glass pavilion on a lawn overlooking the sea at dusk.',
  1, 1, 3
);

-- ----------------------------------------------------------------------------
-- Site Settings
-- ----------------------------------------------------------------------------
INSERT INTO `site_settings` (`key`, `value`) VALUES
('site_name',     'HS Properties'),
('site_tagline',  'Private Advisory'),
('advisor_phone', '+442079460892'),
('advisor_email', 'desk@hsproperties.com');
