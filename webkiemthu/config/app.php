<?php
declare(strict_types=1);

return [
    'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Ho_Chi_Minh',
    'jwt' => [
        'secret' => getenv('JWT_SECRET_KEY') ?: 'web_kiem_thu_secret_key_2024_change_this_to_a_longer_key',
    ],
];
