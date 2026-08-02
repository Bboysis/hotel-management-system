<?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Statistics
$sql_rooms = "SELECT COUNT(*) as total, 
              SUM(CASE WHEN status='Available' THEN 1 ELSE 0 END) as available,
              SUM(CASE WHEN status='Booked' THEN 1 ELSE 0 END) as booked,
              SUM(CASE WHEN status='Maintenance' THEN 1 ELSE 0 END) as maintenance
              FROM rooms";
$rooms_stats = $conn->query($sql_rooms)->fetch_assoc();

$sql_bookings = "SELECT COUNT(*) as total FROM bookings WHERE status IN ('Checked-in', 'Confirmed')";
$active_bookings = $conn->query($sql_bookings)->fetch_assoc();

$sql_customers = "SELECT COUNT(*) as total FROM customers";
$total_customers = $conn->query($sql_customers)->fetch_assoc();

$sql_revenue = "SELECT SUM(amount) as total FROM payments WHERE status='Paid'";
$total_revenue = $conn->query($sql_revenue)->fetch_assoc();

// Recent bookings
$sql_recent = "SELECT b.*, r.room_number, c.full_name 
               FROM bookings b 
               JOIN rooms r ON b.room_id = r.id 
               JOIN customers c ON b.customer_id = c.id 
               ORDER BY b.booking_date DESC LIMIT 5";
$recent = $conn->query($sql_recent);
?>
<!DOCTYPE html>
<html>
 <head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hotel Management</title>
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
                    <small>Management</small>
                </div>
            </div>
           <nav class="sidebar-nav">
     <a href="dashboard.php" class="nav-link"><i class="fas fa-chart-pie"></i><span><?php echo __('dashboard'); ?></span></a>
<a href="rooms.php" class="nav-link"><i class="fas fa-bed"></i><span><?php echo __('rooms'); ?></span></a>
<a href="bookings.php" class="nav-link"><i class="fas fa-calendar-check"></i><span><?php echo __('bookings'); ?></span></a>
<a href="customers.php" class="nav-link"><i class="fas fa-users"></i><span><?php echo __('customers'); ?></span></a>
<a href="payments.php" class="nav-link"><i class="fas fa-credit-card"></i><span><?php echo __('payments'); ?></span></a>
<a href="reports.php" class="nav-link"><i class="fas fa-file-alt"></i><span><?php echo __('reports'); ?></span></a>
<a href="admin-reviews.php" class="nav-link"><i class="fas fa-star"></i><span><?php echo __('reviews'); ?></span></a>
</nav>
            <div class="sidebar-footer">
    <!-- 👇 LOGOUT MOVED UP - ABOVE USER INFO 👇 -->
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
            
            <!-- Top Bar -->
            <header class="topbar">
                <h1>Dashboard Overview</h1>
                <div class="topbar-actions">
                    <div class="top-bar-right">
               <?php echo language_switcher(); ?>
                 <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
                    <span class="date"><?php echo date('l, F j, Y'); ?></span>
</div>
                      
         
                    
              
            </header>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon gold">🏨</div>
                    <div class="stat-info">
                        <h3><?php echo $rooms_stats['total']; ?></h3>
                        <p>Total Rooms</p>
                    </div>
                    <div class="stat-change up">+12%</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue">📋</div>
                    <div class="stat-info">
                        <h3><?php echo $active_bookings['total']; ?></h3>
                        <p>Active Bookings</p>
                    </div>
                    <div class="stat-change up">+5%</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">👤</div>
                    <div class="stat-info">
                        <h3><?php echo $total_customers['total']; ?></h3>
                        <p>Total Customers</p>
                    </div>
                    <div class="stat-change up">+8%</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon gold">💰</div>
                    <div class="stat-info">
                        <h3><?php echo CURRENCY . number_format($total_revenue['total'] ?? 0, 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="stat-change up">+18%</div>
                </div>
            </div>

            <!-- Room Status -->
            <div class="card">
                <h3>🛏️ Room Status</h3>
                <div class="room-status-grid">
                    <div class="status-item available">
                        <span class="dot"></span>
                        <span><?php echo $rooms_stats['available']; ?> Available</span>
                    </div>
                    <div class="status-item booked">
                        <span class="dot"></span>
                        <span><?php echo $rooms_stats['booked']; ?> Booked</span>
                    </div>
                    <div class="status-item maintenance">
                        <span class="dot"></span>
                        <span><?php echo $rooms_stats['maintenance']; ?> Maintenance</span>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-available" style="width: <?php echo ($rooms_stats['total'] > 0) ? ($rooms_stats['available'] / $rooms_stats['total']) * 100 : 0; ?>%;"></div>
                    <div class="progress-booked" style="width: <?php echo ($rooms_stats['total'] > 0) ? ($rooms_stats['booked'] / $rooms_stats['total']) * 100 : 0; ?>%;"></div>
                    <div class="progress-maintenance" style="width: <?php echo ($rooms_stats['total'] > 0) ? ($rooms_stats['maintenance'] / $rooms_stats['total']) * 100 : 0; ?>%;"></div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card">
                <h3>📋 Recent Bookings</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Room</th>
                                <th>Customer</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent->num_rows > 0): ?>
                                <?php while($row = $recent->fetch_assoc()): ?>
                                    <tr>
                                        <td><span class="badge room"><?php echo $row['room_number']; ?></span></td>
                                        <td><?php echo $row['full_name']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['check_in'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['check_out'])); ?></td>
                                        <td>
                                            <span class="badge status-<?php echo strtolower($row['status']); ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align:center; color:#999; padding:30px;">No bookings found</td></tr>
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