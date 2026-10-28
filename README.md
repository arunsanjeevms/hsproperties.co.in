# HS Properties — Consultation Platform

Frontend for the HS Properties private real estate advisory platform.

Built from the Google Stitch project *HS Properties Consultation Platform*,
which is the visual source of truth for the design system.

## Stack

HTML5, CSS3, vanilla JavaScript, and Web3Forms (GitHub Pages static hosting).
Zero backend dependencies — client inquiries are securely routed directly via Web3Forms.


## Pages

| Route | Screen |
| --- | --- |
| `/index.php` | Home — Architectural Editorial Edition |
| `/pages/seller-intake-step-1.php` | Private Seller Intake, Step 01: Property |
| `/pages/seller-intake-step-2.php` | Private Seller Intake, Step 02: Details & Documents |
| `/pages/seller-intake-step-3.php` | Private Seller Intake, Step 03: Review & Submit |
| `/pages/seller-intake-step-3.php?state=submitted` | Advisory confirmation |

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

Home page and the three seller intake steps are complete and responsive
(desktop, laptop, tablet, mobile), with `prefers-reduced-motion` honoured.

Not yet built: buyer intake, admin requests inbox, request detail, dashboard,
and the MySQL layer.
