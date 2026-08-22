</main> <footer class="site-footer"> <div class="footer-inner"> <div class="footer-brand-col"> <div class="brand-text"> <span class="bt-main">MovieBook</span> <span class="bt-sub">Cinemas</span> </div> <p class="footer-tagline">Your premier destination for blockbuster movies and instant seat reservations.</p> </div> <div class="footer-links-col"> <h4>Quick Links</h4> <a href="index.php">Now Showing</a> <a href="index.php">Coming Soon</a> <a href="about.php">About Us</a> <a href="contact.php">Theaters & Locations</a> </div> <div class="footer-links-col"> <h4>Account</h4> <?php if (isLoggedIn()): ?> <a href="my_bookings.php">My Bookings</a> <a href="my_profile.php">Account Settings</a> <a href="logout.php">Log Out</a> <?php else: ?> <a href="javascript:void(0)" onclick="openAuth('login')">Log In</a> <a href="javascript:void(0)" onclick="openAuth('signup')">Create Account</a> <?php endif; ?> </div> </div> <div class="footer-bottom"> <p>&copy; <?php echo date('Y'); ?> MovieBook Cinemas.</p> </div>
</footer> <?php if (!isLoggedIn()): ?> <?php include __DIR__ . '/auth_modal.php'; ?>
<?php endif; ?> <?php
$is_admin = (basename(dirname($_SERVER['PHP_SELF'])) === 'admin');
$base_js = $is_admin ? '../' : '';
$main_js_path = __DIR__ . '/../assets/js/main.js';
$main_js_ver = file_exists($main_js_path) ? filemtime($main_js_path) : time();
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $base_js; ?>assets/js/main.js?v=<?php echo $main_js_ver; ?>"></script>
</body>
</html>
