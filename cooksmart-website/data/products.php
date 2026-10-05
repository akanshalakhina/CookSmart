<?php
/**
 * CookSmart products — the only source of product content on the site.
 *
 * Add more products or pack sizes here, using only values printed on the
 * actual packs. Images live in assets/images/products/{file}.webp and {file}-sm.webp.
 * (WordPress migration: custom post type `cs_product` with these fields.)
 */
declare(strict_types=1);

$spiceDescription = static fn (string $name, string $weight): string =>
    "Sugrihini {$name} from CookSmart — a ready-to-use kitchen essential, packed in a convenient {$weight} pack.";

return [
    'mirchi-powder' => [
        'slug'        => 'mirchi-powder',
        'name'        => 'Mirchi Powder',
        'name_hi'     => 'लाल मिर्च पाउडर',
        'range'       => 'Sugrihini',
        'range_bn'    => 'সুগৃহিণী',
        'category'    => 'spices',
        'net_weights' => ['100 g'],
        'images'      => ['primary' => 'mirchi-powder-100g', 'alt' => 'mirchi-powder-alt'],
        'accent'      => '#E41019',
        'accent_soft' => '#FDE4E2',
        'claims'      => ['100% Satisfaction Guarantee', 'Vegetarian'],
        'description' => $spiceDescription('Mirchi Powder', '100 g'),
    ],
    'haldi-powder' => [
        'slug'        => 'haldi-powder',
        'name'        => 'Haldi Powder',
        'name_hi'     => 'हल्दी पाउडर',
        'range'       => 'Sugrihini',
        'range_bn'    => 'সুগৃহিণী',
        'category'    => 'spices',
        'net_weights' => ['100 g'],
        'images'      => ['primary' => 'haldi-powder-100g', 'alt' => 'haldi-powder-alt'],
        'accent'      => '#E9A900',
        'accent_soft' => '#FFF4CC',
        'claims'      => ['100% Satisfaction Guarantee', 'Vegetarian'],
        'description' => $spiceDescription('Haldi Powder', '100 g'),
    ],
    'jeera-powder' => [
        'slug'        => 'jeera-powder',
        'name'        => 'Jeera Powder',
        'name_hi'     => 'जीरा पाउडर',
        'range'       => 'Sugrihini',
        'range_bn'    => 'সুগৃহিণী',
        'category'    => 'spices',
        'net_weights' => ['50 g'],
        'images'      => ['primary' => 'jeera-powder-50g', 'alt' => 'jeera-powder-alt'],
        'accent'      => '#C07C0A',
        'accent_soft' => '#F8EAD3',
        'claims'      => ['100% Satisfaction Guarantee', 'Vegetarian'],
        'description' => $spiceDescription('Jeera Powder', '50 g'),
    ],
    'dhania-powder' => [
        'slug'        => 'dhania-powder',
        'name'        => 'Dhania Powder',
        'name_hi'     => 'धनिया पाउडर',
        'range'       => 'Sugrihini',
        'range_bn'    => 'সুগৃহিণী',
        'category'    => 'spices',
        'net_weights' => ['50 g'],
        'images'      => ['primary' => 'dhania-powder-50g', 'alt' => 'dhania-powder-alt'],
        'accent'      => '#9A8418',
        'accent_soft' => '#F3EFD2',
        'claims'      => ['100% Satisfaction Guarantee', 'Vegetarian'],
        'description' => $spiceDescription('Dhania Powder', '50 g'),
    ],
    'chakki-fresh-atta' => [
        'slug'        => 'chakki-fresh-atta',
        'name'        => 'Chakki Fresh Atta',
        'name_hi'     => 'आटा',
        'range'       => 'CookSmart Chakki Fresh',
        'range_bn'    => '',
        'category'    => 'atta',
        'net_weights' => ['5 kg'],
        'images'      => ['primary' => 'chakki-fresh-atta-5kg'],
        'accent'      => '#ED5A04',
        'accent_soft' => '#FFE8D6',
        'claims'      => ['Premium Quality', 'Natural & Fresh', '100% Natural', 'No Additives', 'No Preservatives', 'Natural Whole Wheat', 'Hygienically Packed', 'Vegetarian'],
        'description' => 'CookSmart Chakki Fresh Atta — premium quality natural whole wheat atta, hygienically packed for your healthy life. 100% natural, with no additives and no preservatives.',
    ],
];
