# Online Movie Booking System (PHP + MySQL)

A complete PHP/MySQL web application for booking movie tickets online,
built for the eProject "PHP - Movie Booking System" specification.

## Features

- Visitor registration & login (passwords securely hashed)
- Browse "Now Showing" movies with poster, genre, duration
- Movie details page with embedded trailer, description, and showtimes
- Movie reviews & star ratings from logged-in users
- Real interactive seat map (pick individual seats, see Available / Booked / Selected) once a show is assigned a screen - see "Setting Up Real Seat Selection" below
- Seat class selection: Gold / Platinum / Box, each with its own price
- Kid seats (age 3–12) automatically get a 50% concession
- Ticket booking with instant e-ticket / booking confirmation
- **Automatic booking confirmation email sent to the customer's Gmail** after every booking
- "My Bookings" history page for each user
- Contact Us page listing all current cinema locations (pulled live from the database)
- Admin Login link in the site footer (and Contact link in the navbar)
- Admin panel (separate login) to:
  - View dashboard stats (movies, theaters, shows, users, revenue)
  - Add / edit / delete theaters
  - Add / edit / delete movies (with trailer link)
  - Schedule shows and set Gold/Platinum/Box prices
  - View all registered users
- Comes pre-loaded with 4 real, currently-operating Karachi cinemas (with live Google Maps locations) and 90 real, famous movies across every genre
- "View on Google Maps" live location link on every showtime and cinema card
- Payment method selection (Card, JazzCash, EasyPaisa, Cash at Counter) during booking
- Booking confirmation email is sent to whichever email address the customer types at checkout (defaults to their account email, editable)

## Tech Stack

- PHP (procedural, mysqli with prepared statements)
- MySQL / MariaDB
- HTML5 / CSS3 (no framework, custom dark theme)

## Setting Up "Continue with Google" Sign-In

Login and Register both include a real Google Sign-In button. It needs
a free Google Client ID (no library/Composer needed - uses Google's
official Identity Services script).

1. Go to `https://console.cloud.google.com/apis/credentials`
2. Click **Create Credentials → OAuth Client ID → Web application**
3. Under **Authorized JavaScript origins**, add `http://localhost`
4. Copy the generated Client ID (ends in `.apps.googleusercontent.com`)
5. Open `config/google.php` and replace `YOUR_GOOGLE_CLIENT_ID_HERE...`
   with your real Client ID
6. If you already imported the database before this feature was added,
   run `database/migration_google_login.sql` once, and also run
   `database/migration_v2.sql` (adds live map coordinates for theaters
   and the payment method / confirmation email fields for bookings).
   Fresh installs already include all of this in
   `database/movie_booking.sql`.

That's it - the Google button will now appear and work on both the
Login and Register pages.

## Setting Up Booking Confirmation Emails (Gmail)

Emails are sent using PHPMailer over Gmail's SMTP server (already bundled
in the `vendor/` folder — no Composer or extra install needed).

1. Go to `Email/.env.example` and make a copy named `Email/.env` in the
   same folder.
2. On the Gmail account you want to send from, turn on **2-Step
   Verification**, then generate an **App Password** at
   `https://myaccount.google.com/apppasswords`.
3. Open `Email/.env` and fill in:
   - `SMTP_USERNAME` — your Gmail address
   - `SMTP_PASSWORD` — the 16-character App Password (not your normal Gmail password)
   - `MAIL_FROM_ADDRESS` — usually the same Gmail address
4. Save the file. That's it — every new booking will now email the
   customer an HTML confirmation automatically.

If an email fails to send (e.g. `.env` not filled in yet), the booking
itself still completes normally — check `Email/logs/email.log` for the
exact error message.

## Setting Up Real Seat Selection (Screens & Seats)

Booking now supports a real interactive seat map (pick individual seats,
see Available / Booked / Selected at a glance) instead of just choosing
a class and a quantity.

