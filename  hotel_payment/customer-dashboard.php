<?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header('Location: customer-login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Get customer info
$sql_customer = "SELECT * FROM customers WHERE id = $customer_id";
$customer = $conn->query($sql_customer)->fetch_assoc();

// Get customer bookings
$sql_bookings = "SELECT b.*, r.room_number, r.room_type, r.price 
                 FROM bookings b 
                 JOIN rooms r ON b.room_id = r.id 
                 WHERE b.customer_id = $customer_id 
                 ORDER BY b.booking_date DESC";
$bookings = $conn->query($sql_bookings);

// Get available rooms count
$sql_rooms = "SELECT COUNT(*) as total FROM rooms WHERE status='Available'";
$available_rooms = $conn->query($sql_rooms)->fetch_assoc();

// Get total bookings count
$total_bookings = $bookings->num_rows;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    Customer Dashboard - Hotel Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
     
    <div class="wrapper">
        <!-- Sidebar -->
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
     
    <a href="room-service.php" class="nav-link"><i class="fas fa-utensils"></i><span><?php echo __('room_service'); ?></span></a>
    
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

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1>👋 Welcome, <?php echo $_SESSION['full_name']; ?>!</h1>
                 <div class="top-bar-right">
               <?php echo language_switcher(); ?>
                 <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
 
                <span style="color: var(--gray); font-size:14px;"><?php echo date('l, F j, Y'); ?></span>
               
</div>
            </header>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon gold">📋</div>
                    <div class="stat-info">
                        <h3><?php echo $total_bookings; ?></h3>
                        <p>My Bookings</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">🛏️</div>
                    <div class="stat-info">
                        <h3><?php echo $available_rooms['total']; ?></h3>
                        <p>Available Rooms</p>
                        </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue">⭐</div>
                    <div class="stat-info">
                        <h3>4.5</h3>
                        <p>Hotel Rating</p>
                    </div>
                </div>
            </div>

            <!-- My Bookings -->
            <div class="card">
                <h3>📋 My Bookings</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Room</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($bookings->num_rows > 0): ?>
                                <?php while($row = $bookings->fetch_assoc()): ?>
                                    <tr>
                                        <td><span class="badge room"><?php echo $row['room_number']; ?></span></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['check_in'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['check_out'])); ?></td>
                                        <td><?php echo CURRENCY . number_format($row['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding:30px; color:#999;">
                                        <i class="fas fa-calendar fa-2x" style="display:block; margin-bottom:10px;"></i>
                                        No bookings yet. <a href="customer-book.php" style="color:var(--gold);">Book now!</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>