 <?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header('Location: customer-login.php');
    exit();
}

$sql_rooms = "SELECT * FROM rooms ORDER BY room_number";
$rooms = $conn->query($sql_rooms);
?>
<!DOCTYPE html>
<html>
 <head>
      
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    Rooms - Customer</title>
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
                <h1>🛏️ Available Rooms</h1>
                <div class="top-bar-right">
               <?php echo language_switcher(); ?>
                 <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
</div>
            </header>

            <div class="room-grid">
                <?php if ($rooms->num_rows > 0): ?>
                    <?php while($row = $rooms->fetch_assoc()): 
                        $status_class = strtolower($row['status']);
                        $is_available = ($row['status'] == 'Available');
                        
                        // Check if image exists
                        $image_path = '';
                        if (!empty($row['image']) && file_exists($row['image'])) {
                            $image_path = $row['image'];
                        } else {
                            // Use placeholder if no image
                            $image_path = 'assets/images/room-placeholder.jpg';
                        }
                    ?>
                        <div class="room-card">
                            <div class="room-image" style="background-image: url('<?php echo $image_path; ?>'); background-size: cover; background-position: center; height: 180px; position: relative;">
                                <span class="room-number" style="position: absolute; top: 10px; left: 15px; background: rgba(0,0,0,0.6); padding: 5px 15px; border-radius: 8px; font-size: 20px; color: white;"><?php echo $row['room_number']; ?></span>
                                <span class="room-price" style="position: absolute; bottom: 10px; right: 15px; background: rgba(0,0,0,0.7); padding: 5px 15px; border-radius: 8px; color: white;">
                                    <?php echo CURRENCY . number_format($row['price'], 2); ?><small style="font-size:10px;">/night</small>
                                </span>
                            </div>
                            <div class="room-body">
                                <h3><?php echo $row['room_type']; ?></h3>
                                <p><?php echo $row['description'] ?: 'Comfortable room with all amenities.'; ?></p>
                                <div class="room-meta">
                                    <span><i class="fas fa-user"></i> <?php echo $row['capacity']; ?> Guests</span>
                                    <span class="status-badge status-<?php echo $status_class; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </div>
                                <?php if ($is_available): ?>
                                    <a href="customer-book.php?room_id=<?php echo $row['id']; ?>" class="btn btn-primary" style="width:100%; text-align:center;">
                                        <i class="fas fa-calendar-plus"></i> Book Now
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary" style="width:100%; text-align:center; cursor:not-allowed; opacity:0.6;" disabled>
                                        <i class="fas fa-times"></i> Not Available
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-bed fa-4x"></i>
                        <h3>No Rooms Available</h3>
                        <p>Please check back later.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>