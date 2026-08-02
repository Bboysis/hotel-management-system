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

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    
    $sql = "INSERT INTO reviews (customer_id, room_id, rating, review) 
            VALUES ('$customer_id', '$room_id', '$rating', '$review')";
    
    if ($conn->query($sql)) {
        // Update room average rating
        $update = "UPDATE rooms SET avg_rating = (SELECT AVG(rating) FROM reviews WHERE room_id = $room_id),
                   rating_count = (SELECT COUNT(*) FROM reviews WHERE room_id = $room_id)
                   WHERE id = $room_id";
        $conn->query($update);
        $message = "✅ Review submitted successfully!";
    } else {
        $error = "❌ Error: " . $conn->error;
    }
}

// Get rooms for dropdown
$sql_rooms = "SELECT id, room_number, room_type FROM rooms";
$rooms = $conn->query($sql_rooms);

// Get customer's reviews
$sql_reviews = "SELECT r.*, rm.room_number 
                FROM reviews r 
                JOIN rooms rm ON r.room_id = rm.id 
                WHERE r.customer_id = $customer_id 
                ORDER BY r.created_at DESC";
$reviews = $conn->query($sql_reviews);
?>
<!DOCTYPE html>
<html>
 <head>
      
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    Reviews - Hotel</title>
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
                <h1>⭐ Rate & Review</h1>
                <div class="top-bar-right">
               <?php echo language_switcher(); ?>
                 <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
 
                <a href="customer-dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> ↩Back</a>
</div>
            </header>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Review Form -->
             <div class="card">
                <h3>📝 Write a Review</h3>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Select Room</label>
                            <select name="room_id" required>
                                <option value="">-- Select Room --</option>
                                <?php while($row = $rooms->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        <?php echo $row['room_number']; ?> - <?php echo $row['room_type']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Rating (1-5)</label>
                            <select name="rating" required>
                                <option value="5">⭐ 5 - Excellent</option>
                                <option value="4">⭐⭐ 4 - Good</option>
                                <option value="3">⭐⭐⭐ 3 - Average</option>
                                <option value="2">⭐⭐⭐⭐ 2 - Poor</option>
                                <option value="1">⭐⭐⭐⭐⭐ 1 - Terrible</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Your Review</label>
                        <textarea name="review" rows="4" placeholder="Share your experience..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-star"></i> Submit Review</button>
                </form>
            </div>

            <!-- My Reviews -->
            <div class="card">
                <h3>📋 My Reviews</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
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
                                        <td><?php echo $row['room_number']; ?></td>
                                        <td><?php echo str_repeat('⭐', $row['rating']); ?></td>
                                        <td><?php echo $row['review'] ?: '-'; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align:center; padding:30px; color:#999;">No reviews yet</td>
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