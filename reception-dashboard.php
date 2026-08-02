<?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'receptionist') {
    header('Location: index.php');
    exit();
}

// Get statistics
$sql_total_bookings = "SELECT COUNT(*) as total FROM bookings";
$total_bookings = $conn->query($sql_total_bookings)->fetch_assoc();

$sql_checked_in = "SELECT COUNT(*) as total FROM bookings WHERE status='Checked-in'";
$checked_in = $conn->query($sql_checked_in)->fetch_assoc();

$sql_available = "SELECT COUNT(*) as total FROM rooms WHERE status='Available'";
$available = $conn->query($sql_available)->fetch_assoc();

// Today's check-ins
$sql_today = "SELECT b.*, r.room_number, c.full_name 
              FROM bookings b 
              JOIN rooms r ON b.room_id = r.id 
              JOIN customers c ON b.customer_id = c.id 
              WHERE b.check_in = CURDATE() AND b.status != 'Cancelled'";
$today_checkins = $conn->query($sql_today);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    Receptionist Dashboard</title>
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
                    <small>Reception</small>
                </div>
            </div>
            <nav class="sidebar-nav">
    <a href="reception-dashboard.php" class="nav-link"><i class="fas fa-chart-pie"></i><span>Dashboard</span></a>
    <a href="reception-checkin.php" class="nav-link"><i class="fas fa-sign-in-alt"></i><span>Check In</span></a>
    <a href="reception-checkout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i><span>Check Out</span></a>
    <a href="reception-bookings.php" class="nav-link active"><i class="fas fa-calendar-check"></i><span>Bookings</span></a>
    <a href="booking-add.php" class="nav-link"><i class="fas fa-plus"></i><span>New Booking</span></a>
<a href="reception-rooms.php" class="nav-link"><i class="fas fa-bed"></i><span>Rooms</span></a></nav>
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
            <span><?php echo $_SESSION['full_name']; ?></span>
            <small>Receptionist</small>
        </div>
    </div>
</div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h1>📊 Reception Dashboard</h1>
                <div class="top-bar-right">
               <?php echo language_switcher(); ?>
                 <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
                <span style="color: var(--gray); font-size:14px;"><?php echo date('l, F j, Y'); ?></span>
</div>
            </header>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">📋</div>
                    <div class="stat-info">
                        <h3><?php echo $total_bookings['total']; ?></h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">✅</div>
                    <div class="stat-info">
                        <h3><?php echo $checked_in['total']; ?></h3>
                        <p>Checked In</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon gold">🛏️</div>
                    <div class="stat-info">
                        <h3><?php echo $available['total']; ?></h3>
                        <p>Available Rooms</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>📋 Today's Check-ins</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Room</th>
                                <th>Customer</th>
                                <th>Check In</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($today_checkins->num_rows > 0): ?>
                                <?php while($row = $today_checkins->fetch_assoc()): ?>
                                    <tr>
                                        <td><span class="badge room"><?php echo $row['room_number']; ?></span></td>
                                        <td><?php echo $row['full_name']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['check_in'])); ?></td>
                                        <td>
                                            <a href="reception-checkin.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Check In
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align:center; padding:30px; color:#999;">No check-ins today</td>
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