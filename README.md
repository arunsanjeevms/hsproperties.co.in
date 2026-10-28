# HS Properties — Private Real Estate Advisory Platform

A luxury, editorial-grade static web platform for private real estate consultation, optimized for **GitHub Pages** with zero backend dependencies.

Inquiries are securely and privately routed directly to email via **Web3Forms**.

---

## Architecture & Stack

- **Pure Static Frontend**: HTML5, CSS3, vanilla JavaScript.
- **Serverless Form Delivery**: [Web3Forms](https://web3forms.com) API (Zero backend, zero database, direct email alerts).
- **Hosting Ready**: GitHub Pages (or any CDN / static host like Netlify, Vercel, Cloudflare Pages).
- **Emblem / Logo**: Custom golden HS architectural monogram logo.

---

## Structure

```
├── .nojekyll                          # Direct static serving on GitHub Pages
├── index.html                         # Home page with integrated Buy & Sell inquiry modals
├── sell.html                          # Standalone Private Seller Intake page
├── pages/
│   └── seller-intake-step-1.html      # Alternative subfolder intake page
├── assets/
│   ├── icons/sprite.svg               # Inline SVG icon sprite
│   └── images/
│       ├── logo.svg                   # Brand vector logo
│       ├── logo.png                   # High-res PNG logo
│       ├── logo-icon.png              # Monogram emblem icon
│       └── hero-villa.jpg             # Architectural photography
├── css/
│   ├── variables.css                  # Design tokens (colors, typography, spacing)
│   ├── global.css                     # Reset, base typography, layout
│   ├── components.css                 # Buttons, modals, forms, cards
│   ├── pages.css                      # Home section layouts
│   ├── intake.css                     # Dedicated intake layouts
│   └── responsive.css                 # Breakpoint rules
└── js/
    ├── navigation.js                  # Header scroll & drawer navigation
    ├── interactions.js                # Micro-interactions & animations
    └── main.js                        # Smooth scrolling & initialization
```

---

## Client Inquiry Forms (Web3Forms)

Both forms submit asynchronously with instant feedback and zero page reloads:

### 1. Buy ("Find Your Property")
- **Full Name** — Required
- **Phone Number** — Required
- **Email Address** — Optional

### 2. Sell ("Sell Your Property")
- **Full Name** — Required
- **Phone Number** — Required
- **Property Type** — Required dropdown:
  - Villa / Gated Luxury Enclave
  - Apartment / Sky Penthouse
  - Independent House / Standalone Bungalow
  - Plot / Land Development Parcel
  - Commercial / Prime Office / Retail
  - Estate / Farmhouse / Country Acreage
  - Other (Please specify)
- **Dynamic "Other" Input**: Automatically appears when "Other" is chosen so the client can specify their custom property type.

---

## Configuring Web3Forms

1. Obtain a free access key at [https://web3forms.com](https://web3forms.com) (sent instantly to your email).
2. Open `index.html` and `sell.html`.
3. Replace `00000000-0000-0000-0000-000000000000` with your access key:
   ```html
   <input type="hidden" name="access_key" value="YOUR_ACTUAL_ACCESS_KEY_HERE">
   ```

---

## GitHub Pages Setup

1. In your GitHub repository, navigate to **Settings** &rarr; **Pages**.
2. Under **Build and deployment**:
   - **Source**: Deploy from a branch
   - **Branch**: `main` (or your deployment branch)
   - **Folder**: `/ (root)`
3. Click **Save**. Your site will be live immediately!
