# CookSmart® website

Multi-page PHP website for **Cooksmart Premium Food Private Limited**: Home, About Us, Products (with a page for each product), Company Policy and Contact Us. Shop Now links to an external shop.

- Plain PHP 8 with no framework, no database and no build step. It runs on any Apache shared hosting.
- Animations use GSAP 3 + ScrollTrigger from a CDN. They honour *reduced motion*, and the content still shows if JavaScript fails.
- The folder layout follows a WordPress theme, so moving it into WordPress later is mostly copying files (see the end of this file).

---

## 1. Fill these in before going live — `includes/config.php`

| Setting | What to put |
|---|---|
| `SHOP_URL` | Full link of the online shop. Until it is set, every Shop Now button shows a "coming soon" notice. |
| `SITE_URL` | Your domain, e.g. `https://www.cooksmart.in` (used for SEO links and the sitemap). |
| `SOCIAL_LINKS` | Facebook / Instagram / YouTube / LinkedIn / X URLs. Empty ones are hidden. |
| `WHATSAPP_ENABLED` | Set to `true` once +91 98000 87155 is confirmed on WhatsApp. This adds a floating WhatsApp button. |
| `CONTACT_TO_EMAIL` | Inbox that receives enquiry-form emails. It is `cooksmartkol@gmail.com` now. |

Also update `robots.txt` with the real sitemap URL.

## 2. Images to replace when real ones are available

| File | Notes |
|---|---|
| `assets/images/logo/cooksmart-logo.png` (+ `.webp`) | Cut out from a photo of the logo. **Ask the packaging designer for the original vector logo (SVG/AI/PDF)** and replace it. |
| `assets/images/hero/family-kitchen.webp` | *Optional.* Add a warm family/kitchen photo (about 1600×900) and home slide 1 switches automatically to a photo banner, like Ganesh Consumer's. |
| About page leadership card | Shows an "SS" monogram. A comment in `about-us.php` shows where to add a real photo. |
| `assets/images/products/*.webp` | Transparent cut-outs of the supplied pack shots. Add new products in `data/products.php` and drop `{name}.webp` + `{name}-sm.webp` here. |

## 3. Editing content

- **Products** → `data/products.php`. This is the only source of product data; list only what is printed on the packs.
- **Company text** (vision, mission, heritage, segments, commitments) → `data/company.php`.
- **Contact details & addresses** → `includes/config.php`.

## 4. Run it on this computer

PHP 8.3 is installed (via winget; open a new terminal so `php` is on the PATH). From this folder run:

```bash
php -S localhost:8000 router.php
```

Then open http://localhost:8000. `router.php` copies the `.htaccess` clean URLs for PHP's built-in server; Apache doesn't use it.
On localhost the enquiry form **writes to `storage/enquiries.log` instead of sending email**.

## 5. Put it on hosting (Apache / cPanel / Hostinger etc.)

1. Upload everything inside `cooksmart-website/` to `public_html/` (keep `.htaccess`).
2. Edit `includes/config.php` (section 1).
3. Check that PHP 8.1+ is selected and `mod_rewrite` is on (it is by default on almost all hosts).
4. Send a test enquiry. If email does not arrive, the host's PHP `mail()` is blocked; in that case use SMTP (e.g. PHPMailer) or move to WordPress with a form plugin.

## 6. Moving to WordPress

The page URLs already match the WordPress page slugs, so links and SEO carry over.

| Current file | WordPress theme file |
|---|---|
| `includes/header.php` | `header.php` (add `wp_head()`, `body_class()`, `wp_nav_menu()`) |
| `includes/footer.php` | `footer.php` (add `wp_footer()`) |
| `includes/functions.php` | `functions.php` (turn `asset()`/`enqueue_script()` into `wp_enqueue_style/script`) |
| `includes/partials/*` | `template-parts/*` (`get_template_part()`) |
| `index.php` | `front-page.php` |
| `about-us.php` | `page-about-us.php` |
| `products.php` | `archive-cs_product.php` |
| `product.php` | `single-cs_product.php` |
| `company-policy.php` | `page-company-policy.php` |
| `contact-us.php` + `contact-submit.php` | `page-contact-us.php` + an `admin-post.php` handler, or Contact Form 7 / WPForms |
| `404.php` | `404.php` |
| `data/products.php` | Custom post type **`cs_product`** + ACF fields (`cs_` prefix avoids clashing with WooCommerce) |
| `data/company.php`, `includes/config.php` | ACF Options page / Customizer |
| `assets/` | `assets/` (unchanged) |

All animations come from `data-anim` attributes, so content blocks keep animating in WordPress without extra JavaScript.

> **WordPress.com note:** uploading a custom theme needs the **Business plan or higher**. A normal host with WordPress installed (Hostinger, Bluehost, etc.) has no such limit.

## 7. Folder map

```
cooksmart-website/
├── index.php, about-us.php, products.php, product.php,
│   company-policy.php, contact-us.php, contact-submit.php, 404.php, sitemap.php
├── .htaccess · router.php (dev only) · robots.txt
├── includes/   config.php · functions.php · header.php · footer.php · partials/
├── data/       products.php · company.php
├── storage/    enquiry log in dev (blocked from the web)
└── assets/
    ├── css/    main.css (global) · pages.css (sections)
    ├── js/     animations.js · main.js · hero.js · carousel.js · products.js · product.js · policy.js · contact.js
    └── images/ logo/ · products/ · spice-plate.svg · pattern-*.svg · og-image.jpg
```
