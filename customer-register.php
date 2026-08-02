 <?php
require_once 'config.php';
require_once 'language_helper.php';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    
    // Check if username exists
    $check = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($check);
    
    if ($result->num_rows > 0) {
        $error = "❌ Username already exists! Please choose another.";
    } else {
        // Insert customer
        $sql = "INSERT INTO customers (full_name, email, phone, address) 
                VALUES ('$full_name', '$email', '$phone', '$address')";
        
        if ($conn->query($sql)) {
            $customer_id = $conn->insert_id;
            
            // Insert user
            $sql_user = "INSERT INTO users (username, password, role, full_name, email, customer_id) 
                         VALUES ('$username', '$password', 'customer', '$full_name', '$email', '$customer_id')";
            
            if ($conn->query($sql_user)) {
                $message = "✅ Registration successful! You can now login.";
            } else {
                $error = "❌ Error creating user: " . $conn->error;
            }
        } else {
            $error = "❌ Error creating customer: " . $conn->error;
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
    Register - Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }
        .slideshow {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
}
.slideshow .slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: 100% 100%;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0;
    transition: opacity 1.5s ease-in-out;
    animation: slideAnimation 30s infinite;
}
.slideshow .slide:nth-child(1) {
    background-image: url('assets/images/photo_2026-07-06_15-32-28 (2).jpg');
    animation-delay: 0s;
}
.slideshow .slide:nth-child(2) {
    background-image: url('assets/images/photo_2026-07-06_15-32-28 (3).jpg');
    animation-delay: 5s;
}
.slideshow .slide:nth-child(3) {
    background-image: url('assets/images/photo_2026-07-06_15-32-28.jpg');
    animation-delay: 10s;
}
.slideshow .slide:nth-child(4) {
    background-image: url('assets/images/photo_2026-07-06_15-32-29 (2).jpg');
    animation-delay: 15s;
}
.slideshow .slide:nth-child(5) {
    background-image: url('assets/images/photo_2026-07-06_15-32-29 (3).jpg');
    animation-delay: 20s;
}
.slideshow .slide:nth-child(6) {
    background-image: url('assets/images/photo_2026-07-06_15-32-29.jpg');
    animation-delay: 25s;
}

@keyframes slideAnimation {
    0% { opacity: 0; }
    8% { opacity: 1; }
    20% { opacity: 1; }
    28% { opacity: 0; }
    100% { opacity: 0; }
}
        .register-box {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 40px;
            width: 500px;
            max-width: 100%;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
        }
        .register-box h1 {
            font-family: 'Playfair Display', serif;
            color: #d4af37;
            text-align: center;
            font-size: 28px;
        }
        .register-box .subtitle {
            text-align: center;
            color: rgba(255,255,255,0.6);
            font-size: 14px;
            margin-bottom: 25px;
        }
        .register-box input {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 12px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: white;
            font-size: 14px;
            transition: 0.3s;
        }
        .register-box input::placeholder { color: rgba(255,255,255,0.4); }
        .register-box input:focus {
            outline: none;
            border-color: #d4af37;
            background: rgba(255,255,255,0.12);
        }
        .register-box .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #d4af37, #b8960f);
            color: #1a1a2e;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .register-box .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
        }
        .register-box .login-link {
            text-align: center;
            margin-top: 15px;
            color: rgba(255,255,255,0.5);
            font-size: 14px;
        }
        .register-box .login-link a { color: #d4af37; text-decoration: none; }
        .register-box .login-link a:hover { text-decoration: underline; }
        .register-box .back-link {
            display: block;
            text-align: center;
            margin-top: 12px;
            color: rgba(255,255,255,0.3);
            font-size: 13px;
            text-decoration: none;
        }
        .register-box .back-link:hover { color: rgba(255,255,255,0.6); }
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .alert-success {
            background: rgba(39, 174, 96, 0.2);
            color: #27ae60;
            border: 1px solid rgba(39, 174, 96, 0.3);
        }
        .alert-danger {
            background: rgba(231, 76, 60, 0.2);
            color: #ff6b6b;
            border: 1px solid rgba(231, 76, 60, 0.3);
        }
        .decorative-line {
            height: 2px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
            margin: 20px 0 25px;
        }
         /* Language Switcher Inside Login Box */
        .lang-switcher {
            position: absolute;
            top: 15px;
            right: 20px;
            z-index: 10;
        }
        .lang-switcher select {
            padding: 5px 10px;
            border-radius: 8px;
            border: 2px solid rgba(212, 175, 55, 0.5);
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 12px;
            cursor: pointer;
            font-weight: 600;
        }
        .lang-switcher select:hover {
            border-color: #d4af37;
        }
        .lang-switcher select option {
            background: #1a1a2e;
            color: white;
        }
        
    </style>
</head>
<body> 
     <div class="slideshow">
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
    </div>  
<div class="register-box">
    <h1>🏨 Create Account</h1>
        <p class="subtitle">read and formun Beden mul </p>
    <!-- Language Switcher - Top Right -->
    <div class="lang-switcher">
        <select onchange="window.location.href='?lang=' + this.value">
            <?php foreach ($available_languages as $code => $name): ?>
                <option value="<?php echo $code; ?>" <?php echo ($_SESSION['lang'] ?? 'en') == $code ? 'selected' : ''; ?>>
                    <?php echo $name; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!$message): ?>
        <form method="POST">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone Number">
            <input type="text" name="address" placeholder="Address">
            <input type="text" name="username" placeholder="Choose Username" required>
            <input type="password" name="password" placeholder="Choose Password" required>
            <button type="submit" class="btn-register">Create Account</button>
        </form>
        <?php endif; ?>

        <div class="login-link">
            Already have an account? <a href="customer-login.php">Login here</a>
        </div>
        <div class="decorative-line"></div>
        <a href="index.php" class="back-link">⬅ Back to Admin Login</a>
    </div>
</body>
</html>