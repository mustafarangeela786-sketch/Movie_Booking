<?php
/* Destroys the visitor session and returns to the home page. */
require_once __DIR__ . '/includes/functions.php';
unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_picture']);
session_destroy();
redirect('index.php');
?>
