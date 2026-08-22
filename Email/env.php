<?php
// ============================================================
// Minimal .env loader (no external dependency).
// Reads Email/.env and exposes values via env('KEY', 'default').
// ============================================================

function load_env(string $path): void {
    static $loaded = false;
    if ($loaded) return;
    $loaded = true;

    if (!file_exists($path)) {
        return; // .env is optional if real env vars are set another way
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (strpos($line, '=') === false) continue;

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);
        $value = trim($value, "\"'");

        if (getenv($key) === false) {
            putenv("$key=$value");
        }
        if (!isset($_ENV[$key])) {
            $_ENV[$key] = $value;
        }
    }
}

function env(string $key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        $value = $_ENV[$key] ?? null;
    }
    return $value !== null && $value !== '' ? $value : $default;
}
