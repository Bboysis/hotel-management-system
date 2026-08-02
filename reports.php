<?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Monthly Revenue
$sql_monthly = "SELECT DATE_FORMAT(payment_date, '%b') as month, 
                SUM(amount) as total 
                FROM payments 
                WHERE status='Paid' 
                GROUP BY MONTH(payment_date) 
                ORDER BY MONTH(payment_date) DESC LIMIT 6";
$monthly = $conn->query($sql_monthly);

// Room Type Popularity
$sql_types = "SELECT room_type, COUNT(*) as count 
              FROM rooms 
              GROUP BY room_type 
              ORDER BY count DESC";
$types = $conn->query($sql_types);

// Total Revenue
$sql_revenue = "SELECT SUM(amount) as total FROM payments WHERE status='Paid'";
$total_revenue = $conn->query($sql_revenue)->fetch_assoc();

// Total Bookings
$sql_total_bookings = "SELECT COUNT(*) as total FROM bookings";
$total_bookings = $conn->query($sql_total_bookings)->fetch_assoc();

// Occupancy Rate
$sql_occupied = "SELECT COUNT(*) as total FROM rooms WHERE status='Booked'";
$occupied = $conn->query($sql_occupied)->fetch_assoc();
$sql_rooms = "SELECT COUNT(*) as total FROM rooms";
$total_rooms = $conn->query($sql_rooms)->fetch_assoc();
$occupancy_rate = ($total_rooms['total'] > 0) ? round(($occupied['total'] / $total_rooms['total']) * 100) : 0;
?>
<!DOCTYPE html>
<html>
 <head>
      
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    Reports - Hotel Management</title>
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
                    <small>Management</small>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-link"><i class="fas fa-chart-pie"></i><span>Dashboard</span></a>
                <a href="rooms.php" class="nav-link"><i class="fas fa-bed"></i><span>Rooms</span></a>
                <a href="bookings.php" class="nav-link"><i class="fas fa-calendar-check"></i><span>Bookings</span></a>
                <a href="customers.php" class="nav-link"><i class="fas fa-users"></i><span>Customers</span></a>
                <a href="payments.php" class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span></a>
                <a href="reports.php" class="nav-link active"><i class="fas fa-file-alt"></i><span>Reports</span></a>
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
                <h1>📊 Reports & Analytics</h1>
                <div class="top-bar-right">
               <?php echo language_switcher(); ?>
                 <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
</div>
            </header>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon gold">💰</div>
                    <div class="stat-info">
                        <h3><?php echo CURRENCY . number_format($total_revenue['total'] ?? 0, 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue">📋</div>
                    <div class="stat-info">
                        <h3><?php echo $total_bookings['total'] ?? 0; ?></h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">📊</div>
                    <div class="stat-info">
                        <h3><?php echo $occupancy_rate; ?>%</h3>
                        <p>Occupancy Rate</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red">🛏️</div>
                    <div class="stat-info">
                        <h3><?php echo $total_rooms['total'] ?? 0; ?></h3>
                        <p>Total Rooms</p>
                    </div>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="card">
                <h3>📈 Monthly Revenue</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($monthly->num_rows > 0): ?>
                                <?php while($row = $monthly->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['month']; ?></td>
                                        <td><?php echo CURRENCY . number_format($row['total'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align:center; padding:20px; color:#999;">No data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Room Types -->
            <div class="card">
                <h3>🛏️ Room Type Popularity</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Room Type</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($types->num_rows > 0): ?>
                                <?php while($row = $types->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['room_type']; ?></td>
                                        <td><?php echo $row['count']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align:center; padding:20px; color:#999;">No data</td>
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