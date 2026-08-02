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
$room_id = isset($_GET['room_id']) ? $_GET['room_id'] : 0;
$selected_room = null;

if ($room_id > 0) {
    $sql = "SELECT * FROM rooms WHERE id = $room_id AND status = 'Available'";
    $selected_room = $conn->query($sql)->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $adults = $_POST['adults'];
    $children = $_POST['children'];
    
    // Calculate days and total
    $date1 = new DateTime($check_in);
    $date2 = new DateTime($check_out);
    $days = $date1->diff($date2)->days;
    
    $sql_price = "SELECT price FROM rooms WHERE id = $room_id";
    $price_result = $conn->query($sql_price);
    $price_row = $price_result->fetch_assoc();
    $total_amount = $price_row['price'] * $days;
    
    $sql = "INSERT INTO bookings (room_id, customer_id, check_in, check_out, adults, children, total_amount, status) 
            VALUES ('$room_id', '$customer_id', '$check_in', '$check_out', '$adults', '$children', '$total_amount', 'Pending')";
    
    if ($conn->query($sql)) {
        $update_room = "UPDATE rooms SET status = 'Booked' WHERE id = $room_id";
        $conn->query($update_room);
        $message = "✅ Booking request submitted! Waiting for confirmation.";
    } else {
        $error = "❌ Error: " . $conn->error;
    }
}

// Get all rooms for dropdown
$sql_rooms = "SELECT id, room_number, room_type, price FROM rooms WHERE status = 'Available'";
$rooms = $conn->query($sql_rooms);
?>
<!DOCTYPE html>
<html>
 <head>
      
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    Book Room - Hotel</title>
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
                <h1>📅 Book a Room</h1>
                <div class="top-bar-right">
               <?php echo language_switcher(); ?>
                 <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
 
                <a href="customer-rooms.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> ⬅Back to Rooms</a>
</div>
            </header>
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($selected_room): ?>
                <div class="card">
                    <h3>📝 Booking Details</h3>
                    <p><strong>Room:</strong> <?php echo $selected_room['room_number']; ?> (<?php echo $selected_room['room_type']; ?>)</p>
                    <p><strong>Price:</strong> <?php echo CURRENCY . number_format($selected_room['price'], 2); ?> / night</p>
                </div>
            <?php endif; ?>

            <div class="card">
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Select Room *</label>
                            <select name="room_id" required>
                                <option value="">-- Select Room --</option>
                                <?php while($row = $rooms->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $room_id) ? 'selected' : ''; ?>>
                                        <?php echo $row['room_number']; ?> - <?php echo $row['room_type']; ?> (<?php echo CURRENCY . number_format($row['price'], 2); ?>/night)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Check In Date *</label>
                            <input type="date" name="check_in" required>
                        </div>
                        <div class="form-group">
                            <label>Check Out Date *</label>
                            <input type="date" name="check_out" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Adults *</label>
                            <input type="number" name="adults" value="1" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Children</label>
                            <input type="number" name="children" value="0" min="0">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-calendar-check"></i> Submit Booking</button>
                </form>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>