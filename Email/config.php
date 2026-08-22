<?php
// ============================================================
// MovieBook — Booking Confirmation Email Config
// All secrets come from environment variables (Email/.env).
// Never hardcode your real Gmail password here.
// ============================================================

require_once __DIR__ . '/env.php';
load_env(__DIR__ . '/.env');

define('SMTP_HOST',       env('SMTP_HOST', 'smtp.gmail.com'));
define('SMTP_PORT',       (int) env('SMTP_PORT', 587));
define('SMTP_ENCRYPTION', env('SMTP_ENCRYPTION', 'tls')); // 'tls' or 'ssl'
define('SMTP_USERNAME',   env('SMTP_USERNAME', ''));
define('SMTP_PASSWORD',   env('SMTP_PASSWORD', ''));

define('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'no-reply@example.com'));
define('MAIL_FROM_NAME',    env('MAIL_FROM_NAME', 'MovieBook'));

define('COMPANY_NAME',   env('COMPANY_NAME', 'MovieBook'));
define('SUPPORT_EMAIL',  env('SUPPORT_EMAIL', 'support@example.com'));
define('SUPPORT_PHONE',  env('SUPPORT_PHONE', ''));

define('EMAIL_LOG_FILE', __DIR__ . '/logs/email.log');

function email_is_configured(): bool {
    return SMTP_USERNAME !== '' && SMTP_PASSWORD !== '';
}
