<?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$message = '';
$error = '';

// Get rooms
$sql_rooms = "SELECT id, room_number, room_type, price FROM rooms WHERE status = 'Available' OR status = 'Booked'";
$rooms = $conn->query($sql_rooms);

// Get customers
$sql_customers = "SELECT id, full_name FROM customers ORDER BY full_name";
$customers = $conn->query($sql_customers);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $customer_id = $_POST['customer_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $adults = $_POST['adults'];
    $children = $_POST['children'];
    $notes = $_POST['notes'];
    $status = $_POST['status'];
    
    // Calculate days and total
    $date1 = new DateTime($check_in);
    $date2 = new DateTime($check_out);
    $days = $date1->diff($date2)->days;
    
    $sql_price = "SELECT price FROM rooms WHERE id = $room_id";
    $price_result = $conn->query($sql_price);
    $price_row = $price_result->fetch_assoc();
    $total_amount = $price_row['price'] * $days;
    
    $sql = "INSERT INTO bookings (room_id, customer_id, check_in, check_out, adults, children, total_amount, status, notes) 
            VALUES ('$room_id', '$customer_id', '$check_in', '$check_out', '$adults', '$children', '$total_amount', '$status', '$notes')";
    
    if ($conn->query($sql)) {
        // Update room status
        $update_room = "UPDATE rooms SET status = 'Booked' WHERE id = $room_id";
        $conn->query($update_room);
        
        $message = "✅ Booking created successfully!";
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
    Add Booking - Hotel</title>
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
                <h1>➕ New Booking</h1>
                <a href="bookings.php" class="btn btn-secondary">
                    
                <div class="top-bar-right">
               <?php echo language_switcher(); ?>
                 <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
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
                            <label>Select Room *</label>
                            <select name="room_id" required>
                                <option value="">-- Select Room --</option>
                                <?php while($row = $rooms->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        <?php echo $row['room_number']; ?> - <?php echo $row['room_type']; ?> (<?php echo CURRENCY . number_format($row['price'], 2); ?>/night)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select Customer *</label>
                            <select name="customer_id" required>
                                <option value="">-- Select Customer --</option>
                                <?php while($row = $customers->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['full_name']; ?></option>
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
                    <div class="form-row">
                        <div class="form-group">
                            <label>Status *</label>
                            <select name="status" required>
                                <option value="Pending">Pending</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Checked-in">Checked-in</option>
                                <option value="Checked-out">Checked-out</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" placeholder="Special requests..." rows="1"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Booking</button>
                </form>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>