<?php
/**
 * Renders the booking confirmation email HTML.
 *
 * Expects $booking to be an associative array with keys:
 *   customer_name, booking_id, movie_title, theater_name, location,
 *   show_date, show_time, seat_class, adult_seats, kid_seats, total_amount
 *
 * Returns the full HTML string.
 */
function render_booking_confirmation_email(array $booking): string {
    $customerName = htmlspecialchars($booking['customer_name'] ?? 'Customer', ENT_QUOTES, 'UTF-8');
    $bookingId    = htmlspecialchars((string)($booking['booking_id'] ?? ''), ENT_QUOTES, 'UTF-8');
    $movieTitle   = htmlspecialchars($booking['movie_title'] ?? '', ENT_QUOTES, 'UTF-8');
    $theaterName  = htmlspecialchars($booking['theater_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $location     = htmlspecialchars($booking['location'] ?? '', ENT_QUOTES, 'UTF-8');
    $showDate     = htmlspecialchars($booking['show_date'] ?? '', ENT_QUOTES, 'UTF-8');
    $showTime     = htmlspecialchars($booking['show_time'] ?? '', ENT_QUOTES, 'UTF-8');
    $seatClass    = htmlspecialchars($booking['seat_class'] ?? '', ENT_QUOTES, 'UTF-8');
    $adultSeats   = htmlspecialchars((string)($booking['adult_seats'] ?? 0), ENT_QUOTES, 'UTF-8');
    $kidSeats     = htmlspecialchars((string)($booking['kid_seats'] ?? 0), ENT_QUOTES, 'UTF-8');
    $totalAmount  = htmlspecialchars((string)($booking['total_amount'] ?? '0'), ENT_QUOTES, 'UTF-8');

    $companyName  = htmlspecialchars(COMPANY_NAME, ENT_QUOTES, 'UTF-8');
    $supportEmail = htmlspecialchars(SUPPORT_EMAIL, ENT_QUOTES, 'UTF-8');
    $supportPhone = htmlspecialchars(SUPPORT_PHONE, ENT_QUOTES, 'UTF-8');
    $supportPhoneLine = $supportPhone !== '' ? "<br>Phone: {$supportPhone}" : '';

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Confirmation</title>
</head>
<body style="margin:0;padding:0;background:#0f1116;font-family:'Algerian',Arial,Helvetica,sans-serif;"> <table width="100%" cellpadding="0" cellspacing="0" style="background:#0f1116;padding:30px 0;"> <tr> <td align="center"> <table width="560" cellpadding="0" cellspacing="0" style="background:#171a21;border-radius:10px;overflow:hidden;"> <tr> <td style="background:#ffb400;padding:20px 30px;"> <span style="font-size:20px;font-weight:bold;color:#111111;"> {$companyName}</span> </td> </tr> <tr> <td style="padding:30px;"> <h2 style="color:#ffb400;margin-top:0;">Booking Confirmed!</h2> <p style="color:#eaeaea;font-size:14px;">Hi {$customerName},</p> <p style="color:#eaeaea;font-size:14px;">Your movie ticket has been booked successfully. Here are your details:</p> <table width="100%" cellpadding="8" cellspacing="0" style="margin-top:16px;border-collapse:collapse;"> <tr><td style="color:#a0a0a0;font-size:13px;">Booking ID</td><td style="color:#eaeaea;font-size:13px;text-align:right;">#{$bookingId}</td></tr> <tr><td style="color:#a0a0a0;font-size:13px;">Movie</td><td style="color:#eaeaea;font-size:13px;text-align:right;">{$movieTitle}</td></tr> <tr><td style="color:#a0a0a0;font-size:13px;">Cinema</td><td style="color:#eaeaea;font-size:13px;text-align:right;">{$theaterName}, {$location}</td></tr> <tr><td style="color:#a0a0a0;font-size:13px;">Date & Time</td><td style="color:#eaeaea;font-size:13px;text-align:right;">{$showDate} - {$showTime}</td></tr> <tr><td style="color:#a0a0a0;font-size:13px;">Class</td><td style="color:#eaeaea;font-size:13px;text-align:right;">{$seatClass}</td></tr> <tr><td style="color:#a0a0a0;font-size:13px;">Adult Seats</td><td style="color:#eaeaea;font-size:13px;text-align:right;">{$adultSeats}</td></tr> <tr><td style="color:#a0a0a0;font-size:13px;">Kid Seats</td><td style="color:#eaeaea;font-size:13px;text-align:right;">{$kidSeats}</td></tr> <tr><td style="color:#ffb400;font-size:15px;font-weight:bold;padding-top:14px;">Total Paid</td><td style="color:#ffb400;font-size:15px;font-weight:bold;text-align:right;padding-top:14px;">Rs. {$totalAmount}</td></tr> </table> <p style="color:#888888;font-size:12px;margin-top:30px;"> Thank you for booking with {$companyName}!<br> Support: {$supportEmail}{$supportPhoneLine} </p> </td> </tr> </table> </td> </tr> </table>
</body>
</html>
HTML;
}
