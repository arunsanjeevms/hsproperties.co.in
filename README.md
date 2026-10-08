# HS Properties — Consultation Platform

Frontend for the HS Properties private real estate platform.

Built from the Google Stitch project *HS Properties Consultation Platform*,
which is the visual source of truth for the design system.

## Stack

HTML, CSS, vanilla JavaScript, PHP. MySQL is planned but **not yet
implemented** — the PHP here is templating only (includes plus presentation
data). No framework, no build step, no dependencies.

## Pages

| Route | Screen |
| --- | --- |
| `/index.php` | Home — Architectural Editorial Edition |
| `/pages/seller-intake-step-1.php` | Private Seller Intake, Step 01: Property |
| `/pages/seller-intake-step-2.php` | Private Seller Intake, Step 02: Details & Documents |
| `/pages/seller-intake-step-3.php` | Private Seller Intake, Step 03: Review & Submit |
| `/pages/buyer-intake-step-1.php` … `-3.php` | Private Buyer Intake, three steps |
| `/pages/intake-submitted.php?flow=seller` | Submission confirmation |
| `/pages/admin/dashboard.php` | Admin — Operations Center |
| `/pages/admin/requests.php` | Admin — Customer request inbox |
| `/pages/admin/request-detail.php` | Admin — One request |
| `/pages/admin/properties.php` | Admin — Property registry (status changed in place) |
| `/pages/admin/property-detail.php` | Admin — One property |
| `/pages/admin/property-new.php` | Admin — Add a property |
| `/pages/admin/matches.php` | Admin — Who has been paired with what (reading only) |
| `/pages/admin/appointments.php` | Admin — The diary |
| `/pages/admin/appointment-new.php` | Admin — Book an appointment |
| `/pages/admin/appointment-detail.php` | Admin — One appointment |

## Structure

```
assets/
  icons/sprite.svg      Inline SVG icon set (no icon font)
  images/               Photography and the logo
css/
  variables.css         Design tokens — the single source of truth
  global.css            Reset, typography, layout, accessibility, motion
  components.css        Reusable components (buttons, nav, cards, forms)
  pages.css             Home page section layouts
  intake.css            Intake shell + form system (shared by all intakes)
  responsive.css        Breakpoint behaviour
includes/
  config.php            Config, helpers, named route table
  data.php              Home page presentation data
  seller-intake-data.php  Intake option sets (future lookup tables)
  form-helpers.php      Form control renderers
  header/footer/navigation.php     Marketing shell
  intake-header/-progress/-footer.php  Intake shell
js/
  main.js, navigation.js, interactions.js, intake.js
pages/
index.php
```

## Conventions

- **Tokens first.** Colour, type, spacing and motion values live in
  `variables.css`. Do not hard-code them in page-level CSS.
- **Components over one-offs.** New screens compose from `components.css` and
  `intake.css` rather than adding bespoke rules.
- **Routes are named.** Cross-page links go through `route()` in `config.php`,
  so moving pages or adding pretty URLs is a one-line change.
- **Data is shaped like the database.** Arrays in `data.php` and
  `seller-intake-data.php` mirror the rows that will replace them, so the
  templates will not change when MySQL lands.
- **Progressive enhancement.** Form controls are real inputs styled with CSS
  `:has(:checked)`; every page works with JavaScript disabled.
- **Escape everything.** Dynamic output goes through `e()`.

## Running locally

Any PHP 8 server works. With the built-in server from the project root:

```bash
php -S 127.0.0.1:8088 -t .
```

Then open <http://127.0.0.1:8088/index.php>.

## Status

The marketing home page, both intake flows and the admin console listed above
are complete and responsive (desktop, laptop, tablet, mobile), with
`prefers-reduced-motion` honoured.

Three things are written to disk rather than held in a PHP array, as a
stand-in until MySQL lands — see `includes/admin-store.php` and
`includes/appointment-store.php`:

| File | Holds |
| --- | --- |
| `data/properties.json` | properties added through the console |
| `data/property-status.json` | manual status changes, keyed by property code |
| `data/appointments.json` | appointments, keyed by reference |

Each is written under an exclusive lock, and a damaged or missing file degrades
to the seeded content rather than taking the console down.

Not yet built: the MySQL layer, and authentication. Until there is a login,
the console's forms carry no CSRF token and anyone who can reach the URL can
post to them.
