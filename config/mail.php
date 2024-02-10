<?php
return [
    'driver' => env('MAIL_MAILER', 'smtp'), // Use MAIL_MAILER instead of MAIL_DRIVER
    'host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
    'port' => env('MAIL_PORT', 2525),
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'info@choicemarriage.com'),
        'name' => env('MAIL_FROM_NAME', 'Your Name'),
    ],
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    'username' => env('MAIL_USERNAME', '9f1fce4af2f969'), // Use MAIL_USERNAME instead of MAIL_USERNAME
    'password' => env('MAIL_PASSWORD', 'f0a12e1d8c566f'), // Use MAIL_PASSWORD instead of MAIL_PASSWORD
    'sendmail' => '/usr/sbin/sendmail -bs',
    'markdown' => [
        'theme' => 'default',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],
];
