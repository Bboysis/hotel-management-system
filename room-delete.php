<?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'] ?? 0;

$sql = "DELETE FROM rooms WHERE id = $id";

if ($conn->query($sql)) {
    header('Location: rooms.php?deleted=1');
} else {
    echo "Error deleting room: " . $conn->error;
}
?>