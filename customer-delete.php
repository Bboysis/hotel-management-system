<?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'] ?? 0;

// Check if customer has bookings
$check = "SELECT COUNT(*) as total FROM bookings WHERE customer_id = $id";
$result = $conn->query($check);
$count = $result->fetch_assoc();

if ($count['total'] > 0) {
    // Customer has bookings - can't delete
    header('Location: customers.php?error=has_bookings');
} else {
    // Delete customer
    $delete = "DELETE FROM customers WHERE id = $id";
    if ($conn->query($delete)) {
        header('Location: customers.php?deleted=1');
    } else {
        header('Location: customers.php?error=1');
    }
}
?>