 <?php
require_once 'config.php';
require_once 'language_helper.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'chef') {
    header('Location: chef-login.php');
    exit();
}

// Get today's orders
$sql_today = "SELECT o.*, c.full_name as customer_name, r.room_number 
              FROM room_service_orders o 
              JOIN customers c ON o.customer_id = c.id 
              JOIN rooms r ON o.room_id = r.id 
              WHERE DATE(o.order_time) = CURDATE() 
              ORDER BY o.order_time DESC";
$today_orders = $conn->query($sql_today);

// Get pending orders
$sql_pending = "SELECT o.*, c.full_name as customer_name, r.room_number 
                FROM room_service_orders o 
                JOIN customers c ON o.customer_id = c.id 
                JOIN rooms r ON o.room_id = r.id 
                WHERE o.status IN ('Pending', 'Preparing') 
                ORDER BY o.order_time ASC";
$pending_orders = $conn->query($sql_pending);

// Count pending for notification
$sql_count = "SELECT COUNT(*) as total FROM room_service_orders WHERE status = 'Pending'";
$count = $conn->query($sql_count)->fetch_assoc();
$pending_count = $count['total'];

// Handle status update
if (isset($_GET['update'])) {
    $id = $_GET['update'];
    $status = $_GET['status'];
    $update = "UPDATE room_service_orders SET status = '$status' WHERE id = $id";
    if ($conn->query($update)) {
        header('Location: chef-dashboard.php?success=1');
        exit();
    }
}

