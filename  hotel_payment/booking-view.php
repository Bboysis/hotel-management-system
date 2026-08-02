<?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'] ?? 0;

$sql = "SELECT b.*, r.room_number, r.room_type, r.price, c.full_name, c.email, c.phone, c.address 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        JOIN customers c ON b.customer_id = c.id 
        WHERE b.id = $id";
$booking = $conn->query($sql)->fetch_assoc();

if (!$booking) {
    header('Location: bookings.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    Booking Details - Hotel</title>
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
                <a href="bookings.php" class="nav-link active"><i class="fas fa-calendar-check"></i><span>Bookings</span></a>
                <a href="customers.php" class="nav-link"><i class="fas fa-users"></i><span>Customers</span></a>
                <a href="payments.php" class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span></a>
                <a href="reports.php" class="nav-link"><i class="fas fa-file-alt"></i><span>Reports</span></a>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <span><?php echo $_SESSION['full_name'] ?? 'Admin'; ?></span>
                        <small><?php echo $_SESSION['role'] ?? 'Admin'; ?></small>
                    </div>
                </div>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h1>📋 Booking Details</h1>
                <a href="bookings.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </header>

            <div class="card">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h4>🛏️ Room Details</h4>
                        <p><strong>Room Number:</strong> <?php echo $booking['room_number']; ?></p>
                        <p><strong>Room Type:</strong> <?php echo $booking['room_type']; ?></p>
                        <p><strong>Price:</strong> <?php echo CURRENCY . number_format($booking['price'], 2); ?>/night</p>
                    </div>
                    <div>
                        <h4>👤 Customer Details</h4>
                        <p><strong>Name:</strong> <?php echo $booking['full_name']; ?></p>
                        <p><strong>Email:</strong> <?php echo $booking['email'] ?: '-'; ?></p>
                        <p><strong>Phone:</strong> <?php echo $booking['phone'] ?: '-'; ?></p>
                    </div>
                </div>
                <hr style="margin: 20px 0; border: 1px solid #eee;">
                <div>
                    <h4>📅 Booking Details</h4>
                    <p><strong>Check In:</strong> <?php echo date('d/m/Y', strtotime($booking['check_in'])); ?></p>
                    <p><strong>Check Out:</strong> <?php echo date('d/m/Y', strtotime($booking['check_out'])); ?></p>
                    <p><strong>Adults:</strong> <?php echo $booking['adults']; ?></p>
                    <p><strong>Children:</strong> <?php echo $booking['children']; ?></p>
                    <p><strong>Total Amount:</strong> <?php echo CURRENCY . number_format($booking['total_amount'], 2); ?></p>
                    <p><strong>Status:</strong> <span class="status-badge status-<?php echo strtolower($booking['status']); ?>"><?php echo $booking['status']; ?></span></p>
                    <?php if ($booking['notes']): ?>
                        <p><strong>Notes:</strong> <?php echo $booking['notes']; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>