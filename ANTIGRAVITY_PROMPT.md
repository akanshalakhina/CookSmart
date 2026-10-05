# PROMPT FOR ANTIGRAVITY — CookSmart Premium Food Website

> Copy everything below this line into Antigravity (Planning mode).

---

## 0. Your role and goal

You are a senior front-end + PHP developer and motion designer. Build a **multi-page, highly animated, premium corporate website** for **CookSmart®** (legal name: *Cooksmart Premium Food Private Limited*), an Indian food-processing company from West Bengal that sells spices (the **Sugrihini / সুগৃহিণী** range) and **Chakki Fresh Atta**.

The site is being built in **plain PHP** now and will be **migrated into a custom WordPress theme** later, so structure everything to make that migration trivial (see §11).

The client's #1 priority: **the theme, UI and animations must look stunning and premium**, styled after the CookSmart logo colours, combining two reference websites:

| Reference | What the client liked | What you must replicate |
|---|---|---|
| **https://ganeshconsumer.com** | The **centre logo header** | Thin coloured top strip with contact + social icons → white header bar with nav links split evenly **left and right of a centred logo**. The logo sits on a white badge that **hangs below** the header edge with a soft shadow. A rounded **"Shop now" pill button with a cart icon** at the far right. Below it, a full-width **emotional family banner slider** with real product packs composited over the photo, and circular prev/next arrows. |
| **https://rochakmasala.com** | The **animation style** | Brand preloader; hero heading + buttons **slide in from the left**; a **top-down spice plate that slowly rotates forever** (~50s per turn) beside the hero text; a collage of product packs in the hero; feature cards that **fade up with stagger** on scroll; images that **zoom in** on scroll; **number counters** that count up; product-category cards; floating **call / WhatsApp / social buttons** fixed on the right edge; a **back-to-top** button; a dark footer. Small script-font "eyebrow" labels above bold section headings. |

Open both sites in your browser first, study the header, hero and scroll animations, then build something that feels like **the best of both, but more polished and more modern** — in CookSmart's red / white / green identity.

**Before writing code, produce an Implementation Plan artifact** (file tree, design tokens, libraries with pinned versions, page list, animation list) and then build in phases (§13). Verify every page in your browser at desktop (1440px), tablet (768px) and mobile (375px) widths and attach screenshots to your walkthrough.

---

## 1. Hard rules (non-negotiable)

1. **NO MOCK DATA.** Use only the real content in this prompt and the files in this workspace. Specifically **do NOT add**:
   - lorem ipsum, invented testimonials/reviews/star ratings, invented customers or client logos
   - prices, discounts, "add to cart", stock levels (selling happens on an external shop — §6)
   - invented statistics, awards, certifications, FSSAI/ISO numbers, years, milestones
   - invented products, flavours or pack sizes not printed on the pack images
   - invented team members, stock photos of "our team", fake social-media URLs, blog/news posts, recipes
   - stock images hot-linked from Unsplash/Pexels/etc.
   If a section would need data we don't have, **leave the section out** instead of faking it. Put any value I still need to supply (e.g. Shop URL, social URLs) in `includes/config.php` as a clearly commented empty constant — never as fake data on the page.
