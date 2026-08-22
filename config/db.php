<?php
/* ==========================================================
   Database Connection File
   Uses MySQLi to connect PHP with the MySQL database.
   Update the credentials below to match your local server
   (e.g. XAMPP / WAMP default is user "root", empty password).
   ========================================================== */

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "movie booking";

// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection and stop execution if it fails
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Force UTF-8 so titles/descriptions display correctly
$conn->set_charset("utf8mb4");
?>
