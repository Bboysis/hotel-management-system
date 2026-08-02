<?php
require_once 'config.php';
require_once 'language_helper.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Get all reviews with customer and room info
$sql_reviews = "SELECT r.*, c.full_name as customer_name, c.email, rm.room_number, rm.room_type 
                FROM reviews r 
                JOIN customers c ON r.customer_id = c.id 
                JOIN rooms rm ON r.room_id = rm.id 
                ORDER BY r.created_at DESC";
$reviews = $conn->query($sql_reviews);

// Get review statistics
$sql_stats = "SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as avg_rating,
                COUNT(DISTINCT customer_id) as total_customers,
                COUNT(DISTINCT room_id) as total_rooms
                FROM reviews";
$stats = $conn->query($sql_stats)->fetch_assoc();

// Get rating distribution
$sql_distribution = "SELECT rating, COUNT(*) as count FROM reviews GROUP BY rating ORDER BY rating DESC";
$distribution = $conn->query($sql_distribution);
?>
<!DOCTYPE html>
<html>
 <head>
      
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    Reviews - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
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
                <a href="rooms.php" class="nav-link"><i class="fas fa-bed"></i><span>Rooms</span></a>
                <a href="bookings.php" class="nav-link"><i class="fas fa-calendar-check"></i><span>Bookings</span></a>
                <a href="customers.php" class="nav-link"><i class="fas fa-users"></i><span>Customers</span></a>
                <a href="payments.php" class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span></a>
                <a href="reports.php" class="nav-link"><i class="fas fa-file-alt"></i><span>Reports</span></a>
                <a href="admin-reviews.php" class="nav-link active"><i class="fas fa-star"></i><span>Reviews</span></a>
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

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1>⭐ Customer Reviews</h1>
                <span style="color: var(--gray); font-size:14px;"><?php echo date('l, F j, Y'); ?></span>
                 <div class="top-bar-right">
               <?php echo language_switcher(); ?>
                 <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
                 </div>
            </header>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon gold">⭐</div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['avg_rating'] ?? 0, 1); ?></h3>
<p>Average Rating</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue">📋</div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_reviews'] ?? 0; ?></h3>
                        <p>Total Reviews</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">👤</div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_customers'] ?? 0; ?></h3>
                        <p>Customers</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red">🛏️</div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_rooms'] ?? 0; ?></h3>
                        <p>Rooms Reviewed</p>
                    </div>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="card">
                <h3>📊 Rating Distribution</h3>
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <?php 
                    $max_count = 0;
                    $distribution->data_seek(0);
                    while($row = $distribution->fetch_assoc()) {
                        if ($row['count'] > $max_count) $max_count = $row['count'];
                    }
                    $distribution->data_seek(0);
                    
                    $colors = ['#f44336', '#ff9800', '#ffeb3b', '#8bc34a', '#4caf50'];
                    while($row = $distribution->fetch_assoc()): 
                        $width = ($max_count > 0) ? ($row['count'] / $max_count) * 100 : 0;
                    ?>
                        <div style="flex: 1; min-width: 80px; text-align: center;">
                            <div style="font-size: 24px;"><?php echo str_repeat('⭐', $row['rating']); ?></div>
                            <div style="height: 80px; display: flex; align-items: flex-end; justify-content: center;">
                                <div style="width: 30px; background: <?php echo $colors[$row['rating']-1]; ?>; height: <?php echo $width; ?>%; border-radius: 4px 4px 0 0; min-height: 5px;"></div>
                            </div>
                            <div style="font-weight: bold; margin-top: 5px;"><?php echo $row['count']; ?></div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Reviews Table -->
            <div class="card">
                <h3>📋 All Reviews</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Room</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($reviews->num_rows > 0): ?>
                                <?php while($row = $reviews->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $row['id']; ?></td>
                                        <td><?php echo $row['customer_name']; ?></td>
                                        <td><span class="badge room"><?php echo $row['room_number']; ?></span></td>
                                        <td><?php echo str_repeat('⭐', $row['rating']); ?></td>
                                        <td><?php echo $row['review'] ?: '-'; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align:center; padding:30px; color:#999;">
                                        <i class="fas fa-star fa-2x" style="display:block; margin-bottom:10px;"></i>
                                        No reviews yet
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