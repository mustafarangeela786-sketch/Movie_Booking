<?php
// ============================================================
// MovieBook — Booking Confirmation Mailer
//
// Public entry point:
//   send_booking_confirmation_email($conn, $bookingData);
//
// $bookingData must contain at least:
//   booking_id, customer_name, customer_email, movie_title,
//   theater_name, location, show_date, show_time, seat_class,
//   adult_seats, kid_seats, total_amount
//
// Safe to call right after your "INSERT INTO bookings" succeeds.
// It NEVER throws — failures are caught and logged, so the
// booking itself is never affected even if the email fails.
// ============================================================

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/EmailLogger.php';
require_once __DIR__ . '/templates/booking-confirmation.php';

// PHPMailer (bundled directly under /vendor — no Composer needed)
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * Main entry point. Call this AFTER your booking INSERT succeeds.
 *
 * @param mysqli $conn        An open DB connection (reuse your existing one).
 * @param array  $bookingData See field list in the file header comment.
 * @return bool  true if an email was sent, false on failure.
 */
function send_booking_confirmation_email(mysqli $conn, array $bookingData): bool {
    $bookingId = $bookingData['booking_id'] ?? null;

    if (!$bookingId) {
        EmailLogger::error('Missing booking_id — cannot send confirmation email.', $bookingData);
        return false;
    }

    if (empty($bookingData['customer_email']) || !filter_var($bookingData['customer_email'], FILTER_VALIDATE_EMAIL)) {
        EmailLogger::error('Invalid or missing customer_email — skipping email.', ['booking_id' => $bookingId]);
        return false;
    }

    $sent = deliver_booking_email($bookingData);

    return $sent;
}

/**
 * Builds the message and hands it to PHPMailer over SMTP.
 * Returns true/false; never throws.
 */
function deliver_booking_email(array $bookingData): bool {
    $bookingId = $bookingData['booking_id'];

    if (!email_is_configured()) {
        EmailLogger::error('SMTP is not configured (missing SMTP_USERNAME/SMTP_PASSWORD in Email/.env).', ['booking_id' => $bookingId]);
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        // ---- Server settings ----
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // ---- Recipients ----
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($bookingData['customer_email'], $bookingData['customer_name'] ?? '');
        $mail->addReplyTo(SUPPORT_EMAIL, COMPANY_NAME);

        // ---- Content ----
        $mail->isHTML(true);
        $mail->Subject = 'Your Movie Ticket is Confirmed  #' . $bookingId;
        $mail->Body    = render_booking_confirmation_email($bookingData);
        $mail->AltBody = build_plain_text_fallback($bookingData);

        $mail->send();

        EmailLogger::info('Booking confirmation email sent successfully.', [
            'booking_id' => $bookingId,
            'to'         => $bookingData['customer_email'],
        ]);
        return true;

    } catch (PHPMailerException $e) {
        EmailLogger::error('Failed to send booking confirmation email.', [
            'booking_id' => $bookingId,
            'to'         => $bookingData['customer_email'] ?? null,
            'error'      => $mail->ErrorInfo ?: $e->getMessage(),
        ]);
        return false;
    } catch (\Throwable $e) {
        EmailLogger::error('Unexpected error while sending booking confirmation email.', [
            'booking_id' => $bookingId,
            'error'      => $e->getMessage(),
        ]);
        return false;
    }
}

/** Plain-text fallback for clients that don't render HTML. */
function build_plain_text_fallback(array $booking): string {
    $lines   = [];
    $lines[] = 'Booking Confirmed';
    $lines[] = '';
    $lines[] = 'Hi ' . ($booking['customer_name'] ?? 'Customer') . ',';
    $lines[] = '';
    $lines[] = 'Booking ID: #' . $booking['booking_id'];
    $lines[] = 'Movie: ' . ($booking['movie_title'] ?? '');
    $lines[] = 'Cinema: ' . ($booking['theater_name'] ?? '') . ', ' . ($booking['location'] ?? '');
    $lines[] = 'Date & Time: ' . ($booking['show_date'] ?? '') . ' - ' . ($booking['show_time'] ?? '');
    $lines[] = 'Class: ' . ($booking['seat_class'] ?? '');
    $lines[] = 'Adult Seats: ' . ($booking['adult_seats'] ?? 0);
    $lines[] = 'Kid Seats: ' . ($booking['kid_seats'] ?? 0);
    $lines[] = 'Total Paid: Rs. ' . ($booking['total_amount'] ?? '0');
    $lines[] = '';
    $lines[] = 'Thank you for booking with ' . COMPANY_NAME . '!';
    $lines[] = 'Support: ' . SUPPORT_EMAIL . (SUPPORT_PHONE ? ' / ' . SUPPORT_PHONE : '');

    return implode("\n", $lines);
}
