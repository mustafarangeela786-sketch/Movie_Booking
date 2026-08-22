<?php
/* ==========================================================
   Shared Header & Navigation Bar
   Modern UI/UX Designer-Crafted Interface
   ========================================================== */
require_once __DIR__ . '/functions.php';
$is_admin = (basename(dirname($_SERVER['PHP_SELF'])) === 'admin');
$asset_base = $is_admin ? '../' : '';
$css_path = __DIR__ . '/../assets/css/style.css';
$css_version = file_exists($css_path) ? filemtime($css_path) : time();
$logo_path = __DIR__ . '/../assets/img/logo.svg';
$logo_version = file_exists($logo_path) ? filemtime($logo_path) : time();
?>
<!DOCTYPE html>
<html lang="en">
<head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>MovieBook &mdash; Premier Cinema Experience</title> <!-- Modern High-End Typography --> <link rel="preconnect" href="https://fonts.googleapis.com"> <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet"> <!-- Bootstrap 5 & Custom Style --> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css"> <link rel="stylesheet" href="<?php echo $asset_base; ?>assets/css/style.css?v=<?php echo $css_version; ?>"> <script> (function () {
            try {
                if (localStorage.getItem('theme') === 'light') {
                    document.documentElement.classList.add('light-theme');
                }
            } catch (e) {}
        })(); </script>
</head>
<body<?php echo (!empty($GLOBALS['page_genre'])) ? ' data-genre="' . htmlspecialchars($GLOBALS['page_genre'], ENT_QUOTES) . '"' : ''; ?>> <header class="navbar navbar-expand-lg"> <div class="nav-inner container-fluid"> <!-- Brand & Logo --> <div class="nav-left"> <a href="<?php echo $asset_base; ?>index.php" class="brand"> <div class="brand-logo-wrap"> <img src="<?php echo $asset_base; ?>assets/img/logo.svg?v=<?php echo $logo_version; ?>" alt="MovieBook" class="brand-logo"> </div> <div class="brand-text"> <span class="bt-main">MovieBook</span> <span class="bt-sub">Cinemas</span> </div> </a> <!-- Theme Toggle Button --> <button type="button" class="theme-toggle" id="themeToggleBtn" aria-label="Toggle theme" title="Toggle Light/Dark Theme"> <span class="theme-label theme-label-dark">Dark</span> <span class="theme-label theme-label-light">Light</span> </button> </div> <!-- Sleek Search Bar --> <div class="nav-search-wrap"> <form action="<?php echo $asset_base; ?>index.php" method="get" class="nav-search" role="search"> <input type="text" name="q" placeholder="Search movies, genres, theaters..." aria-label="Search movies" autocomplete="off"
                       value="<?php echo isset($_GET['q']) ? clean($_GET['q']) : ''; ?>"> </form> </div> <script> // Industry filter always routes to the homepage catalog, from any page.
                function mvGoToIndustry(value) {
                    var base = <?php echo json_encode($asset_base); ?> + 'index.php';
                    var u = new URL(base, window.location.href);
                    if (value) { u.searchParams.set('industry', value); }
                    window.location.href = u.toString();
                } </script> <!-- Mobile Toggler --> <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> </button> <!-- Nav Links & Auth --> <nav class="collapse navbar-collapse navbar-nav-custom" id="navMain"> <a href="<?php echo $asset_base; ?>index.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">Movies</a> <a href="<?php echo $asset_base; ?>about.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) === 'about.php' ? 'active' : ''; ?>">About</a> <a href="<?php echo $asset_base; ?>contact.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) === 'contact.php' ? 'active' : ''; ?>">Theaters</a> <?php if (isLoggedIn()): ?> <a href="<?php echo $asset_base; ?>my_bookings.php" class="nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) === 'my_bookings.php' ? 'active' : ''; ?>">My Tickets</a> <a href="<?php echo $asset_base; ?>my_profile.php" class="user-pill <?php echo basename($_SERVER['PHP_SELF']) === 'my_profile.php' ? 'is-active' : ''; ?>"> <span class="user-avatar-wrap"> <?php if (!empty($_SESSION['user_picture'])): ?> <img src="<?php echo clean($_SESSION['user_picture']); ?>" alt="" class="user-avatar" referrerpolicy="no-referrer" onerror="this.style.display='none';"> <?php else: ?> <?php echo clean(mb_strtoupper(mb_substr($_SESSION['user_name'], 0, 1))); ?> <?php endif; ?> </span> <span><?php echo clean(explode(' ', $_SESSION['user_name'])[0]); ?></span> </a> <a href="<?php echo $asset_base; ?>logout.php" class="nav-link-custom nav-logout-btn">Logout</a> <?php else: ?> <button type="button" class="nav-auth-btn login-btn" onclick="openAuth('login')">Log In</button> <button type="button" class="nav-auth-btn register-btn" onclick="openAuth('signup')">Sign Up</button> <?php endif; ?> </nav> </div>
</header> <main class="container">