1. Import `database/migration_v5_seat_map.sql` once (same way as the
   other migration files - phpMyAdmin, SQL tab, paste, Go). It only
   *adds* new tables (`screens`, `seats`, `booking_seats`) and a
   nullable `screen_id` column on `shows` - nothing existing is changed
   or removed, so it's safe to run even on a site that's already live.
2. In the admin panel, go to **Screens & Seats** and add a screen for
   each theater (e.g. "Screen 1", 8 rows x 10 seats). This instantly
   generates a full seat grid, auto-split into Gold/Platinum/Box by
   row (fine-tune individual seats afterwards from "Edit Seats").
3. When scheduling a show in **Shows & Rates**, pick a Screen. That
   show's booking page will now show the real seat map.
4. Shows left with no screen assigned keep working exactly as before,
   with the original "seat class + how many seats" picker - so nothing
   breaks on movies/theaters you haven't set screens up for yet.

## Setting Up Real Card Payments (Stripe, Test Mode)

The "Credit/Debit Card" payment option now goes through a real Stripe
Checkout page instead of a dummy card-number form. No business
registration or CNIC is needed for Stripe's free Test mode - only
an email address.

1. Import `database/migration_v7_payment_gateway.sql` once (skip
   this if you imported the fresh `database/movie_booking_full.sql`,
   which already includes it).
2. Sign up for free at `https://dashboard.stripe.com/register`.
3. Make sure the dashboard is in **Test mode** (top-right toggle).
4. Go to **Developers -> API keys**, copy the **Secret key**
   (`sk_test_...`) and **Publishable key** (`pk_test_...`).
5. Open `config/stripe.php` and paste both keys in.
6. That's it. Choosing "Credit/Debit Card" at checkout now redirects
   to Stripe's real hosted payment page. Use test card
   `4242 4242 4242 4242`, any future expiry date, any CVC - no real
   money is charged. The booking is only marked "Paid" and the
   e-ticket/QR/email are only sent after the site verifies the
   payment with Stripe's API server-side (`stripe_return.php`).
7. The other payment options (EasyPaisa, JazzCash, PayPal, UPaisa,
   Cash at Counter) remain simulated, since those require a
   registered local business/merchant account to integrate for real.
8. To go live with real cards later: switch to Stripe's **Live mode**
   keys (`sk_live_...` / `pk_live_...`) in `config/stripe.php`, and
   make sure the site is served over HTTPS.

## Setup Instructions (XAMPP / WAMP / LAMP)

1. Copy the `movie_booking` folder into your server's web root
   (e.g. `htdocs` for XAMPP, `www` for WAMP).
2. Start Apache and MySQL from your control panel.
3. Open phpMyAdmin and import `database/movie_booking.sql`.
   This creates the `movie_booking` database, all tables, a default
   admin account, and a few sample movies/theaters/shows.
4. Check `config/db.php` and update `$DB_USER` / `$DB_PASS` if your
   MySQL login differs from the XAMPP default (`root` / empty password).
6. Open `http://localhost/movie_booking/` in your browser.

## Default Admin Login

- URL: `admin/login.php`
- Username: `admin`
- Password: `admin123`

(Change this password directly in the `admin` table after first login,
for security.)

## Folder Structure

```
movie_booking/
├── admin/              Admin panel (login, dashboard, CRUD pages)
├── assets/css/         Stylesheet
├── config/db.php       Database connection settings
├── database/           SQL schema + sample data
├── includes/           Shared header, footer, helper functions
├── index.php           Home page (Now Showing)
├── register.php / login.php / logout.php
├── movie_details.php   Movie info, trailer, showtimes, reviews
├── book_ticket.php     Seat class & seat count selection
├── booking_confirm.php E-ticket / booking confirmation
└── my_bookings.php     User's booking history
```

## Notes for Status Report Submission

This project maps to the required features from the specification
document (login/registration, showtimes by date, reviews & ratings,
trailers, admin dashboard, theater/movie/show management). Screenshots
of each working page and this source code folder can be zipped together
for your status report / final submission.
