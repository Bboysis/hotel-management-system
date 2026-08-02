<?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'] ?? 0;

// Get room_id before deleting
$sql = "SELECT room_id FROM bookings WHERE id = $id";
$result = $conn->query($sql);
$booking = $result->fetch_assoc();

if ($booking) {
    $room_id = $booking['room_id'];
    
    // Delete booking
    $delete = "DELETE FROM bookings WHERE id = $id";
    if ($conn->query($delete)) {
        // Update room status back to Available
        $update = "UPDATE rooms SET status = 'Available' WHERE id = $room_id";
        $conn->query($update);
        header('Location: bookings.php?deleted=1');
    } else {
        header('Location: bookings.php?error=1');
    }
} else {
    header('Location: bookings.php');
}
?>