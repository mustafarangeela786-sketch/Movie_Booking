<?php
/* ==========================================================
   includes/stripe_helper.php

   Minimal Stripe integration using raw cURL calls to Stripe's
   REST API directly - no Composer / stripe-php SDK needed.

   Uses Stripe Checkout: a secure, Stripe-hosted payment page.
   Card numbers are typed on Stripe's page, never on our server -
   this is both simpler and safer (keeps us out of PCI scope).
   ========================================================== */
require_once __DIR__ . '/../config/stripe.php';

// Low-level request to any Stripe API endpoint.
function stripe_request($method, $endpoint, $params = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY . ':');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_err  = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['error' => ['message' => 'Could not reach Stripe: ' . $curl_err]];
    }

    $data = json_decode($response, true);

    if ($http_code >= 400) {
        return ['error' => $data['error'] ?? ['message' => 'Stripe API error (HTTP ' . $http_code . ')']];
    }

    return $data;
}

// Create a Stripe Checkout Session for one booking and return the
// full API response (has ['url'] to redirect the customer to on
// success, or ['error'] if something went wrong - e.g. keys not
// configured yet in config/stripe.php).
function stripe_create_checkout_session($amount_pkr, $description, $booking_id, $success_url, $cancel_url) {
    if (strpos(STRIPE_SECRET_KEY, 'REPLACE_WITH_YOUR') !== false) {
        return ['error' => ['message' => 'Stripe API keys have not been set up yet in config/stripe.php.']];
    }

    $unit_amount = (int) round($amount_pkr * 100); // Stripe wants the smallest currency unit

    $params = [
        'mode'                                             => 'payment',
        'success_url'                                      => $success_url . (strpos($success_url, '?') === false ? '?' : '&') . 'session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'                                        => $cancel_url,
        'client_reference_id'                              => $booking_id,
        'metadata[booking_id]'                             => $booking_id,
        'line_items[0][quantity]'                          => 1,
        'line_items[0][price_data][currency]'               => STRIPE_CURRENCY,
        'line_items[0][price_data][unit_amount]'            => $unit_amount,
        'line_items[0][price_data][product_data][name]'    => $description,
    ];

    return stripe_request('POST', 'checkout/sessions', $params);
}

// Look up a Checkout Session by ID (used on the return URL to verify,
// server-side, that the payment actually succeeded before we mark
// the booking as paid - never trust the redirect alone).
function stripe_retrieve_checkout_session($session_id) {
    return stripe_request('GET', 'checkout/sessions/' . rawurlencode($session_id));
}
