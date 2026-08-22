<?php
// ============================================================
// Simple append-only logger for email send attempts.
// Writes to Email/logs/email.log — check this file first if
// confirmation emails aren't arriving.
// ============================================================

require_once __DIR__ . '/config.php';

class EmailLogger {

    public static function info(string $message, array $context = []): void {
        self::write('INFO', $message, $context);
    }

    public static function error(string $message, array $context = []): void {
        self::write('ERROR', $message, $context);
    }

    private static function write(string $level, string $message, array $context): void {
        $dir = dirname(EMAIL_LOG_FILE);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $line = sprintf(
            "[%s] [%s] %s %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            $context ? json_encode($context, JSON_UNESCAPED_SLASHES) : ''
        );

        @file_put_contents(EMAIL_LOG_FILE, $line, FILE_APPEND | LOCK_EX);

        if ($level === 'ERROR') {
            error_log('[MOVIEBOOK EMAIL] ' . $message . ' ' . json_encode($context));
        }
    }
}
