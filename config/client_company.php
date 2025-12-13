<?php

return [
    'name' => env('COMPANY_NAME', 'Default Company'),
    'owner_name' => env('COMPANY_OWNER', 'Owner'),
    'logo' => env('COMPANY_LOGO', 'default_logo.png'),
    'logo_svg_path' => env('COMPANY_LOGO_SVG_PATH', 'images/default.svg'),
    'phone_number' => env('COMPANY_PHONE', ''),
    'pusher_enabled' => env('PUSHER_ENABLED', false),
];
