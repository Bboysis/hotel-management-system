 <?php
require_once 'config.php';
require_once 'language_helper.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$message = '';
$error = '';

// Get all payments with customer and room info
$sql_payments = "SELECT p.*, c.full_name as customer_name, c.email, c.phone, 
                 r.room_number, r.room_type, 
                 b.check_in, b.check_out 
                 FROM payments p 
                 LEFT JOIN customers c ON p.customer_id = c.id 
                 LEFT JOIN rooms r ON p.room_id = r.id 
                 LEFT JOIN bookings b ON p.booking_id = b.id 
                 ORDER BY p.payment_date DESC";
$payments = $conn->query($sql_payments);

// Payment Summary
$sql_summary = "SELECT 
                SUM(CASE WHEN status='Paid' THEN amount ELSE 0 END) as total_paid,
                SUM(CASE WHEN status='Pending' THEN amount ELSE 0 END) as total_pending,
                COUNT(*) as total_transactions
                FROM payments";
$summary = $conn->query($sql_summary)->fetch_assoc();

// Get customers for dropdown
$sql_customers = "SELECT id, full_name FROM customers ORDER BY full_name";
$customers = $conn->query($sql_customers);

// Get rooms for dropdown
$sql_rooms = "SELECT id, room_number, room_type, price FROM rooms";
$rooms = $conn->query($sql_rooms);

// Get bookings for dropdown
$sql_bookings = "SELECT b.id, r.room_number, c.full_name 
                 FROM bookings b 
                 JOIN rooms r ON b.room_id = r.id 
                 JOIN customers c ON b.customer_id = c.id 
                 WHERE b.status != 'Cancelled'";
$bookings = $conn->query($sql_bookings);

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];
    $room_id = $_POST['room_id'];
    $booking_id = $_POST['booking_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $reference_number = $_POST['reference_number'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    
    $sql = "INSERT INTO payments (customer_id, room_id, booking_id, amount, payment_method, reference_number, description, status) 
            VALUES ('$customer_id', '$room_id', '$booking_id', '$amount', '$payment_method', '$reference_number', '$description', '$status')";
    
    if ($conn->query($sql)) {
        // If payment is paid, update booking status if applicable
        if ($status == 'Paid' && $booking_id > 0) {
            $update_booking = "UPDATE bookings SET status = 'Confirmed' WHERE id = $booking_id";
            $conn->query($update_booking);
        }
        $message = "✅ Payment recorded successfully!";
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
                <a href="payments.php" class="nav-link active"><i class="fas fa-credit-card"></i><span>Payments</span></a>
                <a href="reports.php" class="nav-link"><i class="fas fa-file-alt"></i><span>Reports</span></a>
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
                <h1>💰 Payment Management</h1>
                <div class="topbar-actions">
                    <a href="#addPayment" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Record Payment
                    </a>
                   <div class="top-bar-right">
               <?php echo language_switcher(); ?>
                 <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
                </div>
            </header>

            <!-- Summary Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon gold">💰</div>
                    <div class="stat-info">
                        <h3><?php echo CURRENCY . number_format($summary['total_paid'] ?? 0, 2); ?></h3>
                        <p>Total Paid</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red">⏳</div>
                    <div class="stat-info">
                        <h3><?php echo CURRENCY . number_format($summary['total_pending'] ?? 0, 2); ?></h3>
                        <p>Total Pending</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue">📋</div>
                    <div class="stat-info">
                        <h3><?php echo $summary['total_transactions'] ?? 0; ?></h3>
                        <p>Transactions</p>
                    </div>
                </div>
            </div>

            <!-- Add Payment Form -->
            <div id="addPayment" class="card">
                <h3>📝 Record New Payment</h3>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Customer *</label>
                            <select name="customer_id" required>
                                <option value="">-- Select Customer --</option>
                                <?php while($row = $customers->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['full_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Room *</label>
                            <select name="room_id" required>
                                <option value="">-- Select Room --</option>
                                <?php while($row = $rooms->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        <?php echo $row['room_number']; ?> - <?php echo $row['room_type']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Booking (Optional)</label>
                            <select name="booking_id">
                                <option value="">-- Select Booking --</option>
                                <?php while($row = $bookings->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        #<?php echo $row['id']; ?> - <?php echo $row['room_number']; ?> (<?php echo $row['full_name']; ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount *</label>
                            <input type="number" name="amount" step="0.01" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Payment Method *</label>
                            <select name="payment_method" required>
                                <option value="Cash">Cash</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Mobile Money">Mobile Money</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status *</label>
                            <select name="status" required>
                                <option value="Paid">Paid</option>
                                <option value="Pending">Pending</option>
                                <option value="Refunded">Refunded</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Reference Number</label>
                            <input type="text" name="reference_number" placeholder="e.g., TXN-2026-001">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" name="description" placeholder="Payment description">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Record Payment</button>
                </form>
            </div>

            <!-- Payment History -->
            <div class="card">
                <h3>📋 Payment History</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Room</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($payments->num_rows > 0): ?>
                                <?php while($row = $payments->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo $row['customer_name'] ?? 'N/A'; ?></td>
                                        <td><?php echo $row['room_number'] ?? 'N/A'; ?></td>
                                        <td><?php echo CURRENCY . number_format($row['amount'], 2); ?></td>
                                        <td><?php echo $row['payment_method']; ?></td>
                                        <td><?php echo $row['reference_number'] ?: '-'; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['payment_date'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align:center; padding:30px; color:#999;">No payments recorded</td>
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