# CookSmart® website

Multi-page PHP website for **Cooksmart Premium Food Private Limited**: Home, About Us, Products (with a page for each product), Company Policy, Career and Contact Us. Shop Now links to an external shop.

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
| `WHATSAPP_ENABLED` | Set to `true` once +91 70036 94825 is confirmed on WhatsApp. This adds a floating WhatsApp button. |
| `CONTACT_TO_EMAIL` | Inbox that receives enquiry-form emails. It is `cooksmartkol@gmail.com` now. |

Also update `robots.txt` with the real sitemap URL.

## 2. Images to replace when real ones are available

| File | Notes |
|---|---|
| `assets/images/logo/cooksmart-logo.png` (+ `.webp`) | Cut out from a photo of the logo. **Ask the packaging designer for the original vector logo (SVG/AI/PDF)** and replace it. |
| `assets/images/hero/family-kitchen.webp` | *Optional.* Add a warm family/kitchen photo (about 1600×900) and home slide 1 switches automatically to a photo banner, like Ganesh Consumer's. |
| About page leadership card | Shows an "SS" monogram. A comment in `about-us.php` shows where to add a real photo. |
| `assets/images/products/*.webp` | Transparent cut-outs of the supplied pack shots, one front and one back per pack size (see section 3). The 50 g / 1 kg weight labels and the back views were produced from the supplied artwork — swap in real packaging renders when available. |

## 3. Editing content

- **Products** → `data/products.php`. This is the only source of product data; list only what is printed on the packs.
  Every product has a `packs` list (for example Haldi Powder: 100 g and 50 g; Chakki Fresh Atta: 5 kg and 1 kg). On the product page the
  pack you pick stays in colour and the other sizes turn grey, and a Front / Back switch shows the back of the pack. Each pack needs four images in
  `assets/images/products/`: `{slug}-{pack}-front.webp`, `{slug}-{pack}-front-sm.webp`, `{slug}-{pack}-back.webp`, `{slug}-{pack}-back-sm.webp`
  (900 px and 420 px tall). The front and back images are built from the printer's flat artwork (centre panel = front; the two side panels =
  back), so they carry the real FSSAI number, barcode, QR code and nutrition table. **Whole Yellow Mustard** has no printer artwork yet — its
  images are still the earlier stand-ins; send its flat artwork and rebuild. Spelling slips in the print files ("Protin", "Corporate O ice")
  are corrected on the website images only.
  `price_pack` (₹5) switches on the highlighted **"Available at ₹5"** line in the description; leave it `null` for products without that pack.
- **Product categories** (Ground Spices, Whole Spices, Atta) → `product_categories()` in `includes/functions.php`. The Products page, footer and enquiry form group products by these.
- **Company text** (vision, mission, heritage, segments, commitments) → `data/company.php`.
- **Contact details & addresses** → `includes/config.php`.

## 4. Run it on this computer

PHP 8.3 is installed (via winget; open a new terminal so `php` is on the PATH). From this folder run:

```bash
php -S localhost:8000 router.php
```

Then open http://localhost:8000. `router.php` copies the `.htaccess` clean URLs for PHP's built-in server; Apache doesn't use it.
On localhost the enquiry form **writes to `storage/enquiries.log` instead of sending email**.

## 4b. Career page and owner area

- `/career/` lists the job openings; each one has an **Apply Now** button (opens the contact form with the job filled in) and a WhatsApp button.
- `/admin/` is the owner area (not linked from the site; the owner types the address). Sign in with a **Login ID and password** to post, edit,
  close/reopen or delete openings; changes appear on the Career page immediately.
- The login lives in `storage/admin.json` (password stored hashed). It is **not in git on purpose**, so upload `storage/admin.json` together with
  the site. If that file is missing, the first person to open `/admin/` is asked to create a Login ID and password — so never leave a live site without it.
- Openings are saved in `storage/jobs.json`. `storage/` is blocked from the web and must be **writable** by PHP (on Hostinger this is the default;
  permissions 755/644). When you re-upload the site, do not overwrite `storage/admin.json` or `storage/jobs.json`; back them up if you move the site.
- The owner can change the password from the owner area (**Change password**). Forgot it? Delete `storage/admin.json` and open `/admin/` again to
  create a new login (your job openings are kept).
- To demo it on a computer: run `php -S localhost:8000 router.php` in this folder and open http://localhost:8000/admin/.
- Wrong passwords are limited to 5 tries per 15 minutes. The owner area is excluded from search engines (`robots.txt` + `noindex`).

## 4c. The owner area on the Vercel design preview (demo)

Vercel cannot run PHP, so `/admin/` on the preview link is a **practice copy** (clearly marked *Demo*) built for showing the owner how posting works.
It lives only in `preview/` and `includes/demo-admin.php` and is added to `dist/` by `php build-static.php`; the live PHP site never serves it.

- Demo login (not the real one): **Login ID `demo` · Password `cooksmart-demo`**. It is only a gate for the demo — anyone can read it in the page source.
- Jobs posted in the demo are saved in that visitor's own browser (localStorage) and appear on the preview's Career page for that browser only.
  Nobody else sees them. The real owner area (`admin.php`) saves jobs on the server for everyone.
- `preview/preview-admin.js` = the demo owner area · `preview/preview-careers.js` = shows the demo jobs on the Career page and fills the contact form
  from "Apply Now". To change the demo password, put the SHA-256 of the new one in `DEMO_HASH` in `preview/preview-admin.js` and rebuild.

## 5. Put it on hosting (Apache / cPanel / Hostinger etc.)

1. Upload everything inside `cooksmart-website/` to `public_html/` (keep `.htaccess`).
2. Edit `includes/config.php` (section 1).
3. Check that PHP 8.1+ is selected and `mod_rewrite` is on (it is by default on almost all hosts).
4. Send a test enquiry. If email does not arrive, the host's PHP `mail()` is blocked; in that case use SMTP (e.g. PHPMailer) or move to WordPress with a form plugin.

## 5b. Design preview on Vercel (no PHP needed)

Vercel can't run this PHP site, but it can host a static HTML copy so you can show the design.

The repo is connected to Vercel through GitHub: the root `vercel.json` tells Vercel to serve `cooksmart-website/dist/`, which holds plain HTML.

1. After any design change, rebuild `dist/` from this folder (no need to start a server first):

   ```bash
   php build-static.php
   ```

2. Commit and push. Vercel redeploys automatically from GitHub.
3. *Optional:* so WhatsApp/social link previews show the CookSmart image, build with your Vercel URL:
   `php build-static.php https://your-project.vercel.app`

In the preview the enquiry form shows a "design preview" notice instead of sending, and every page is marked *noindex* so Google ignores it. `dist/` and `build-static.php` are blocked on Apache hosting and are not part of the live site.

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
├── index.php, about-us.php, products.php, product.php, career.php, admin.php,
│   company-policy.php, contact-us.php, contact-submit.php, 404.php, sitemap.php
├── .htaccess · router.php (dev only) · robots.txt
├── includes/   config.php · functions.php · careers.php · header.php · footer.php · partials/
├── preview/    demo owner area scripts for the Vercel preview only (not used on PHP hosting)
├── data/       products.php · company.php
├── storage/    jobs.json · admin.json (created by the owner area) · enquiry log in dev — blocked from the web
└── assets/
    ├── css/    main.css (global) · pages.css (sections)
    ├── js/     animations.js · main.js · hero.js · carousel.js · products.js · product.js · policy.js · contact.js · admin.js
    └── images/ logo/ · products/ · spice-plate.svg · pattern-*.svg · og-image.jpg
```
