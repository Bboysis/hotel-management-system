 <?php
require_once 'config.php';
require_once 'language_helper.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'chef') {
    header('Location: chef-login.php');
    exit();
}

// Get all orders
$sql_all = "SELECT o.*, c.full_name as customer_name, r.room_number 
            FROM room_service_orders o 
            JOIN customers c ON o.customer_id = c.id 
            JOIN rooms r ON o.room_id = r.id 
            ORDER BY o.order_time DESC";
$all_orders = $conn->query($sql_all);

// Get filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'All';

if ($status_filter != 'All') {
    $sql_filter = "SELECT o.*, c.full_name as customer_name, r.room_number 
                   FROM room_service_orders o 
                   JOIN customers c ON o.customer_id = c.id 
                   JOIN rooms r ON o.room_id = r.id 
                   WHERE o.status = '$status_filter'
                   ORDER BY o.order_time DESC";
    $all_orders = $conn->query($sql_filter);
}

// Count by status
$sql_counts = "SELECT status, COUNT(*) as count FROM room_service_orders GROUP BY status";
$counts = $conn->query($sql_counts);
$status_counts = [];
while($row = $counts->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    All Orders - Chef</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 25px;
            background: #1a1a2e;
            border-bottom: 2px solid #d4af37;
        }
        .top-bar h1 {
            color: white;
            font-size: 20px;
            font-family: 'Playfair Display', serif;
            margin: 0;
        }
        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .top-bar-right .date-text {
            color: #aaa;
            font-size: 12px;
        }

        /* Filter Buttons */
        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .filter-buttons a {
            padding: 8px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            background: #f0f0f0;
            color: #333;
            transition: 0.3s;
            border: 2px solid transparent;
        }
        .filter-buttons a:hover {
            background: #ddd;
            transform: scale(1.02);
        }
        .filter-buttons a.active {
            background: #1a237e;
            color: white;
            border-color: #d4af37;
        }
        .filter-buttons a .count-badge {
            background: rgba(0,0,0,0.1);
            padding: 1px 10px;
            border-radius: 12px;
            font-size: 11px;
            margin-left: 5px;
        }
        .filter-buttons a.active .count-badge {
            background: rgba(255,255,255,0.2);
        }

        /* Table Card */
        .order-table-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .order-table-card table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            }
        .order-table-card th {
            text-align: left;
            padding: 12px 15px;
            background: #f5f5f5;
            color: #1a1a2e;
            border-bottom: 2px solid #d4af37;
            font-weight: 700;
            font-size: 13px;
        }
        .order-table-card td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            color: #333;
        }
        .order-table-card tr:hover td {
            background: #f9f9f9;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-Pending { background: #fff3e0; color: #e65100; }
        .status-Preparing { background: #e3f2fd; color: #0d47a1; }
        .status-Delivered { background: #e8f5e9; color: #2e7d32; }
        .status-Completed { background: #e8f5e9; color: #2e7d32; }
        .status-Cancelled { background: #ffebee; color: #c62828; }

        /* Sidebar */
        .sidebar-footer .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            padding: 8px 12px;
            background: rgba(239,68,68,0.1);
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .sidebar-footer .logout-btn:hover {
            background: rgba(239,68,68,0.2);
        }
        .sidebar-footer .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-footer .user-info i {
            font-size: 32px;
            color: #d4af37;
        }
        .sidebar-footer .user-info span {
            display: block;
            font-size: 14px;
            font-weight: 500;
        }
        .sidebar-footer .user-info small {
            font-size: 11px;
            color: rgba(255,255,255,0.4);
        }

        @media (max-width: 768px) {
            .top-bar {
                flex-direction: column;
                gap: 10px;
            }
            .order-table-card {
                overflow-x: auto;
            }
            .order-table-card table {
                font-size: 12px;
                min-width: 600px;
            }
            .filter-buttons {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <span class="brand-icon">👨‍🍳</span>
                <div>
                    <h2>Hotel</h2>
                    <small>Kitchen</small>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="chef-dashboard.php" class="nav-link"><i class="fas fa-chart-pie"></i><span>Dashboard</span></a>
                <a href="chef-orders.php" class="nav-link active"><i class="fas fa-utensils"></i><span>All Orders</span></a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <span><?php echo $_SESSION['full_name']; ?></span>
                        <small>Chef</small>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <h1>📋 All Orders</h1>
                <div class="top-bar-right">
                      
                    <span class="date-text"><?php echo date('d/m/Y'); ?></span>
                     <a href="logout.php" class="btn btn-logout">
                        <i class="fas fa-sign-out-alt"></i>  Logout</a>
                    <?php echo language_switcher(); ?>
                    
                      
                </div>
            </div>
            <div style="padding: 20px;">
                <!-- Filter Buttons -->
                <div class="filter-buttons">
                    <a href="chef-orders.php?status=All" class="<?php echo ($status_filter == 'All') ? 'active' : ''; ?>">
                        📋 All <span class="count-badge"><?php echo array_sum($status_counts); ?></span>
                    </a>
                    <a href="chef-orders.php?status=Pending" class="<?php echo ($status_filter == 'Pending') ? 'active' : ''; ?>">
                        🟡 Pending <span class="count-badge"><?php echo $status_counts['Pending'] ?? 0; ?></span>
                    </a>
                    <a href="chef-orders.php?status=Preparing" class="<?php echo ($status_filter == 'Preparing') ? 'active' : ''; ?>">
                        🔵 Preparing <span class="count-badge"><?php echo $status_counts['Preparing'] ?? 0; ?></span>
                    </a>
                    <a href="chef-orders.php?status=Delivered" class="<?php echo ($status_filter == 'Delivered') ? 'active' : ''; ?>">
                        🟢 Delivered <span class="count-badge"><?php echo $status_counts['Delivered'] ?? 0; ?></span>
                    </a>
                    <a href="chef-orders.php?status=Completed" class="<?php echo ($status_filter == 'Completed') ? 'active' : ''; ?>">
                        ✅ Completed <span class="count-badge"><?php echo $status_counts['Completed'] ?? 0; ?></span>
                    </a>
                    <a href="chef-orders.php?status=Cancelled" class="<?php echo ($status_filter == 'Cancelled') ? 'active' : ''; ?>">
                        ❌ Cancelled <span class="count-badge"><?php echo $status_counts['Cancelled'] ?? 0; ?></span>
                    </a>
                </div>

                <!-- Orders Table -->
                <div class="order-table-card">
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Room</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($all_orders->num_rows > 0): ?>
                                <?php while($row = $all_orders->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo $row['order_number']; ?></strong></td>
                                        <td><?php echo $row['customer_name']; ?></td>
                                        <td>🏠 <?php echo $row['room_number']; ?></td>
                                        <td><?php echo $row['items']; ?></td>
                                        <td><strong>$<?php echo number_format($row['total_amount'], 2); ?></strong></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $row['status']; ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['order_time'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align:center; padding:30px; color:#999;">
                                        <i class="fas fa-utensils fa-2x" style="display:block; margin-bottom:10px;"></i>
                                        No orders found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>