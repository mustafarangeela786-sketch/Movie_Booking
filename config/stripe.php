<?php
/* ==========================================================
   config/stripe.php - Stripe test-mode API keys.

   HOW TO GET THESE (free, no business docs needed for TEST mode):
     1. Sign up at https://dashboard.stripe.com/register (just an
        email address - no CNIC/business verification required to
        use Test mode).
     2. Once logged in, make sure the dashboard toggle says
        "Test mode" (top-right).
     3. Go to Developers -> API keys.
     4. Copy the "Secret key" (starts with sk_test_...) and the
        "Publishable key" (starts with pk_test_...) and paste them
        below.
     5. Save this file. Card payments on the site will now go
        through Stripe's real hosted Checkout page, using test
        card numbers only - no real money moves in test mode.
        Test card: 4242 4242 4242 4242, any future expiry, any CVC.
   ========================================================== */

define('STRIPE_SECRET_KEY', 'sk_test_REPLACE_WITH_YOUR_STRIPE_TEST_SECRET_KEY');
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_REPLACE_WITH_YOUR_STRIPE_TEST_PUBLISHABLE_KEY');

// PKR is supported by Stripe. If your Stripe account/region doesn't
// support PKR, change this to 'usd' (Stripe will then charge in USD).
define('STRIPE_CURRENCY', 'pkr');
