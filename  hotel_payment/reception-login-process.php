<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password' AND role='receptionist'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = 'receptionist';
        $_SESSION['full_name'] = $user['full_name'];
        header('Location: reception-dashboard.php');
        exit();
    } else {
        header('Location: reception-login.php?error=1');
    }
}
?>