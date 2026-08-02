<?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'] ?? 0;
$sql = "SELECT * FROM rooms WHERE id = $id";
$room = $conn->query($sql)->fetch_assoc();

if (!$room) {
    header('Location: rooms.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $price = $_POST['price'];
    $capacity = $_POST['capacity'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    
    $update = "UPDATE rooms SET 
                room_number='$room_number', 
                room_type='$room_type', 
                price='$price', 
                capacity='$capacity', 
                status='$status', 
                description='$description' 
                WHERE id=$id";
    
    if ($conn->query($update)) {
        echo "<script>alert('✅ Room updated successfully!'); window.location.href='rooms.php';</script>";
    } else {
        echo "<script>alert('❌ Error: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    Edit Room - Hotel Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php echo language_switcher(); ?>
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
                <a href="dashboard.php" class="nav-link"><i class="fas fa-chart-pie"></i><span>Dashboard</span></a>
                <a href="rooms.php" class="nav-link active"><i class="fas fa-bed"></i><span>Rooms</span></a>
                <a href="bookings.php" class="nav-link"><i class="fas fa-calendar-check"></i><span>Bookings</span></a>
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

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1>✏️ Edit Room</h1>
                <a href="rooms.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </header>

            <div class="card">
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Room Number *</label>
                            <input type="text" name="room_number" value="<?php echo $room['room_number']; ?>" required>
                            </div>
                        <div class="form-group">
                            <label>Room Type *</label>
                            <select name="room_type" required>
                                <?php 
                                $types = ['Single', 'Double', 'Suite', 'Deluxe', 'Presidential'];
                                foreach ($types as $type) {
                                    $selected = ($room['room_type'] == $type) ? 'selected' : '';
                                    echo "<option value='$type' $selected>$type</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Price per Night *</label>
                            <input type="number" name="price" step="0.01" value="<?php echo $room['price']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Capacity (Guests) *</label>
                            <input type="number" name="capacity" value="<?php echo $room['capacity']; ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Status *</label>
                            <select name="status" required>
                                <?php 
                                $statuses = ['Available', 'Booked', 'Maintenance', 'Cleaning'];
                                foreach ($statuses as $status) {
                                    $selected = ($room['status'] == $status) ? 'selected' : '';
                                    echo "<option value='$status' $selected>$status</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="3"><?php echo $room['description']; ?></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Room</button>
                </form>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>