// Handle cancel order
if (isset($_GET['cancel'])) {
    $id = $_GET['cancel'];
    $update = "UPDATE room_service_orders SET status = 'Cancelled' WHERE id = $id";
    if ($conn->query($update)) {
        header('Location: chef-dashboard.php?cancelled=1');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
 <head>
      
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    Chef Dashboard - Hotel</title>
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
        .top-bar-right .notification-badge {
            position: relative;
            display: inline-block;
        }
        .top-bar-right .notification-badge .badge-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: bold;
            min-width: 20px;
            text-align: center;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .stat-icon.gold { background: #fff3e0; }
        .stat-icon.blue { background: #e3f2fd; }
        .stat-icon.green { background: #e8f5e9; }
        .stat-info h3 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a2e;
        }
        .stat-info p {
            font-size: 13px;
            color: #777;
            margin-top: 2px;
        }

        /* Order Grid */
        .order-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .order-column {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .order-column h3 {
            color: #1a1a2e;
            font-size: 16px;
            margin-bottom: 15px;
            border-bottom: 2px solid #d4af37;
            padding-bottom: 10px;
        }
        .order-column h3 .count {
            background: #d4af37;
            color: white;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 8px;
        }

        /* Order Card */
        .order-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            border-left: 4px solid #d4af37;
            transition: 0.3s;
        }
        .order-card:hover {
            background: #f0f0f0;
        }
        .order-card.pending { border-left-color: #ff9800; }
        .order-card.preparing { border-left-color: #2196f3; }
        .order-card.delivered { border-left-color: #4caf50; }
        .order-card.completed { border-left-color: #9e9e9e; }
        .order-card.cancelled { border-left-color: #f44336; opacity: 0.6; }

        .order-card .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        .order-card .order-header .order-number {
            font-weight: bold;
            font-size: 15px;
            color: #1a1a2e;
        }
        .order-card .order-header .room-number {
            background: #d4af37;
            color: white;
            padding: 2px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .order-card .order-items {
            font-size: 14px;
            color: #555;
            margin: 4px 0;
        }
        .order-card .order-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            font-size: 12px;
            color: #999;
        }
        .order-card .order-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .order-card .order-actions .btn-action {
            padding: 5px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-cook { background: #ff9800; color: white; }
        .btn-cook:hover { background: #e68900; }
        .btn-ready { background: #4caf50; color: white; }
        .btn-ready:hover { background: #388e3c; }
        .btn-complete { background: #9e9e9e; color: white; }
        .btn-complete:hover { background: #757575; }
        .btn-cancel { background: #f44336; color: white; }
        .btn-cancel:hover { background: #d32f2f; }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 3px 14px;
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

        /* Alerts */
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin: 10px 20px;
            font-weight: 500;
        }
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }
        .alert-danger {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }

        @media (max-width: 768px) {
            .order-grid {
                grid-template-columns: 1fr;
            }
            .top-bar {
                flex-direction: column;
                gap: 10px;
            }
            .stats-grid {
                grid-template-columns: 1fr 1fr;
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
                <a href="chef-dashboard.php" class="nav-link active"><i class="fas fa-chart-pie"></i><span>Dashboard</span></a>
                <a href="chef-orders.php" class="nav-link"><i class="fas fa-utensils"></i><span>All Orders</span></a>
            </nav>
            <div class="sidebar-footer">
    <!-- 👇 LOGOUT MOVED UP 👇 -->
    <a href="logout.php" class="logout-btn" style="
        display: flex; 
        align-items: center; 
        gap: 10px; 
        color: #44efe6; 
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
            <div class="top-bar">
                <h1>👨‍🍳 Kitchen Dashboard</h1>
                
                        <div class="top-bar-right">
               <?php echo language_switcher(); ?>
         <div class="top-bar-right">
                  <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
                <div class="top-bar-right">
                    <span class="date-text"><?php echo date('d/m/Y'); ?></span>
                    <div class="notification-badge">
                        <i class="fas fa-bell" style="font-size: 18px; color: white;"></i>
                        <?php if ($pending_count > 0): ?>
                        
                            <span class="badge-count"><?php echo $pending_count; ?></span>
                        <?php endif; ?>
                        </div>
                        </div>
            </div>      
                </div>
                </div>
                  
          

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">✅ Order status updated successfully!</div>
            <?php endif; ?>
            <?php if (isset($_GET['cancelled'])): ?>
                <div class="alert alert-danger">❌ Order cancelled!</div>
            <?php endif; ?>

            <div style="padding: 20px;">
                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon gold">📋</div>
                        <div class="stat-info">
                            <h3><?php echo $pending_count; ?></h3>
                            <p>Pending Orders</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon blue">👨‍🍳</div>
                        <div class="stat-info">
                            <h3><?php echo $today_orders->num_rows; ?></h3>
                            <p>Today's Orders</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">✅</div>
                        <div class="stat-info">
                            <h3><?php 
                                $completed = 0;
                                $today_orders->data_seek(0);
                                while($row = $today_orders->fetch_assoc()) {
                                    if ($row['status'] == 'Completed') $completed++;
                                }
                                echo $completed;
                            ?></h3>
                            <p>Completed</p>
                        </div>
                    </div>
                </div>

                <!-- Orders Grid -->
                <div class="order-grid">
                    <!-- Pending Orders -->
                    <div class="order-column">
                        <h3>🟡 Pending Orders <span class="count"><?php echo $pending_count; ?></span></h3>
                        <?php 
                        $pending_orders->data_seek(0);
                        if ($pending_orders->num_rows > 0): 
                            while($row = $pending_orders->fetch_assoc()): 
                        ?>
                            <div class="order-card <?php echo strtolower($row['status']); ?>">
                                <div class="order-header">
                                    <span class="order-number"><?php echo $row['order_number']; ?></span>
                                    <span class="room-number">🏠 Room <?php echo $row['room_number']; ?></span>
                                </div>
                                <div class="order-items"><?php echo $row['items']; ?></div>
                                <div class="order-meta">
                                    <span>👤 <?php echo $row['customer_name']; ?></span>
                                    <span><?php echo date('H:i', strtotime($row['order_time'])); ?></span>
                                    </div>
                                <?php if (!empty($row['special_requests'])): ?>
                                    <div style="font-size: 12px; color: #ff9800; margin-top: 4px;">
                                        📝 <?php echo $row['special_requests']; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="order-actions">
                                    <?php if ($row['status'] == 'Pending'): ?>
                                        <a href="chef-dashboard.php?update=<?php echo $row['id']; ?>&status=Preparing" class="btn-action btn-cook">👨‍🍳 Cook</a>
                                    <?php endif; ?>
                                    <?php if ($row['status'] == 'Preparing'): ?>
                                        <a href="chef-dashboard.php?update=<?php echo $row['id']; ?>&status=Delivered" class="btn-action btn-ready">🚚 Ready</a>
                                    <?php endif; ?>
                                    <?php if ($row['status'] == 'Delivered'): ?>
                                        <a href="chef-dashboard.php?update=<?php echo $row['id']; ?>&status=Completed" class="btn-action btn-complete">✅ Complete</a>
                                    <?php endif; ?>
                                    <?php if ($row['status'] != 'Completed' && $row['status'] != 'Cancelled'): ?>
                                        <a href="chef-dashboard.php?cancel=<?php echo $row['id']; ?>" class="btn-action btn-cancel" onclick="return confirm('Cancel this order?')">❌ Cancel</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; 
                        else: ?>
                            <p style="text-align:center; padding:20px; color:#999;">✅ No pending orders</p>
                        <?php endif; ?>
                    </div>

                    <!-- Today's Orders -->
                    <div class="order-column">
                        <h3>📅 Today's Orders <span class="count"><?php echo $today_orders->num_rows; ?></span></h3>
                        <?php 
                        $today_orders->data_seek(0);
                        if ($today_orders->num_rows > 0): 
                            while($row = $today_orders->fetch_assoc()): 
                        ?>
                            <div class="order-card <?php echo strtolower($row['status']); ?>">
                                <div class="order-header">
                                    <span class="order-number"><?php echo $row['order_number']; ?></span>
                                    <span class="room-number">🏠 Room <?php echo $row['room_number']; ?></span>
                                </div>
                                <div class="order-items"><?php echo $row['items']; ?></div>
                                <div class="order-meta">
                                    <span>👤 <?php echo $row['customer_name']; ?></span>
                                    <span class="status-badge status-<?php echo $row['status']; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endwhile; 
                        else: ?>
                            <p style="text-align:center; padding:20px; color:#999;">📭 No orders today</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Auto-refresh every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>