2. **Multi-page architecture — NOT a one-page site.** Each nav item is its own PHP page with its own URL, `<title>` and meta description.
3. **PHP 8.x, no framework, no database, no build step.** Vanilla CSS (custom properties) + vanilla JS + a few CDN libraries. Must run on any shared hosting (Apache) and with `php -S`.
4. **WordPress-ready** structure (§11). Same URL slugs as the future WordPress pages.
5. **Responsive** from 360px to 1920px, no horizontal scroll at any width, 16px minimum side gutter on mobile.
6. **Accessible & fast**: semantic HTML, alt text, keyboard focus states, `prefers-reduced-motion` support, lazy-loaded images, WebP.
7. Correct the grammar/spelling of the brochure text (I've done it in §9 — use that version), but **never change its meaning or add new claims**.

---

## 2. Workspace inputs (already in this folder)

```
E:\Cook Smart\
├── Reference\
│   ├── logo-reference.jpg            ← CookSmart logo (see §3.4 — has a FAKE checkerboard background)
│   ├── company-profile-page-1.webp   ← company profile / policy document, page 1
│   └── company-profile-page-2.webp   ← company profile / policy document, page 2
└── Products Image\                   ← REAL product pack shots (white background, no alpha, ~2 MB each)
    ├── Mirchi Powder.png(100g).png   ← primary  (Mirchi, 100 g printed on pack)
    ├── Mirchi Powder.png             ← alternate view
    ├── Haldi Powder(100g).png        ← primary  (Haldi, 100 g)
    ├── Haldi Powder.jpeg             ← alternate view
    ├── Jeera Powder (50g).png        ← primary  (Jeera, 50 g)
    ├── Jeera Powder.png              ← alternate view
    ├── Dhania powder.png(50g).png    ← primary  (Dhania, 50 g)
    ├── Dhania powder.png             ← alternate view
    └── Atta Image.png                ← Chakki Fresh Atta, 5 kg
```

Build the website inside a new folder: **`E:\Cook Smart\cooksmart-website\`**. Never modify the original files in `Reference\` or `Products Image\` — copy and process them into `assets/`.

---

## 3. Brand system

### 3.1 Colours (sampled from the logo — use these exact values as CSS custom properties)

```css
:root{
  --cs-red:        #F20000; /* logo banner red — large brand surfaces, big headings, hero blocks */
  --cs-red-strong: #C3161A; /* buttons, links & small text on white (passes 4.5:1 with white text) */
  --cs-red-deep:   #A8161A; /* logo swoosh shadow — hover states, gradients */
  --cs-maroon:     #6E0B0E; /* footer / dark sections */
  --cs-green:      #319847; /* logo swoosh dark green */
  --cs-green-mid:  #64B741; /* logo swoosh mid green */
  --cs-lime:       #A8E13C; /* logo swoosh lime */
  --cs-white:      #FFFFFF;
  --cs-cream:      #FFF8F0; /* warm off-white section background */
  --cs-ink:        #1F1A17; /* body text */
  --cs-muted:      #6B605A;
  --cs-swoosh: linear-gradient(90deg, #319847 0%, #64B741 50%, #A8E13C 100%);
}
```

Product accent colours (sample exact values from each pack image and store them in the product data): Mirchi = red, Haldi = turmeric yellow, Jeera = cumin tan-brown, Dhania = olive-mustard, Atta = orange with blue.

Rules: white text on `--cs-red` only for large text (≥24px bold); use `--cs-red-strong` for buttons and small text. Green is an accent (swoosh lines, underlines, "Shop Now" pill, success states), not body text. Check every text/background pair for ≥4.5:1 contrast.

### 3.2 Typography (Google Fonts)
- Display headings: **Fraunces** (weights 700–900, use the `SOFT` axis at 100 for a friendly, chunky feel close to the logo wordmark)
- Body/UI: **DM Sans** (400/500/700)
- Script eyebrow labels (Rochak-style small label above headings, e.g. "About Us", "Our Products"): **Kaushan Script**
- Bengali (সুগৃহিণী): **Hind Siliguri**; Devanagari (product Hindi names): **Hind**
- Use `font-display: swap` and preconnect to fonts.gstatic.com.

### 3.3 Visual motifs
- The **logo swoosh** (dark-red + green→lime curve) is the signature motif: use it as section dividers (SVG waves), heading underlines that **draw in** on scroll (SVG stroke animation), the preloader, and the footer's curved top edge.
- The logo's **arched banner shape** (convex top and bottom edges) → use for the hanging logo badge, page-hero bottom edges and highlight cards.
- Subtle spice-texture/pattern backgrounds (SVG line-art of chillies, turmeric roots, cumin, coriander — like the line-art on the Sugrihini packs) at low opacity on cream sections.
- Generous whitespace, large rounded corners (16–24px), soft layered shadows, warm feel.

### 3.4 Logo — IMPORTANT
`Reference/logo-reference.jpg` is a JPG whose "transparent" checkerboard is **baked into the pixels** — do not place it on the site as-is.
1. If a file named `Reference/logo-original.svg` or `Reference/logo-original.png` (with real transparency) exists, use that.
2. Otherwise, create `assets/images/logo/cooksmart-logo.png` (with alpha) by removing the checkerboard around the logo's white outline (e.g. Python + Pillow flood-fill from the outer edges, then re-apply a clean white outline/stroke if the outline gets eaten). Inspect the result visually at 2× zoom; edges must be clean. Also export a 512×512 favicon/app icon and an OG image (1200×630, logo centred on red with the swoosh).
3. Add a note in README that the original vector logo from the packaging designer should replace the cut-out.
Never recolour, stretch or re-typeset the logo.

---

## 4. Global layout (every page)

### 4.1 Preloader (Rochak-style, but branded)
Cream/white screen; the logo fades/scales in while the green→lime swoosh **draws** beneath it, then the screen splits/wipes away (red curtain upward). Max 1.2s. Show the full version only on the first page view of a session (`sessionStorage`); on later navigations use the short page-transition curtain (4.4).

### 4.2 Header — Ganesh-style centred logo
- **Top strip** (≈40px, `--cs-red-strong` background, white text): left = phone `+91 98000 87155` (tel: link) and email `cooksmartkol@gmail.com` (mailto: link) with icons; right = social icons rendered **only for non-empty URLs** in `config.php`.
- **Main bar** (white, ≈84px, soft bottom shadow):
  - Left group: **Home · About Us · Products**
  - **Centre: logo** on a white arched badge that hangs ~28px below the bar with a soft shadow (exactly the Ganesh effect). Logo ~150px wide on desktop.
  - Right group: **Company Policy · Contact Us · [Shop Now 🛒]** — Shop Now is a rounded pill in the green→lime swoosh gradient with dark-green text and a cart icon; it opens `SHOP_URL` in a new tab (`target="_blank" rel="noopener"`), with a subtle shine sweep animation every few seconds and a lift + glow on hover.
- Nav links: animated underline in the swoosh gradient that grows from the centre on hover; active page shows the underline permanently.
- **On scroll**: top strip slides up, main bar becomes slightly translucent with `backdrop-filter: blur`, logo badge shrinks smoothly to ~100px. Hide header on fast scroll down, reveal on scroll up.
- **Mobile (<1024px)**: logo centred, hamburger on the right, Shop Now as a compact cart icon on the left. Hamburger opens a full-screen red overlay menu with links that stagger in from the left, contact details and Shop Now button at the bottom; animated hamburger → X.

### 4.3 Footer
- Curved top edge drawn as the logo swoosh (red + green wave SVG), then a deep maroon → `--cs-red-deep` gradient background with a faint spice line-art pattern.
- Columns: (1) logo on white badge + one-line brand statement: *"Quality is not expensive — it's priceless."* + Shop Now button; (2) Quick links (all pages); (3) Our Products (links to each product page); (4) Contact: both addresses, phone, email.
- Bottom bar: `© <?= date('Y') ?> Cooksmart Premium Food Private Limited. All rights reserved.` — CookSmart® is a registered trademark.
- Footer content fades up with stagger when it enters the viewport.

### 4.4 Page transitions (multi-page, so make navigation feel seamless)
- Use **cross-document View Transitions** (`@view-transition { navigation: auto; }`) for supporting browsers.
- Fallback: on internal link click, a red curtain with the small logo sweeps in, then the next page loads and the curtain sweeps out. Skip for external links, `tel:`, `mailto:`, new-tab links and when reduced motion is on.

### 4.5 Floating actions (Rochak-style)
Fixed vertical stack on the right edge, mid-height: **Call** (tel:), **WhatsApp** (`https://wa.me/919800087155` — only if `WHATSAPP_ENABLED` is true in config), **Email** (mailto:). Circular buttons that expand into a label pill on hover, gentle pulse on the call button. **Back-to-top** button bottom-right with a circular scroll-progress ring in the swoosh gradient.

### 4.6 Inner-page hero (About, Products, Product detail, Company Policy, Contact)
≈45vh red banner with a slow-moving spice-dust/particle background and line-art pattern, page title in Fraunces that reveals word-by-word (mask slide-up), breadcrumb (Home › Page) fading in after, and an arched/swoosh-shaped bottom edge. Each page also gets a relevant real product image or generated image (§7) floating on the right with a gentle bob.

---

## 5. Animation system

### 5.1 Libraries (CDN — pin exact latest stable versions; load with `defer`)
- **GSAP 3 + ScrollTrigger + SplitText + Flip** (all free) — every scroll/entrance animation
- **Swiper 11+** — home hero slider and product carousels
- **Lenis** — smooth scrolling (disable when reduced motion is on)
- Icons: inline SVG (Lucide-style) — no icon font

### 5.2 Declarative animation API (WordPress-friendly)
Write one `assets/js/animations.js` that reads data attributes so any template (and later any WP block) can animate without new JS:
```html
<h2 data-anim="split-up">…</h2>
<div data-anim="fade-up" data-delay="0.2">…</div>
<div data-anim-stagger="fade-up" data-stagger="0.12"> <article>…</article> <article>…</article> </div>
<img data-anim="zoom-in" …>
<span data-counter="90" data-prefix="₹" data-suffix=" Cr">0</span>
```
Supported: `fade-up`, `fade-in`, `slide-left` (enters from the left — Rochak's `slideInLeft`), `slide-right`, `zoom-in` (Rochak's `zoomIn`), `split-up` (word-mask reveal), `draw` (SVG stroke draw), `parallax` (with `data-speed`), `counter`. Defaults: 0.9s, `power3.out`, trigger at 85% viewport, play once.

### 5.3 Signature animations to implement
1. **Rotating spice plate** — the top-down spice plate image (§7) rotates continuously (`50s linear infinite`, CSS), with the 4 Sugrihini packs floating around it on staggered bobbing loops. Slight mouse-parallax tilt on desktop.
2. **Hero slide timeline** — on every slide change: eyebrow fades, H1 words slide in from the left, paragraph fades up, buttons slide in, packs pop up with a springy stagger; background has a slow Ken Burns zoom.
3. **Heading underline swoosh** that draws in under every section heading.
4. **Counters** count up with easing when visible.
5. **Product cards**: 3D tilt toward the cursor, pack lifts and casts a coloured glow in the product's accent colour, a soft blob behind the pack morphs, "View details →" slides up.
6. **Infinite marquee** of business segments (pauses on hover), with small spice icons between items.
7. **Spice particles**: lightweight canvas of drifting red/yellow/green specks in the home hero and inner-page heroes (max ~60 particles, paused when off-screen).
8. **Quote reveal**: the objective quote highlights word-by-word as the user scrolls (scrubbed ScrollTrigger).
9. **Products filter** uses GSAP Flip so cards rearrange smoothly.
10. **Magnetic buttons** on desktop (primary CTAs pull slightly toward the cursor).

### 5.4 Motion rules
- Animate only `transform` and `opacity`; no layout thrash; 60fps on mid-range Android.
- `@media (prefers-reduced-motion: reduce)`: disable rotation, parallax, particles, Lenis and page curtains; show content immediately.
- Content must be visible if JS fails (add a `js` class on `<html>` and only hide pre-animation states under `.js`).

---

## 6. Pages (each its own PHP file / URL)

| Nav | URL | File |
|---|---|---|
| Home | `/` | `index.php` |
| About Us | `/about-us/` | `about-us.php` |
| Products | `/products/` | `products.php` |
| (product detail) | `/products/{slug}/` | `product.php?slug=` |
| Company Policy | `/company-policy/` | `company-policy.php` |
| Contact Us | `/contact-us/` | `contact-us.php` |
| Shop Now | external `SHOP_URL` (new tab) | — |
| 404 | — | `404.php` |

### 6.1 HOME (`index.php`)

**A. Hero slider (Ganesh emotional banner + Rochak animation)** — full width, ~90vh desktop / auto on mobile, Swiper fade effect, autoplay 6.5s, pause on hover, custom circular arrows (white with red icon) and a progress-bar pagination in red/green.

- **Slide 1 — Emotional family banner (Ganesh-style).** Background: generated image A (§7). White-to-transparent gradient from the left for text legibility. Eyebrow (script): *Since 2015*. H1: **"Quality is not expensive — it's priceless."** Paragraph: *"From our own production to your family's kitchen — spices and atta made with an unwavering commitment to quality."* Buttons: **Explore Products** (red) → /products/, **Shop Now** (swoosh pill) → SHOP_URL. Bottom-right: the 4 real Sugrihini packs + Atta pack composited over the photo, popping up in a stagger then bobbing gently.
- **Slide 2 — Sugrihini spices (Rochak-style).** Deep red/maroon background with spice texture + particles. Left: eyebrow *Sugrihini — সুগৃহিণী*; H1: **"The taste of a good homemaker's kitchen."**; paragraph: *"Mirchi, Haldi, Jeera and Dhania — everyday spices, ready to use in convenient packs."*; a 2×2 collage of the 4 real packs below the text; button **View Spices**. Right: the **rotating spice plate** (generated image B).
- **Slide 3 — Chakki Fresh Atta.** Warm orange/cream background with drifting wheat-grain particles, generated image D (rotis + wheat) behind the large real Atta pack. H1: **"Chakki Fresh Atta — Natural & Fresh."** Paragraph: *"Natural whole wheat, hygienically packed. 100% natural — no additives, no preservatives."* Buttons: **View Atta**, **Shop Now**.

**B. Promise strip** — 4 cards overlapping the bottom of the hero (negative margin), fade-up stagger, icon in a red circle that flips on hover:
1. **Own Production** — "Everything we sell comes from our own production."
2. **Quality First** — "Our one and only objective: quality is not expensive — it's priceless."
3. **100% Satisfaction Guarantee** — "Printed on every Sugrihini pack."
4. **Convenient Packs & Sizes** — "Ready-to-use for the modern Indian kitchen."

**C. About teaser** — left: image collage (generated image C + a real pack) with `zoom-in`; a red "Since 2015" badge rotating slowly. Right: eyebrow *About Us*, heading **"Welcome to CookSmart"**, the intro paragraph (§9.1), and a counter row:
- **`<?= date('Y') - 2015 ?>+`** Years of Journey (computed, never hard-coded)
- **₹90 Cr** Turnover (approx.)
- **3** Core Categories (Spices, Pulses, Cereals)
- **9** Business Segments
Button **Read Our Story** → /about-us/.

**D. Heritage story band (emotional)** — full-width parallax with generated image C (hands grinding spices on a shil-nora). Overlay text (scroll-reveal): *"Decades ago, housewives ground their spices by hand at home and made their own blends."* → then: *"Times have changed. The care hasn't. That's the spirit of Sugrihini — সুগৃহিণী, the good homemaker."* (Sugrihini literally means "good homemaker" in Bengali — this is the emotional core of the brand.)

**E. Products showcase** — eyebrow *Our Products*, heading **"Made for Every Indian Kitchen"**. Swiper carousel (desktop shows 4) of all 5 real products using the product card (§5.3-5), plus filter chips *All / Spices / Atta*. Button **View All Products**.

**F. Objective quote** — red section with a large swoosh graphic; quote **"Quality is not expensive — it's priceless."** revealed word-by-word on scroll; attribution: *— Our one and only objective*.

**G. Where we serve** — eyebrow *Our Business*, heading **"Core Categories & Segments"**. Three large category tiles (Spices, Pulses, Cereals) with zoom-in images/illustrations, then the infinite **marquee** of segments: General Trade · Modern Trade · HORECA · Snacks & Convenience Foods · Soups, Sauces & Dressings · Bakery & Confectionery · Beverage · Others.

**H. Vision & Mission** — two cards side by side (slide-left / slide-right), text from §9.3/§9.4, link **Read Company Policy**.

**I. CTA band** — swoosh-gradient background: **"Bring CookSmart home today."** Buttons: **Shop Now** + **Contact Us**.

### 6.2 ABOUT US (`about-us.php`)
1. Inner hero: title **"About Us"**, breadcrumb.
2. **Our Story** — split layout: text §9.1 + §9.2 (slide-left), image collage (zoom-in).
3. **Leadership** — elegant card: *"Under the able guidance & leadership of"* **Mr Santanu Saha**. No photo yet → a red circular monogram "SS" with an animated swoosh ring (leave a clearly commented slot for a real photo). Do **not** invent a title, quote or biography.
4. **Our Journey** — a 2-point animated line (the line draws on scroll): **2015** — "Began our journey in the food processing sector" → **Today** — "Turnover of around ₹90 crore, with our own production." No other milestones.
5. **Counters** row (same as home).
6. **Our Philosophy** — §9.2 paragraphs as a large pull-quote style section.
7. **Brand family** — CookSmart® → **Sugrihini (সুগৃহিণী)** spices and **Chakki Fresh Atta**, each with real pack images, linking to products.
8. **Vision · Mission · Values** — three cards (fade-up stagger) with short versions + link to Company Policy.
9. **Our Offices** — two cards (Registered Office / Corporate Office) with map pin icons, linking to Contact Us.

### 6.3 PRODUCTS (`products.php`)
1. Inner hero: **"Our Products"**.
2. Intro line: *"Pure, ready-to-use spices and chakki-fresh atta — from our own production to your kitchen."*
3. Filter tabs: **All · Sugrihini Spices · Atta** (GSAP Flip rearrange, URL hash updates, works without JS by showing all).
4. Product grid (4 columns desktop / 2 tablet / 1–2 mobile) using the animated product card: pack image on a soft accent-coloured blob, English name, Hindi name in Devanagari, pack size chip, "View details →", and a small **Buy on our shop ↗** link (SHOP_URL).
5. Bottom CTA band.

### 6.4 PRODUCT DETAIL (`product.php`, clean URL `/products/{slug}/`)
- Inner hero in the product's accent colour (not always red).
- Left: large pack image with mouse-parallax and a thumbnail switcher (primary + alternate view where available); image zooms in on load.
- Right: range label (e.g. *Sugrihini — সুগৃহিণী*), product name (English) + Hindi name, net weight chip(s), claims as animated badges (only the claims listed in §8 for that product), a short neutral description (only what is in §8), buttons **Shop Now ↗** and **Enquire** (→ /contact-us/?product={slug}).
- "More from CookSmart" carousel with the other products.
- Unknown slug → 404 page with HTTP 404 status.

### 6.5 COMPANY POLICY (`company-policy.php`)
This page carries the **full company profile document** (§9), beautifully laid out — not a wall of text:
1. Inner hero: **"Company Policy"**.
2. Sticky left sub-navigation on desktop (scroll-spy highlights the current section): Our Objective · Who We Are · Vision · Mission · Values · Our Heritage · Industry Outlook · Our Business · Our Commitments.
3. **Our Objective** — huge quote with word-by-word reveal.
4. **Who We Are** — §9.1 + §9.2.
5. **Vision / Mission / Values** — three tall cards with the arched-banner shape, icon draw-in animation, full text from §9.3–§9.5.
6. **Our Heritage** — §9.6 with generated image C in a parallax frame and an animated "7,000 years" counter.
7. **Industry Outlook** — §9.7 split into two columns; highlight the words *turmeric, ginger and black pepper* with an animated marker underline.
8. **Our Business** — Core categories (3 tiles) + Core segments (9 icon chips, fade-up stagger) from §9.8.
9. **Our Commitments** — 3 numbered steps (01/02/03) from §9.9 connected by a line that draws on scroll.
10. Closing statement §9.10 on the red swoosh band.

### 6.6 CONTACT US (`contact-us.php`)
1. Inner hero: **"Contact Us"**.
2. Three info cards (fade-up stagger, icon bounce on hover): **Call Us** +91 98000 87155 (tel:), **Email Us** cooksmartkol@gmail.com (mailto:), **Company** Cooksmart Premium Food Private Limited.
3. Two address cards with a tab switcher and embedded Google Maps (no API key: `https://www.google.com/maps?q=<urlencoded address>&output=embed`, `loading="lazy"`):
   - **Registered Office:** 324, S.N. Mallick Road, P.O. + P.S. – Singur, Dist. – Hooghly, West Bengal, PIN 712409
   - **Corporate Office:** 142/1A, Sarat Bose Road, Kolkata, West Bengal 700029
4. **Enquiry form** — Name*, Phone*, Email*, Enquiry type (General Enquiry / Trade & Distribution — General Trade, Modern Trade / HORECA & Bulk / Product Feedback), Product (optional select from product data; pre-filled from `?product=`), Message*.
   - Floating labels, animated focus states, inline validation.
   - Submits via `fetch` to `contact-submit.php` (and also works without JS via normal POST + redirect).
   - Server side: validate & sanitise, CSRF token (session), honeypot field, simple per-session rate limit, send with PHP `mail()` to `CONTACT_TO_EMAIL` with `Reply-To` = visitor. When `DEV_MODE` is true, append the enquiry to `storage/enquiries.log` instead (protect `storage/` with `Require all denied` in `.htaccess`).
   - Animated success state (checkmark draw + confetti of tiny spice specks); friendly error state.

### 6.7 404 (`404.php`)
Friendly branded page: *"Looks like this page wandered out of the kitchen."* — rotating spice plate, buttons Home / Products.

---

## 7. Imagery

### 7.1 Real product images (from `Products Image\`)
- Copy into `assets/images/products/` with clean slugs, e.g. `mirchi-powder-100g.webp`, `mirchi-powder-alt.webp`, `haldi-powder-100g.webp`, `haldi-powder-alt.webp`, `jeera-powder-50g.webp`, `jeera-powder-alt.webp`, `dhania-powder-50g.webp`, `dhania-powder-alt.webp`, `chakki-fresh-atta-5kg.webp`.
- The originals have a **plain white background and no transparency**. Create transparent cut-outs (remove the white background, e.g. Python Pillow flood-fill from the corners with a small tolerance, or `rembg`), check edges visually, and export WebP (with alpha) at 800px and 400px heights plus a PNG fallback. Target < 150 KB each.
- Use `<picture>`/`srcset`, explicit `width`/`height`, `loading="lazy"` (except the first hero slide).

### 7.2 Generated images (use your image-generation tool)
These are atmosphere images only — **no text, no logos, no packaging in them** (the real packs are layered on top in HTML). Save to `assets/images/generated/`, WebP, ≤ 250 KB, and list them in README as "replace with a real photoshoot when available".
- **A — Family kitchen (hero slide 1):** "Warm cinematic photograph of a Bengali mother in a red-and-white cotton saree (laal paar) cooking with her young daughter in a sunlit Indian home kitchen, steam rising from a kadai, small bowls of red chilli, turmeric, cumin and coriander powder on the counter, golden-hour window light, shallow depth of field, empty space on the left third for text, photorealistic, 16:9, no text, no logos."
- **B — Spice plate (rotating):** "Perfectly top-down photograph of a round white ceramic plate holding four small white bowls of red chilli powder, turmeric powder, cumin powder and coriander powder, with whole dried red chillies, turmeric roots, cumin seeds, coriander seeds and fresh coriander leaves arranged between the bowls, centred, isolated on a plain background, soft studio light, square 1:1, no text." → then cut out the circular plate to a transparent WebP.
- **C — Heritage:** "Close-up of a woman's hands grinding turmeric and red chillies on a traditional Bengali stone grinder (shil-nora), rustic warm light, earthy textures, 16:9, no text."
- **D — Atta:** "Stack of soft fresh rotis on a brass plate beside a heap of whole wheat grains and golden wheat stalks, warm kitchen light, empty space on the left, 16:9, no text."
If image generation is unavailable, use CSS/SVG compositions (gradients, spice line-art, particles) instead — **never** hot-link stock photos.

### 7.3 Illustrations
Create a small SVG set in the pack's line-art style: chilli, turmeric root, cumin seeds, coriander seeds + leaf, wheat stalk, grinding stone, plus icons for the promise strip and segments. Single-colour, inherit `currentColor`.

---

## 8. Product data (`data/products.php` — the ONLY source for product content)

Return an array; each product: `slug, name, name_hi, range, range_bn, category, net_weights[], images{primary, alt}, accent, claims[], description`.

| slug | name | name_hi | range | category | net weight (as printed) | claims (only these) |
|---|---|---|---|---|---|---|
| `mirchi-powder` | Mirchi Powder | लाल मिर्च पाउडर | Sugrihini (সুগৃহিণী) | Spices | 100 g | 100% Satisfaction Guarantee · Vegetarian |
| `haldi-powder` | Haldi Powder | हल्दी पाउडर | Sugrihini (সুগৃহিণী) | Spices | 100 g | 100% Satisfaction Guarantee · Vegetarian |
| `jeera-powder` | Jeera Powder | जीरा पाउडर | Sugrihini (সুগৃহিণী) | Spices | 50 g | 100% Satisfaction Guarantee · Vegetarian |
| `dhania-powder` | Dhania Powder | धनिया पाउडर | Sugrihini (সুগৃহিণী) | Spices | 50 g | 100% Satisfaction Guarantee · Vegetarian |
| `chakki-fresh-atta` | Chakki Fresh Atta | आटा | CookSmart Chakki Fresh | Atta | 5 kg | Premium Quality · Natural & Fresh · 100% Natural · No Additives · No Preservatives · Natural Whole Wheat · Hygienically Packed |

Descriptions — short, factual, no new claims:
- Spices: "Sugrihini {Name} from CookSmart — a ready-to-use kitchen essential, packed in a convenient {weight} pack."
- Atta: "CookSmart Chakki Fresh Atta — premium quality natural whole wheat atta, hygienically packed for your healthy life. 100% natural, with no additives and no preservatives."

Add a code comment: *"Add more pack sizes/products here — only values printed on the actual packs."*

---

## 9. Company content (cleaned from the company profile — use verbatim)

**9.1 Introduction.** CookSmart Premium Food started its journey in 2015 in the food processing sector, under the able guidance and leadership of Mr Santanu Saha. Since then there has been no looking back — our current turnover touches around ₹90 crore, with our own production.

**9.2 Philosophy.** With the changing business scenario and competition, there is a revolutionary change in consumers' perception of buying habits and needs. CookSmart believes in the internal power of creativity in moulding minds — combining this power with a proper understanding of market dynamics, and engineering a creative strategy that not only helps us consolidate but also puts us way ahead of the competition. Our endless journey towards ultimate quality and excellence, for better consumer satisfaction, inspires us to keep improving on our present best.

**Objective.** *"Quality is not expensive — it's priceless."* — our one and only objective.

**9.3 Vision.** An outstanding company with innovative brands and exceptional people that together really make a difference.

**9.4 Mission.** Be it machinery, technology or people, CookSmart believes in the spirit of excellence, with an unwavering commitment to quality in every sphere — so that the society in which we live and work regards us with belief and confidence.

**9.5 Values.** Our core values are our core beliefs on how we want to run the business, and an integral part of how we do business. They help the business deliver results.
(For visual value chips you may use only these words, which come straight from the document: *Quality · Excellence · Creativity · Innovation · Consumer Satisfaction · Trust*.)

**9.6 Heritage.** The history of Indian spices is probably as old as human civilisation itself, dating back some 7,000 years. Decades ago, housewives ground their spices by hand at home and made their own blends. With changing times, socio-economic conditions and other factors, this is giving way to ready-to-use blends and fusions in convenient packs and sizes — be it spices, grains, oils, pulses or groceries — all of this coupled with a growing population.

**9.7 Industry outlook.** The food and beverage industry in India is driven by changing consumer preferences, increasing disposable incomes, urbanisation and a diverse culinary landscape. In addition, the increasing demand for processed and convenience foods is leading to higher consumption of spices in various food products, influencing market growth. Indian spices are essential in enhancing the aroma, flavour and sensory appeal of food, and are widely used as natural seasonings and colourants in cuisines ranging from traditional Indian dishes to international delicacies — another major growth-inducing factor.
Besides this, the food processing industry relies heavily on spices to add distinct tastes to snacks and new food products, catering to the evolving palate of consumers. The rising popularity of health and wellness foods is further accelerating demand for spices with medicinal properties, such as turmeric, ginger and black pepper, contributing to their increased use in functional food and nutraceutical products.

**9.8 Our business.**
- Core business categories: **Spices, Pulses, Cereals**, etc.
- Core business segments: **General Trade, Modern Trade, HORECA, Snacks & Convenience Foods, Soups, Sauces & Dressings, Bakery & Confectionery, Beverage, Others.**

**9.9 Our commitments.**
1. Achieving retail distribution levels and in-store presence based on our objectives.
2. Accurately setting and delivering volume objectives consistent with consumer demand.
3. Building organisational capacity to sustain business expansion and growth.

**9.10 Closing.** What we are offering is real choice in defining an organisation that has purpose and meaning for all.

**9.11 Contact details** (store in `includes/config.php`, render from there everywhere):
- Company: Cooksmart Premium Food Private Limited
- Registered Office: 324, S.N. Mallick Road, P.O. + P.S. – Singur, Dist. – Hooghly, West Bengal, PIN 712409
- Corporate Office: 142/1A, Sarat Bose Road, Kolkata, West Bengal 700029
- Mobile: +91 98000 87155
- Email: cooksmartkol@gmail.com

Put all page copy in `data/content.php` (keyed by page/section) so the client can edit text without touching templates.

---

## 10. File structure

```
cooksmart-website/
├── index.php  about-us.php  products.php  product.php
├── company-policy.php  contact-us.php  contact-submit.php  404.php
├── sitemap.php            ← outputs sitemap.xml dynamically (pages + product slugs)
├── robots.txt
├── .htaccess              ← clean URLs, 404, gzip/brotli, cache headers, security headers
├── router.php             ← DEV ONLY: emulates the .htaccess rewrites for `php -S`
├── includes/
│   ├── config.php         ← SITE_URL, SHOP_URL (empty, I will fill), CONTACT_TO_EMAIL, PHONE,
│   │                         ADDRESSES, SOCIAL_LINKS (empty array), WHATSAPP_ENABLED, DEV_MODE
│   ├── functions.php      ← e(), url(), asset() with filemtime cache-busting, is_active(),
│   │                         render_partial(), get_product(), page_meta()
│   ├── header.php  footer.php
│   └── partials/          ← preloader, top-strip, nav, page-hero, section-heading,
│                             product-card, cta-band, floating-actions, counters
├── data/   products.php  content.php
├── storage/ (.htaccess: Require all denied)
├── assets/
│   ├── css/  tokens.css  base.css  layout.css  components.css  animations.css  pages/*.css
│   ├── js/   main.js  animations.js  hero-slider.js  products.js  contact.js  particles.js
│   ├── images/ logo/  products/  generated/  icons/  patterns/
│   └── fonts/ (optional self-hosted)
└── README.md
```

Every page follows the same pattern so it maps to a WordPress template:
```php
<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';
$page = ['slug' => 'about-us', 'title' => 'About Us', 'description' => '…'];
require __DIR__ . '/includes/header.php';
?>
<main id="main"> … sections … </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
```
Load CSS/JS only through helper functions (one place), so they become `wp_enqueue_style()` / `wp_enqueue_script()` later.

---

## 11. WordPress migration readiness

- URL slugs must equal the future WordPress page slugs: `/about-us/`, `/products/`, `/products/{slug}/`, `/company-policy/`, `/contact-us/`.
- Keep `header.php` / `footer.php` self-contained so `wp_head()`, `wp_footer()`, `wp_nav_menu()` and `body_class()` can drop in.
- Product data shape = a future custom post type **`cs_product`** (not `product`, to avoid clashing with WooCommerce) with ACF-style fields.
- `config.php` values = future Customizer/ACF Options fields.
- No inline `<script>` blocks with page logic — JS lives in files and reads `data-*` attributes.
- In README include a **mapping table** (current file → WordPress template file, e.g. `index.php → front-page.php`, `about-us.php → page-about-us.php`, `products.php → archive-cs_product.php`, `product.php → single-cs_product.php`, `contact-submit.php → admin-post.php handler or a form plugin`) and step-by-step migration notes.

---

## 12. SEO, performance, accessibility

- Unique `<title>` and meta description per page; canonical URLs; Open Graph + Twitter cards (OG image from §3.4).
- JSON-LD: `Organization` (name, logo, email, phone) with both `PostalAddress`es; `Product` schema on product pages **without price/offers/ratings**; `BreadcrumbList` on inner pages.
- One `<h1>` per page, logical heading order, skip-to-content link, visible focus rings (swoosh-green outline), `aria-label`s on icon buttons, sliders keyboard-accessible with pause control.
- Lighthouse (mobile) targets: Performance ≥ 85, Accessibility ≥ 95, Best Practices ≥ 95, SEO ≥ 95. Preload the first hero image and the display font; defer all JS; no layout shift from fonts or images.
- `lang="en"`; wrap Bengali/Hindi text in `lang="bn"` / `lang="hi"` spans.

---

## 13. Build phases (show me progress after each)

1. **Plan** — implementation plan artifact + design tokens.
2. **Assets** — process product images (cut-outs, WebP), logo cut-out, favicon/OG image, generate images A–D, SVG illustrations.
3. **Global shell** — header (centred logo, scroll behaviour, mobile menu), footer, preloader, page transition, floating actions, back-to-top, animation engine. Screenshot desktop + mobile.
4. **Home page** — all sections A–I with animations.
5. **Products + product detail**.
6. **About Us**, **Company Policy**, **Contact Us** (with working form + dev log), **404**.
7. **Polish & QA** — cross-browser (Chrome, Edge, Firefox, Safari/iOS behaviour), 360/375/768/1024/1440/1920 widths, reduced-motion mode, JS-disabled check, Lighthouse report, fix all console errors.
8. **README** — how to run locally, how to deploy to Apache shared hosting, what to fill in `config.php`, WordPress migration map, list of generated images to replace.

### Running locally
If PHP is not installed, install it (e.g. `winget install PHP.PHP.8.3`, or use XAMPP), then from `cooksmart-website/` run:
```
php -S localhost:8000 router.php
```
and verify every page at `http://localhost:8000/` in your browser.

### Definition of done
- [ ] 6 separate pages + product detail pages + 404, all with clean URLs working locally (router.php) and on Apache (.htaccess)
- [ ] Centred hanging-logo header exactly in the Ganesh style, with working scroll shrink and mobile menu
- [ ] Rochak-style motion everywhere: slide-in-left hero text, fade-up staggered cards, zoom-in images, rotating spice plate, counters, floating action buttons, back-to-top
- [ ] Brand colours from §3.1 only; logo never distorted
- [ ] Zero mock/placeholder content visible on any page; every fact traceable to this prompt
- [ ] Shop Now opens `SHOP_URL` in a new tab from header, mobile menu, hero, product pages and CTA bands
- [ ] Contact form validated client + server side, CSRF + honeypot, success/error states, logs in DEV_MODE
- [ ] Reduced-motion and no-JS modes fully usable
- [ ] Lighthouse targets met; no console errors; no horizontal scroll at any width
