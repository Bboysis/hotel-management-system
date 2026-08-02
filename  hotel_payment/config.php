<?php
 session_start();

// Database Configuration
$host = 'localhost:3307';
$username = 'root';
$password = '';
$database = 'hotel_db';

$conn = new mysqli('localhost:3307', 'root', '', 'hotel_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Timezone
date_default_timezone_set('Africa/Addis_Ababa');

// Currency
define('CURRENCY', 'ETB');
define('APP_NAME', '🏨 Hotel Management System');
?>