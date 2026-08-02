<?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'receptionist') {
    header('Location: index.php');
    exit();
}

$message = '';
$error = '';

// Handle check-out
if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    
    // Get booking details
    $sql = "SELECT b.*, r.room_number, c.full_name 
            FROM bookings b 
            JOIN rooms r ON b.room_id = r.id 
            JOIN customers c ON b.customer_id = c.id 
            WHERE b.id = $booking_id";
    $booking = $conn->query($sql)->fetch_assoc();
    
    if ($booking) {
        // Update booking status to Checked-out
        $update = "UPDATE bookings SET status = 'Checked-out' WHERE id = $booking_id";
        if ($conn->query($update)) {
            // Update room status to Available
            $update_room = "UPDATE rooms SET status = 'Available' WHERE id = " . $booking['room_id'];
            $conn->query($update_room);
            $message = "✅ Guest checked out successfully!";
        } else {
            $error = "❌ Error checking out: " . $conn->error;
        }
    } else {
        $error = "❌ Booking not found!";
    }
}

// Get checked-in guests
$sql_checked_in = "SELECT b.*, r.room_number, c.full_name, c.email, c.phone 
                   FROM bookings b 
                   JOIN rooms r ON b.room_id = r.id 
                   JOIN customers c ON b.customer_id = c.id 
                   WHERE b.status = 'Checked-in'
                   ORDER BY b.check_out ASC";
$checked_in = $conn->query($sql_checked_in);
?>
<!DOCTYPE html>
<html>
 <head>
      
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    Check Out - Reception</title>
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
                <a href="reception-checkout.php" class="nav-link active"><i class="fas fa-sign-out-alt"></i><span>Check Out</span></a>
                <a href="bookings.php" class="nav-link"><i class="fas fa-calendar-check"></i><span>Bookings</span></a>
<a href="reception-rooms.php" class="nav-link"><i class="fas fa-bed"></i><span>Rooms</span></a>            </nav>
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
                <h1>🚪 Check Out Guests</h1>
                <div class="top-bar-right">
               <?php echo language_switcher(); ?>
                 <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
                <span style="color: var(--gray); font-size:14px;"><?php echo date('l, F j, Y'); ?></span>
</div>
            </header>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="card">
                <h3>📋 Guests Currently Checked In</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Room</th>
                                <th>Guest</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($checked_in->num_rows > 0): ?>
                                <?php while($row = $checked_in->fetch_assoc()): ?>
                                    <tr>
                                        <td><span class="badge room"><?php echo $row['room_number']; ?></span></td>
                                        <td><?php echo $row['full_name']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['check_in'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['check_out'])); ?></td>
                                        <td>
                                            <a href="reception-checkout.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Check out <?php echo $row['full_name']; ?>?')">
                                                <i class="fas fa-sign-out-alt"></i> Check Out
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding:30px; color:#999;">
                                        <i class="fas fa-bed fa-2x" style="display:block; margin-bottom:10px;"></i>
                                        No guests currently checked in
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