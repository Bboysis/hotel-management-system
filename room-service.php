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

// Get customer's room
$sql_room = "SELECT b.room_id, r.room_number 
             FROM bookings b 
             JOIN rooms r ON b.room_id = r.id 
             WHERE b.customer_id = $customer_id AND b.status IN ('Checked-in', 'Confirmed')
             ORDER BY b.id DESC LIMIT 1";
$room = $conn->query($sql_room)->fetch_assoc();

// Get menu items
$sql_menu = "SELECT * FROM menu_items WHERE availability = 1 ORDER BY category";
$menu_items = $conn->query($sql_menu);

// Get categories
$sql_categories = "SELECT DISTINCT category FROM menu_items ORDER BY category";
$categories = $conn->query($sql_categories);

// Get order history
$sql_orders = "SELECT * FROM room_service_orders WHERE customer_id = $customer_id ORDER BY order_time DESC LIMIT 10";
$orders = $conn->query($sql_orders);

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    if (!$room) {
        $error = "❌ You need an active booking to order room service!";
    } else {
        $items = $_POST['items'];
        $special_requests = $_POST['special_requests'];
        $total_amount = $_POST['total_amount'];
        $order_number = 'RS-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $sql = "INSERT INTO room_service_orders (order_number, customer_id, room_id, items, total_amount, special_requests, status) 
                VALUES ('$order_number', '$customer_id', '".$room['room_id']."', '$items', '$total_amount', '$special_requests', 'Pending')";
        
        if ($conn->query($sql)) {
            $message = "✅ Order placed successfully! Order #: $order_number";
        } else {
            $error = "❌ Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
 <head>
      
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    <?php echo ('room_service'); ?> - <?php echo ('app_name'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 25px;
            background: rgba(255,255,255,0.08);
            border-bottom: 2px solid rgba(255,255,255,0.1);
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
            color: rgba(255,255,255,0.5);
            font-size: 12px;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .menu-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #eee;
            transition: 0.3s;
        }
        .menu-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .menu-item h4 {
            margin-bottom: 5px;
        }
        .menu-item .category-tag {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(212, 175, 55, 0.15);
            color: #b8960f;
        }
        .menu-item .price-tag {
            font-weight: bold;
            color: var(--gold);
            font-size: 16px;
            margin-top: 5px;
        }
        .menu-item input[type="number"] {
            width: 100%;
            padding: 5px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .category-title {
            color: var(--gold);
            font-size: 18px;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid rgba(212, 175, 55, 0.2);
            padding-bottom: 8px;
        }
        .category-title i {
            margin-right: 8px;
        }
        .order-btn {
            margin-top: 15px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #d4af37, #b8960f);
            color: #1a1a2e;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
        }
        .order-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
        }
        .order-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .special-requests {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 10px;
        }
        .order-summary {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            display: none;
        }
        .order-summary.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <span class="brand-icon">🏨</span>
                <div>
                    <h2>Hotel</h2>
                    <small>Customer Portal</small>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="customer-dashboard.php" class="nav-link"><i class="fas fa-chart-pie"></i><span><?php echo __('dashboard'); ?></span></a>
                <a href="customer-rooms.php" class="nav-link"><i class="fas fa-bed"></i><span><?php echo __('rooms'); ?></span></a>
                <a href="customer-book.php" class="nav-link"><i class="fas fa-calendar-plus"></i><span><?php echo __('book_now'); ?></span></a>
                <a href="customer-profile.php" class="nav-link"><i class="fas fa-user"></i><span><?php echo __('profile'); ?></span></a>
                <a href="customer-reviews.php" class="nav-link"><i class="fas fa-star"></i><span><?php echo __('reviews'); ?></span></a>
                <a href="room-service.php" class="nav-link active"><i class="fas fa-utensils"></i><span><?php echo __('room_service'); ?></span></a>
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
            <!-- Top Bar -->
            <div class="top-bar">
                <h1>🍽️ <?php echo __('room_service'); ?></h1>
                <div class="top-bar-right">
                    <span class="date-text"><?php echo date('d/m/Y'); ?></span>
                    <?php echo language_switcher(); ?>
                     <div class="top-bar-right">
                <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>  Logout</a>
 
                <a href="customer-dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> ↩Back</a>
</div>
                </div>
            </div>

            <div style="padding: 20px;">

                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (!$room): ?>
                    <div class="alert alert-danger">
                        ⚠️ You need an active booking to order room service! Please check in first.
                    </div>
                <?php endif; ?>

                <!-- Menu -->
                <div class="card">
                    <h3>📋 Menu</h3>
                    <?php if ($menu_items->num_rows > 0): ?>
                        <form method="POST" id="orderForm">
                            <?php 
                            $current_category = '';
                            while($row = $menu_items->fetch_assoc()): 
                                if ($row['category'] != $current_category) {
                                    if ($current_category != '') echo '</div>';
                                    $current_category = $row['category'];
                                    echo '<div class="category-title"><i class="fas fa-utensils"></i> ' . $current_category . '</div>';
                                    echo '<div class="menu-grid">';
                                }
                            ?>
                                <div class="menu-item">
                                    <h4><?php echo $row['name']; ?></h4>
                                    <span class="category-tag"><?php echo $row['category']; ?></span>
                                    <p style="font-size: 13px; color: #666; margin: 5px 0;"><?php echo $row['description']; ?></p>
                                    <div class="price-tag"><?php echo CURRENCY . number_format($row['price'], 2); ?></div>
                                    <input type="number" name="qty_<?php echo $row['id']; ?>" value="0" min="0" max="10" placeholder="Qty">
                                </div>
                            <?php endwhile; ?>
                            </div><!-- end menu-grid -->

                            <div style="margin-top: 20px;">
                                <label style="font-weight: bold;">Special Requests</label>
                                <textarea name="special_requests" placeholder="Any special requests..." class="special-requests"></textarea>
                            </div>

                            <div id="orderSummary" class="order-summary">
                                <p><strong>Items:</strong> <span id="summaryItems"></span></p>
                                <p><strong>Total:</strong> <span id="summaryTotal">$0.00</span></p>
                            </div>

                            <input type="hidden" name="items" id="itemsInput">
                            <input type="hidden" name="total_amount" id="totalAmount">
                            <button type="submit" name="place_order" class="order-btn" <?php echo (!$room) ? 'disabled' : ''; ?>>
                                <i class="fas fa-shopping-cart"></i> Place Order
                            </button>
                        </form>
                    <?php else: ?>
                        <p style="color: #999; text-align: center; padding: 20px;">Menu is empty</p>
                    <?php endif; ?>
                </div>

                <!-- Order History -->
                <div class="card">
                    <h3>📋 My Orders</h3>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($orders->num_rows > 0): ?>
                                    <?php while($row = $orders->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['order_number']; ?></td>
                                            <td><?php echo $row['items']; ?></td>
                                            <td><?php echo CURRENCY . number_format($row['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                                    <?php echo $row['status']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($row['order_time'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align:center; padding:30px; color:#999;">No orders yet</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            var items = [];
            var total = 0;
            var inputs = document.querySelectorAll('input[type="number"]');
            var prices = <?php 
                $prices = [];
                $menu_items->data_seek(0);
                while($row = $menu_items->fetch_assoc()) {
                    $prices[$row['id']] = $row['price'];
                }
                echo json_encode($prices);
            ?>;
            
            inputs.forEach(function(input) {
                var qty = parseInt(input.value);
                if (qty > 0) {
                    var id = input.name.replace('qty_', '');
                    var name = input.closest('.menu-item').querySelector('h4').textContent;
                    items.push(name + ' x' + qty);
                    total += qty * prices[id];
                }
            });
            
            if (items.length === 0) {
                e.preventDefault();
                alert('Please select at least one item!');
                return;
            }
            
            document.getElementById('itemsInput').value = items.join(', ');
            document.getElementById('totalAmount').value = total;
        });
        // Show summary when quantities change
        document.querySelectorAll('input[type="number"]').forEach(function(input) {
            input.addEventListener('change', function() {
                var items = [];
                var total = 0;
                var inputs = document.querySelectorAll('input[type="number"]');
                var prices = <?php 
                    $prices = [];
                    $menu_items->data_seek(0);
                    while($row = $menu_items->fetch_assoc()) {
                        $prices[$row['id']] = $row['price'];
                    }
                    echo json_encode($prices);
                ?>;
                
                inputs.forEach(function(inp) {
                    var qty = parseInt(inp.value);
                    if (qty > 0) {
                        var id = inp.name.replace('qty_', '');
                        var name = inp.closest('.menu-item').querySelector('h4').textContent;
                        items.push(name + ' x' + qty);
                        total += qty * prices[id];
                    }
                });
                
                var summary = document.getElementById('orderSummary');
                if (items.length > 0) {
                    summary.classList.add('active');
                    document.getElementById('summaryItems').textContent = items.join(', ');
                    document.getElementById('summaryTotal').textContent = '$' + total.toFixed(2);
                } else {
                    summary.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>