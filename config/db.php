<?php
$DB_HOST = "sql208.infinityfree.com";
$DB_USER = "if0_42722773";
$DB_PASS = "iF8nXwmOEN4Fb";
$DB_NAME = "if0_42722773_movie_booking";
$conn = new mysqli("sql208.infinityfree.com", "if0_42722773", "iF8nXwmOEN4Fb", "if0_42722773_movie_booking");
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
