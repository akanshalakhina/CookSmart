<?php
/**
 * CookSmart — site configuration.
 *
 * Every contact detail and link shown on the website is read from here,
 * so a change in this file updates the whole site.
 * (WordPress migration: these become Customizer / ACF Options fields.)
 */
declare(strict_types=1);

/* ------------------------------------------------------------------
 * Links you still need to fill in
 * ------------------------------------------------------------------ */

// Online shop that every "Shop Now" button opens (in a new tab).
// TODO(client): paste the full shop URL, e.g. 'https://shop.example.com'.
const SHOP_URL = '';

// Public site address without a trailing slash, e.g. 'https://www.cooksmart.in'.
// Leave empty to auto-detect from the current request.
const SITE_URL = '';

// Social profiles — only non-empty entries are shown in the header and footer.
const SOCIAL_LINKS = [
    'facebook'  => '',
    'instagram' => '',
    'youtube'   => '',
    'linkedin'  => '',
    'x'         => '',
];

// Show the floating WhatsApp button (uses PHONE_E164). Turn on once the
// number is confirmed to be on WhatsApp.
const WHATSAPP_ENABLED = false;

/* ------------------------------------------------------------------
 * Company details (from the company profile)
 * ------------------------------------------------------------------ */

const SITE_NAME          = 'CookSmart';
const COMPANY_LEGAL_NAME = 'Cooksmart Premium Food Private Limited';
const FOUNDED_YEAR       = 2015;
const LEADER_NAME        = 'Mr Santanu Saha';

const PHONE_DISPLAY  = '+91 98000 87155';
const PHONE_E164     = '+919800087155';
const CONTACT_EMAIL  = 'cooksmartkol@gmail.com';

// Where enquiry-form submissions are emailed.
const CONTACT_TO_EMAIL = 'cooksmartkol@gmail.com';

const ADDRESSES = [
    'registered' => [
        'label'    => 'Registered Office',
        'lines'    => ['324, S.N. Mallick Road', 'P.O. + P.S. – Singur', 'Dist. – Hooghly, West Bengal', 'PIN 712409'],
        'map_query'=> '324, S.N. Mallick Road, Singur, Hooghly, West Bengal 712409',
        'locality' => 'Singur',
        'region'   => 'West Bengal',
        'postcode' => '712409',
        'street'   => '324, S.N. Mallick Road, P.O. + P.S. Singur, Dist. Hooghly',
    ],
    'corporate' => [
        'label'    => 'Corporate Office',
        'lines'    => ['142/1A, Sarat Bose Road', 'Kolkata, West Bengal', 'PIN 700029'],
        'map_query'=> '142/1A, Sarat Bose Road, Kolkata, West Bengal 700029',
        'locality' => 'Kolkata',
        'region'   => 'West Bengal',
        'postcode' => '700029',
        'street'   => '142/1A, Sarat Bose Road',
    ],
];

/* ------------------------------------------------------------------
 * Environment
 * ------------------------------------------------------------------ */

date_default_timezone_set('Asia/Kolkata');

// On localhost enquiries are written to /storage/enquiries.log instead of emailed.
define('DEV_MODE', in_array($_SERVER['SERVER_NAME'] ?? 'localhost', ['localhost', '127.0.0.1', '::1'], true));

// GSAP version loaded from the CDN.
const GSAP_VERSION = '3.13.0';
