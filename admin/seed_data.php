<?php
/* ==========================================================
   admin/seed_data.php - One-click helper (admin only).

   Run this once to automatically finish setting up the whole
   site so nothing sits at "0" / "NULL" / empty for no reason:
     1. Mark a handful of "Now Showing" movies as Featured.
     2. Give every movie showtimes at 10 different cinemas (picked
        from whatever theaters exist in your DB), spread across
        varied upcoming dates/days and varied times, with
        Gold/Platinum/Box prices - so each movie can be booked at
        10 different cinemas and never shows "no upcoming shows".
     3. Give any coupon that has no expiry date a sensible one
        (90 days from today), so "Never" isn't the only option.
     4. Auto-generate a screen for every theater that doesn't have
        one yet, so the real interactive seat map has something to
        show. Each theater gets its own layout (rows/seats vary per
        theater) so different cinemas have genuinely different seat
        maps instead of an identical grid everywhere.
     5. Assign a screen to any upcoming show that doesn't have
        one yet, so its booking page uses the real seat map
        instead of the old class+quantity picker.

   It's safe to click "Run" more than once: anything already set
   up (a movie already featured, a coupon that already has an
   expiry, a theater that already has a screen, a show that
   already has one assigned) is left alone and just skipped.
   ========================================================== */
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$log = [];
$ran = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_seed'])) {
    $ran = true;

    // ---- 0. Give every movie missing an industry (Hollywood/Bollywood) a sensible one ----
    $no_industry = $conn->query("SELECT movie_id, title, language FROM movies WHERE industry IS NULL OR industry = ''");
    if ($no_industry && $no_industry->num_rows > 0) {
        $upd = $conn->prepare("UPDATE movies SET industry = ? WHERE movie_id = ?");
        while ($mv = $no_industry->fetch_assoc()) {
            $mid = (int)$mv['movie_id'];
            $lang = strtolower(trim($mv['language'] ?? ''));
            $industry = in_array($lang, ['urdu', 'hindi', 'punjabi']) ? 'Bollywood' : 'Hollywood';
            $upd->bind_param("si", $industry, $mid);
            $upd->execute();
            $log[] = "Set industry for \"{$mv['title']}\" to $industry.";
        }
        $upd->close();
    } else {
        $log[] = "Skipped industry fill - every movie already has one.";
    }

    // ---- 1. Feature a handful of Now Showing movies (skip if some already are) ----
    $already_featured = (int)$conn->query("SELECT COUNT(*) AS n FROM movies WHERE is_featured = 1")->fetch_assoc()['n'];
    if ($already_featured === 0) {
        $pick = $conn->query("SELECT movie_id, title FROM movies WHERE release_date <= CURDATE() ORDER BY RAND() LIMIT 10");
        $ids = [];
        while ($row = $pick->fetch_assoc()) {
            $ids[] = (int)$row['movie_id'];
            $log[] = "Featured: " . $row['title'];
        }
        if ($ids) {
            $conn->query("UPDATE movies SET is_featured = 1 WHERE movie_id IN (" . implode(',', $ids) . ")");
        }
    } else {
        $log[] = "Skipped featuring - $already_featured movie(s) are already featured.";
    }

    // ---- 2. Make sure every movie has shows at 10 cinemas, spread across varied dates/times ----
    $CINEMAS_PER_MOVIE = 10;
    $theater_ids = [];
    $tres = $conn->query("SELECT theater_id FROM theaters");
    while ($t = $tres->fetch_assoc()) $theater_ids[] = (int)$t['theater_id'];

    if (count($theater_ids) < $CINEMAS_PER_MOVIE) {
        $log[] = "Only " . count($theater_ids) . " theaters found - import migration_v3_featured_and_cinemas.sql first so there are at least $CINEMAS_PER_MOVIE to choose from.";
    } else {
        // Wide spread of times so different cinemas/days don't all look identical.
        $times = ['10:30:00', '11:00:00', '13:15:00', '14:00:00', '15:30:00', '17:00:00', '18:15:00', '19:45:00', '20:30:00', '22:00:00', '22:45:00'];
        // Shows are spread across the next 14 days (varied days of the week, not just a repeating 5-day cycle).
        $DAY_SPREAD = 14;

        $movies = $conn->query("SELECT movie_id, title FROM movies");
        while ($m = $movies->fetch_assoc()) {
            $mid = (int)$m['movie_id'];
            $existing = $conn->prepare("SELECT COUNT(*) AS n FROM shows WHERE movie_id = ? AND show_date >= CURDATE()");
            $existing->bind_param("i", $mid);
            $existing->execute();
            $count = (int)$existing->get_result()->fetch_assoc()['n'];
            $existing->close();

            if ($count >= $CINEMAS_PER_MOVIE) {
                $log[] = "Skipped \"{$m['title']}\" - already has $count upcoming shows.";
                continue;
            }

            $pick_theaters = $theater_ids;
            shuffle($pick_theaters);
            $pick_theaters = array_slice($pick_theaters, 0, $CINEMAS_PER_MOVIE);

            // Give each cinema its own day offset (no repeats) picked at random within the spread window.
            $day_offsets = range(0, $DAY_SPREAD - 1);
            shuffle($day_offsets);

            $ins = $conn->prepare("INSERT INTO shows (movie_id, theater_id, show_date, show_time, price_gold, price_platinum, price_box) VALUES (?,?,?,?,?,?,?)");
            $added = 0;
            foreach ($pick_theaters as $i => $tid) {
                $show_date = date('Y-m-d', strtotime('+' . $day_offsets[$i % count($day_offsets)] . ' days'));
                $show_time = $times[array_rand($times)];
                $gold      = rand(800, 1200);
                $platinum  = rand(1500, 2200);
                $box       = rand(2500, 3500);
                $ins->bind_param("iissddd", $mid, $tid, $show_date, $show_time, $gold, $platinum, $box);
                $ins->execute();
                $added++;
            }
            $ins->close();
            $log[] = "Added $added showtimes for \"{$m['title']}\" across $CINEMAS_PER_MOVIE cinemas.";
        }
    }

    // ---- 3. Give any coupon with no expiry a sensible 90-day expiry ----
    $no_expiry = $conn->query("SELECT coupon_id, code FROM coupons WHERE expires_on IS NULL");
    if ($no_expiry && $no_expiry->num_rows > 0) {
        $expiry_date = date('Y-m-d', strtotime('+90 days'));
        $upd = $conn->prepare("UPDATE coupons SET expires_on = ? WHERE coupon_id = ?");
        while ($cp = $no_expiry->fetch_assoc()) {
            $cid = (int)$cp['coupon_id'];
            $upd->bind_param("si", $expiry_date, $cid);
            $upd->execute();
            $log[] = "Set \"{$cp['code']}\" to expire on " . date('d M Y', strtotime($expiry_date)) . ".";
        }
        $upd->close();
    } else {
        $log[] = "Skipped coupon expiry - every coupon already has one (or there are no coupons yet).";
    }

    // ---- 4 & 5. Auto-generate screens + assign them to shows (seat-map feature) ----
    if (!seatMapSchemaExists($conn)) {
        $log[] = "Skipped screens/seats - import database/migration_v5_seat_map.sql first to enable the real seat map.";
    } else {
        // 4. Give every theater without a screen yet a default one: 8 rows x 10 seats
        $theaters_needing_screen = $conn->query("
            SELECT t.theater_id, t.theater_name FROM theaters t
            LEFT JOIN screens sc ON sc.theater_id = t.theater_id
            WHERE sc.screen_id IS NULL
        ");
        $row_letters = range('A', 'Z');
        // Realistic cinema sizes from a small ~120-seat screen up to a big
        // ~400-seat one, randomly assigned per theater so cinemas genuinely differ.
        $layouts = [
            ['rows' => 12, 'seats' => 10, 'name' => 'Screen 1'],            // 120
            ['rows' => 13, 'seats' => 12, 'name' => 'Screen 1 (Standard)'], // 156
            ['rows' => 14, 'seats' => 14, 'name' => 'Screen 2'],            // 196
            ['rows' => 15, 'seats' => 16, 'name' => 'Screen 1 (Wide)'],     // 240
            ['rows' => 16, 'seats' => 18, 'name' => 'Screen 2 (Grand)'],    // 288
            ['rows' => 18, 'seats' => 18, 'name' => 'IMAX'],                // 324
            ['rows' => 20, 'seats' => 20, 'name' => 'IMAX (Superscreen)'],  // 400
        ];
        $li = 0;
        while ($t = $theaters_needing_screen->fetch_assoc()) {
            $theater_id = (int)$t['theater_id'];
            $layout = $layouts[array_rand($layouts)];
            $li++;
            $rows_count = $layout['rows'];
            $seats_per_row = $layout['seats'];
            $screen_name = $layout['name'];

            $ins = $conn->prepare("INSERT INTO screens (theater_id, screen_name, rows_count, seats_per_row) VALUES (?, ?, ?, ?)");
            $ins->bind_param("isii", $theater_id, $screen_name, $rows_count, $seats_per_row);
            $ins->execute();
            $screen_id = $ins->insert_id;
            $ins->close();

            $gold_rows = (int)round($rows_count * 0.4);
            $platinum_rows = (int)round($rows_count * 0.4);
            $seat_ins = $conn->prepare("INSERT INTO seats (screen_id, seat_row, seat_number, seat_code, seat_class) VALUES (?, ?, ?, ?, ?)");
            for ($r = 0; $r < $rows_count; $r++) {
                $row_letter = $row_letters[$r] ?? ('R' . $r);
                if ($r < $gold_rows) $class = 'Gold';
                elseif ($r < $gold_rows + $platinum_rows) $class = 'Platinum';
                else $class = 'Box';
                for ($n = 1; $n <= $seats_per_row; $n++) {
                    $seat_code = $row_letter . $n;
                    $seat_ins->bind_param("isiss", $screen_id, $row_letter, $n, $seat_code, $class);
                    $seat_ins->execute();
                }
            }
            $seat_ins->close();
            $log[] = "Created \"$screen_name\" ($rows_count x $seats_per_row seats) for \"{$t['theater_name']}\".";
        }

        // 5. Assign a screen to every upcoming show that doesn't have one yet
        $unassigned = $conn->query("SELECT show_id, theater_id FROM shows WHERE screen_id IS NULL AND show_date >= CURDATE()");
        $assigned_count = 0;
        if ($unassigned && $unassigned->num_rows > 0) {
            $screen_lookup = [];
            $scres = $conn->query("SELECT screen_id, theater_id FROM screens");
            while ($sc = $scres->fetch_assoc()) $screen_lookup[$sc['theater_id']][] = $sc['screen_id'];

            $upd = $conn->prepare("UPDATE shows SET screen_id = ? WHERE show_id = ?");
            while ($sh = $unassigned->fetch_assoc()) {
                $tid = (int)$sh['theater_id'];
                if (!empty($screen_lookup[$tid])) {
                    $screen_id = $screen_lookup[$tid][array_rand($screen_lookup[$tid])];
                    $sid = (int)$sh['show_id'];
                    $upd->bind_param("ii", $screen_id, $sid);
                    $upd->execute();
                    $assigned_count++;
                }
            }
            $upd->close();
        }
        $log[] = $assigned_count > 0
            ? "Assigned a screen to $assigned_count upcoming show(s) - those booking pages now use the real seat map."
            : "Skipped screen assignment - every upcoming show already has a screen (or none exist).";
    }
}

include __DIR__ . '/admin_header.php';
?> <h1>Bulk Setup: One-Click Site Setup</h1>
<?php if ($ran): ?> <div class="alert alert-success">Done. See the log below.</div> <div class="form-box" style="margin-left:0;max-width:700px;max-height:400px;overflow-y:auto;"> <?php foreach ($log as $line): ?> <p style="margin:4px 0;color:#c8c8c8;font-size:14px;"><?php echo clean($line); ?></p> <?php endforeach; ?> </div>
<?php endif; ?> <form method="POST" style="margin-top:20px;"> <button type="submit" name="run_seed" value="1">Run Bulk Setup</button>
</form> <?php include __DIR__ . '/admin_footer.php'; ?> 