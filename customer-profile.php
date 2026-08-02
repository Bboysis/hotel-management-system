<?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header('Location: customer-login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
$message = '';
$error = '';

$sql = "SELECT * FROM customers WHERE id = $customer_id";
$customer = $conn->query($sql)->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    $update = "UPDATE customers SET 
                full_name='$full_name', 
                email='$email', 
                phone='$phone', 
                address='$address' 
                WHERE id = $customer_id";
    
    if ($conn->query($update)) {
        $_SESSION['full_name'] = $full_name;
        $message = "✅ Profile updated successfully!";
        $customer = $conn->query($sql)->fetch_assoc();
    } else {
        $error = "❌ Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
 <head>
      
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    My Profile - Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
     <div class="wrapper">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <span class="brand-icon">🏨</span>
                <div>
                    <h2>Hotel</h2>
                    <small>Customer Portal</small>
                </div>
            </div>
            <nav class="sidebar-nav">
    <a href="customer-dashboard.php" class="nav-link active"><i class="fas fa-chart-pie"></i><span><?php echo __('dashboard'); ?></span></a>
    <a href="customer-rooms.php" class="nav-link"><i class="fas fa-bed"></i><span><?php echo __('rooms'); ?></span></a>
    <a href="customer-book.php" class="nav-link"><i class="fas fa-calendar-plus"></i><span><?php echo __('book_now'); ?></span></a>
    <a href="customer-profile.php" class="nav-link"><i class="fas fa-user"></i><span><?php echo __('profile'); ?></span></a>
    <a href="customer-reviews.php" class="nav-link"><i class="fas fa-star"></i><span><?php echo __('reviews'); ?></span></a>
    <!-- 👇 ADD THIS LINE 👇 -->
    <a href="room-service.php" class="nav-link"><i class="fas fa-utensils"></i><span><?php echo __('room_service'); ?></span></a>
    <!-- 👆 ADD THIS LINE 👆 -->
</nav>
           <div class="sidebar-footer">
    <!-- 👇 LOGOUT MOVED UP 👇 -->
    <a href="logout.php" class="logout-btn" style="
        display: flex; 
        align-items: center; 
        gap: 10px; 
        color: #ef4444; 
        text-decoration: none; 
        font-weight: 600; 
        font-size: 14px; 
        padding: 8px 12px; 
        background: rgba(239, 68, 68, 0.1); 
        border-radius: 8px; 
        margin-bottom: 12px;
        transition: 0.3s;
    " onmouseover="this.style.background='rgba(239,68,68,0.2)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">
        <i class="fas fa-sign-out-alt"></i>
        <span><?php echo __('logout'); ?></span>
    </a>
    <!-- 👆 LOGOUT MOVED UP 👆 -->
    
    <div class="user-info">
        <i class="fas fa-user-circle"></i>
        <div>
            <span><?php echo $_SESSION['full_name'] ?? 'Admin'; ?></span>
            <small><?php echo $_SESSION['role'] ?? 'Admin'; ?></small>
        </div>
    </div>
</div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h1>👤 My Profile</h1>
                  <div class="top-bar-right">
               <?php echo language_switcher(); ?>
                 <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
 
                <a href="customer-dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> ↩Back</a>
</div>
            </header>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="card">
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="full_name" value="<?php echo $customer['full_name']; ?>" required>
                            </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" value="<?php echo $customer['email']; ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" value="<?php echo $customer['phone']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" value="<?php echo $customer['address']; ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Profile</button>
                </form>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>