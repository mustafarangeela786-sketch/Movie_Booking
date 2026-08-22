<?php
// ============================================================
// Minimal autoloader (Composer NOT required).
// Loads PHPMailer directly from vendor/phpmailer/phpmailer/src/
// so OrderMailer.php's `require_once __DIR__ . '/../vendor/autoload.php'`
// works exactly like a real Composer-installed vendor folder.
// ============================================================

require_once __DIR__ . '/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/phpmailer/src/SMTP.php';
