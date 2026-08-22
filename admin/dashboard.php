<?php
/* ==========================================================
   admin/dashboard.php - Futuristic Admin Cockpit Dashboard
   ========================================================== */
include __DIR__ . '/admin_header.php';

$movie_count   = $conn->query("SELECT COUNT(*) c FROM movies")->fetch_assoc()['c'];
$theater_count = $conn->query("SELECT COUNT(*) c FROM theaters")->fetch_assoc()['c'];
$show_count    = $conn->query("SELECT COUNT(*) c FROM shows")->fetch_assoc()['c'];
$user_count    = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$booking_count = $conn->query("SELECT COUNT(*) c FROM bookings")->fetch_assoc()['c'];
$revenue       = $conn->query("SELECT COALESCE(SUM(total_amount),0) r FROM bookings WHERE status='Confirmed'")->fetch_assoc()['r'];

// Revenue for the last 14 days
$daily = $conn->query("
    SELECT DATE(booking_date) d, COALESCE(SUM(total_amount),0) r
    FROM bookings
    WHERE status = 'Confirmed' AND booking_date >= (CURDATE() - INTERVAL 13 DAY)
    GROUP BY DATE(booking_date)
");
$daily_map = [];
while ($row = $daily->fetch_assoc()) { $daily_map[$row['d']] = (float)$row['r']; }
$chart_labels = [];
$chart_values = [];
for ($i = 13; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i day"));
    $chart_labels[] = date('d M', strtotime($d));
    $chart_values[] = $daily_map[$d] ?? 0;
}
?> <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;"> <div> <h1 style="margin-bottom:4px;">Cinema Analytics Cockpit</h1> </div>
</div> <div class="stats-grid"> <div class="stat-card"> <div class="stat-number" style="color:var(--neon-cyan);"><?php echo $movie_count; ?></div> <div class="stat-label">Total Active Movies</div> </div> <div class="stat-card"> <div class="stat-number" style="color:var(--neon-purple);"><?php echo $theater_count; ?></div> <div class="stat-label">Partner Theaters</div> </div> <div class="stat-card"> <div class="stat-number" style="color:var(--neon-blue);"><?php echo $show_count; ?></div> <div class="stat-label">Scheduled Shows</div> </div> <div class="stat-card"> <div class="stat-number" style="color:var(--neon-amber);"><?php echo $user_count; ?></div> <div class="stat-label">Registered Members</div> </div> <div class="stat-card"> <div class="stat-number" style="color:var(--neon-pink);"><?php echo $booking_count; ?></div> <div class="stat-label">Total Bookings</div> </div> <div class="stat-card"> <div class="stat-number" style="color:var(--neon-green);font-size:26px;">Rs. <?php echo number_format($revenue, 2); ?></div> <div class="stat-label">Gross Revenue</div> </div>
</div> <h2 style="margin-top:36px;margin-bottom:16px;">Revenue Trend (Last 14 Days)</h2>
<div class="form-box" style="margin-left:0;max-width:100%;"> <canvas id="revenueChart" height="90"></canvas>
</div> <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'Revenue (Rs.)',
            data: <?php echo json_encode($chart_values); ?>,
            borderColor: '#e50914',
            backgroundColor: 'rgba(229, 9, 20, 0.18)',
            tension: 0.35,
            fill: true,
            pointBackgroundColor: '#f59e0b',
            pointBorderColor: '#ffffff',
            pointRadius: 4
        }]
    },
    options: {
        plugins: { legend: { labels: { color: '#f3f5fa', font: { family: 'Plus Jakarta Sans', weight: 'bold' } } } },
        scales: {
            x: { ticks: { color: '#9da6be' }, grid: { color: 'rgba(255,255,255,0.06)' } },
            y: { ticks: { color: '#9da6be' }, grid: { color: 'rgba(255,255,255,0.06)' } }
        }
    }
});
</script> <h2 style="margin-top:36px;margin-bottom:16px;">Quick Actions</h2>
<div style="display:grid;grid-template-columns: repeat(auto-fill, minmax(180px,1fr));gap:16px;"> <a class="stat-card" href="bookings.php" style="text-decoration:none !important;padding:20px;transition:transform 0.2s;"><div style="font-size:24px;margin-bottom:6px;"></div><strong style="color:var(--text-primary);">Manage Bookings</strong></a> <a class="stat-card" href="movies.php" style="text-decoration:none !important;padding:20px;transition:transform 0.2s;"><div style="font-size:24px;margin-bottom:6px;"></div><strong style="color:var(--text-primary);">Manage Movies</strong></a> <a class="stat-card" href="shows.php" style="text-decoration:none !important;padding:20px;transition:transform 0.2s;"><div style="font-size:24px;margin-bottom:6px;"></div><strong style="color:var(--text-primary);">Manage Shows</strong></a> <a class="stat-card" href="reviews.php" style="text-decoration:none !important;padding:20px;transition:transform 0.2s;"><div style="font-size:24px;margin-bottom:6px;"></div><strong style="color:var(--text-primary);">Moderate Reviews</strong></a> <a class="stat-card" href="coupons.php" style="text-decoration:none !important;padding:20px;transition:transform 0.2s;"><div style="font-size:24px;margin-bottom:6px;"></div><strong style="color:var(--text-primary);">Manage Coupons</strong></a>
</div> <?php include __DIR__ . '/admin_footer.php'; ?>
