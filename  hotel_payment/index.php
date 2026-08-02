<?php
require_once 'config.php';
require_once 'language_helper.php';

// Get stats
$sql_users = "SELECT COUNT(*) as total FROM users";
$total_users = $conn->query($sql_users)->fetch_assoc()['total'];

$sql_bookings = "SELECT COUNT(*) as total FROM bookings";
$total_bookings = $conn->query($sql_bookings)->fetch_assoc()['total'];

$sql_rooms = "SELECT COUNT(*) as total FROM rooms";
$total_rooms = $conn->query($sql_rooms)->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('app_name'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

        /* ============================================ */
/* SLIDESHOW BACKGROUND - 6 IMAGES             */
/* ============================================ */
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

        /* Dark overlay on slideshow */
        .slideshow::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        /* ============================================ */
        /* LOGIN BOX                                    */
        /* ============================================ */
        .login-box {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 40px;
            width: 420px;
            max-width: 100%;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5);
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-box .logo {
            text-align: center;
            font-size: 60px;
            margin-bottom: 10px;
        }
        .login-box h1 {
            font-family: 'Playfair Display', serif;
            color: #d4af37;
            text-align: center;
            font-size: 28px;
        }
        .login-box .subtitle {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-bottom: 25px;
        }
        .login-box input {
            width: 100%;
            padding: 14px 18px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: white;
            font-size: 14px;
            transition: 0.3s;
        }
        .login-box input::placeholder { color: rgba(255, 255, 255, 0.5); }
        .login-box input:focus {
            outline: none;
            border-color: #d4af37;
            background: rgba(255, 255, 255, 0.15);
        }
        .login-box .btn-login {
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
        .login-box .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
        }
        .login-box .btn-customer {
            display: block;
            width: 100%;
            padding: 12px;
            background: transparent;
            color: #d4af37;
            border: 1px solid #d4af37;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
            text-decoration: none;
            margin-bottom: 12px;
        }
        .login-box .btn-customer:hover {
            background: rgba(212, 175, 55, 0.1);
        }
        .login-box .btn-reception {
            display: block;
            width: 100%;
            padding: 12px;
            background: transparent;
            color: #27ae60;
            border: 1px solid #27ae60;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
            text-decoration: none;
            margin-bottom: 12px;
        }
        .login-box .btn-reception:hover {
            background: rgba(39, 174, 96, 0.1);
        }
        .login-box .btn-chef {
            display: block;
            width: 100%;
            padding: 12px;
            background: transparent;
            color: #ff9800;
            border: 1px solid #ff9800;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
            text-decoration: none;
            margin-bottom: 12px;
        }
        .login-box .btn-chef:hover {
            background: rgba(255, 152, 0, 0.1);
        }
        .login-box .divider {
            text-align: center;
            color: rgba(255, 255, 255, 0.3);
            font-size: 12px;
            margin: 15px 0;
            position: relative;
        }
        .login-box .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 40%;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }
        .login-box .divider::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            width: 40%;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }
        .login-box .register-link {
            text-align: center;
            margin-top: 12px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
        }
        .login-box .register-link a { color: #d4af37; text-decoration: none; }
        .login-box .register-link a:hover { text-decoration: underline; }
        .error {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(255, 0, 0, 0.1);
            border-radius: 8px;
            font-size: 14px;
        }
        .decorative-line {
            height: 2px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
            margin: 20px 0 20px;
        }
        .role-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(212, 175, 55, 0.2);
            color: #d4af37;
        }

        /* Language Switcher */
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
            background: rgba(0, 0, 0, 0.3);
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

        .demo-text {
            text-align: center;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.2);
            margin-top: 15px;
        }

        @media (max-width: 500px) {
            .login-box {
                padding: 25px 20px;
            }
            .login-box h1 {
                font-size: 22px;
            }
        }
        /* Typing Animation */
.typing-text {
    font-size: 16px;
    font-weight: 300;
    color: rgba(255,255,255,0.8);
    text-align: center;
    margin-bottom: 20px;
    min-height: 30px;
}
.typing-text .cursor {
    display: inline-block;
    width: 3px;
    height: 20px;
    background: #d4af37;
    animation: blink 0.8s infinite;
    vertical-align: text-bottom;
}
@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}
/* Login Notification */
.login-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 215, 0, 0.3);
    border-radius: 12px;
    padding: 15px 25px;
    color: white;
    font-size: 14px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    transform: translateX(150%);
    transition: transform 0.5s ease;
    min-width: 250px;
}
.login-notification.show {
    transform: translateX(0);
}
.login-notification .notif-icon {
    font-size: 24px;
    margin-right: 12px;
}
/* Floating Bubbles Animation */
#bubbles-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
    overflow: hidden;
}
.bubble {
    position: absolute;
    bottom: -100px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.15);
    pointer-events: none;
    animation: floatUp linear infinite;
}
@keyframes floatUp {
    0% { transform: translateY(0) scale(0.5); opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { transform: translateY(-110vh) scale(1.2); opacity: 0; }
}
/* Falling Stars Animation */
#stars-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
    overflow: hidden;
}
.star {
    position: absolute;
    top: -20px;
    color: #FFD700;
    font-size: 20px;
    opacity: 0;
    animation: fall linear infinite;
    text-shadow: 0 0 10px rgba(255, 215, 0, 0.5), 0 0 20px rgba(255, 215, 0, 0.3);
}
@keyframes fall {
    0% { 
        transform: translateY(0) rotate(0deg) scale(0.5); 
        opacity: 0; 
    }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { 
        transform: translateY(110vh) rotate(720deg) scale(1.2); 
        opacity: 0; 
    }
}
    </style>
</head>
<body>
    <!-- Falling Stars Container -->
    <div id="stars-container">
    </div>
    
    <!-- Floating Bubbles Container -->
    <div id="bubbles-container">
    </div>

    <!-- Login Notification -->
    <div class="login-notification" id="loginNotif">
        <span class="notif-icon">👋</span>
        <span id="notifMessage">Welcome back!</span>
    </div>

    <!-- ============================================ -->
    <!-- SLIDESHOW BACKGROUND                         -->
    <!-- ============================================ -->
    <div class="slideshow">
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
    </div>

     
    <div class="login-box">
    <!-- Language Switcher -->
    <div class="lang-switcher">
        <select onchange="window.location.href='?lang=' + this.value">
            <?php foreach ($available_languages as $code => $name): ?>
                <option value="<?php echo $code; ?>" <?php echo ($_SESSION['lang'] ?? 'en') == $code ? 'selected' : ''; ?>>
                    <?php echo $name; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="logo"></div>
    <h1><?php echo __('app_name'); ?></h1>
     <div class="typing-text" id="typingText">
    <span id="typedWords"></span><span class="cursor"></span>
</div>

     

    <div id="errorMsg" class="error" style="display:none;"></div>

    <!-- Customer Login -->
    <a href="customer-login.php" class="btn-customer">
        <i class="fas fa-user"></i> <?php echo __('customer_login'); ?>
    </a>

    <!-- Reception Login -->
    <a href="reception-login.php" class="btn-reception">
        <i class="fas fa-hotel"></i> <?php echo __('Reception Login'); ?>
    </a>

    <!-- Chef Login -->
    <a href="chef-login.php" class="btn-chef">
        <i class="fas fa-utensils"></i> Chef Login
    </a>

    <!-- Divider -->
    <div class="divider"><?php echo __('Admin / Staff Login'); ?></div>

    <!-- Login Form -->
    <form action="login.php" method="POST">
        <input type="text" name="username" placeholder="<?php echo __('username'); ?>" required>
        
       <div style="position: relative;">
    <input type="password" name="password" id="password" placeholder="<?php echo __('password'); ?>" required style="width: 100%; padding: 14px 45px 14px 18px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: white; font-size: 14px;">
    <i class="fas fa-eye" id="togglePassword" onclick="togglePasswordVisibility()" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.5); cursor: pointer; font-size: 18px; z-index: 10;"></i>
</div>
        
        <button type="submit" class="btn-login"><?php echo __('login'); ?></button>
    </form>

    <!-- Login Stats -->
    <div style="display: flex; justify-content: space-around; margin: 20px 0; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
        <div style="text-align: center;">
            <div style="font-size: 20px; font-weight: bold; color: #d4af37;"><?php echo $total_users; ?></div>
            <div style="font-size: 11px; color: rgba(255,255,255,0.5);">Users</div>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 20px; font-weight: bold; color: #d4af37;"><?php echo $total_bookings; ?></div>
            <div style="font-size: 11px; color: rgba(255,255,255,0.5);">Bookings</div>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 20px; font-weight: bold; color: #d4af37;"><?php echo $total_rooms; ?></div>
            <div style="font-size: 11px; color: rgba(255,255,255,0.5);">Rooms</div>
        </div>
    </div>

    <div class="register-link">
        <?php echo ("Don't have an account?"); ?> <a href="customer-register.php"><?php echo ('register'); ?></a>
    </div>

    <div class="decorative-line"></div>
    <p class="demo-text">
     </p>
</div>
   <script>
    
// FALLING STARS ANIMATION
 function createStar() {
    const container = document.getElementById('stars-container');
    if (!container) return;
    
    const star = document.createElement('div');
    star.className = 'star';
    
    // Random star type
    const starTypes = ['⭐', '✦', '✧', '🌟', '✨'];
    const randomStar = starTypes[Math.floor(Math.random() * starTypes.length)];
    
    const size = Math.random() * 25 + 10;
    const startX = Math.random() * 100;
    const duration = Math.random() * 8 + 5;
    const delay = Math.random() * 10;
    const rotation = Math.random() * 360;
    
    star.textContent = randomStar;
    star.style.fontSize = size + 'px';
    star.style.left = startX + '%';
    star.style.animationDuration = duration + 's';
    star.style.animationDelay = delay + 's';
    star.style.transform = 'rotate(' + rotation + 'deg)';
    
    // Random colors
    const colors = ['#FFD700', '#FFA500', '#FF6B6B', '#4ECDC4', '#FFE66D', '#FF9FF3', '#F368E0'];
    const randomColor = colors[Math.floor(Math.random() * colors.length)];
    star.style.color = randomColor;
    star.style.textShadow = '0 0 20px ' + randomColor + ', 0 0 40px ' + randomColor + '33';
    
    container.appendChild(star);
    
    // Remove star after animation
    setTimeout(() => {
        if (star.parentNode) {
            star.remove();
        }
    }, (duration + delay) * 1000 + 1000);
}

// Create stars every 1.5 seconds
setInterval(createStar, 1500);

// Initial stars
for (let i = 0; i < 50; i++) {
    setTimeout(createStar, i * 100);
}
     
    // ============================================
    // FLOATING BUBBLES ANIMATION
    // ============================================
    function createBubble() {
        const container = document.getElementById('bubbles-container');
        if (!container) return;
        
        const bubble = document.createElement('div');
        const size = Math.random() * 60 + 15;
        const startX = Math.random() * 100;
        const duration = Math.random() * 15 + 10;
        const delay = Math.random() * 10;
        
        bubble.className = 'bubble';
        bubble.style.width = size + 'px';
        bubble.style.height = size + 'px';
        bubble.style.left = startX + '%';
        bubble.style.animationDuration = duration + 's';
        bubble.style.animationDelay = delay + 's';
        bubble.style.opacity = Math.random() * 0.3 + 0.1;
        
        container.appendChild(bubble);
        
        // Remove bubble after animation to prevent memory issues
        setTimeout(() => {
            if (bubble.parentNode) {
                bubble.remove();
            }
        }, (duration + delay) * 1000 + 1000);
    }

    // Create bubbles every 2 seconds
    let bubbleInterval = setInterval(createBubble, 300);

    // Initial bubbles
    for (let i = 0; i < 50; i++) {
        setTimeout(createBubble, i * 300);
    }
 
    // ============================================
    // SHOW ERROR MESSAGE
    // ============================================
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('error')) {
        const errorMsg = document.getElementById('errorMsg');
        if (errorMsg) {
            errorMsg.style.display = 'block';
            errorMsg.textContent = '❌ <?php echo __('invalid_credentials'); ?>';
        }
    }
    

    // ============================================
    // TYPING ANIMATION
    // ============================================
    const words = [
        'Welcome to Hotel Management! 🏨',
        'Book your stay with us! ✨',
        'Luxury awaits you! 🌟',
        'Experience the best! 🎉'
    ];
    let wordIndex = 0;
    let charIndex = 0;
    let isDeleting = false;

    function typeEffect() {
        const typedElement = document.getElementById('typedWords');
        if (!typedElement) {
            setTimeout(typeEffect, 100);
            return;
        }
        const currentWord = words[wordIndex];
        if (!isDeleting) {
            typedElement.textContent = currentWord.substring(0, charIndex + 1);
            charIndex++;
            if (charIndex === currentWord.length) {
                setTimeout(() => { isDeleting = true; }, 2000);
            }
        } else {
            typedElement.textContent = currentWord.substring(0, charIndex);
            charIndex--;
            if (charIndex < 0) {
                isDeleting = false;
                wordIndex = (wordIndex + 1) % words.length;
            }
        }
        setTimeout(typeEffect, isDeleting ? 50 : 100);
    }
 // Toggle Password Visibility
function togglePasswordVisibility() {
    const password = document.getElementById('password');
    const toggle = document.getElementById('togglePassword');
    
    if (!password || !toggle) {
        console.log('Elements not found!');
        return;
    }
    
    console.log('Current type:', password.type);
    
    if (password.type === 'password') {
        password.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
        toggle.style.color = '#d4af37';
        toggle.style.transform = 'scale(1.2)';
        console.log('Password is now VISIBLE');
    } else {
        password.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
        toggle.style.color = 'rgba(255,255,255,0.5)';
        toggle.style.transform = 'scale(1)';
        console.log('Password is now HIDDEN');
    }

}
    // ============================================
    // TOGGLE PASSWORD
    // ============================================

    function togglePassword() {
        const toggleBtn = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        if (toggleBtn && password) {
            toggleBtn.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });
        }
    }

    // ============================================
    // LOGIN NOTIFICATION
    // ============================================
     
function showNotification(message, icon = '👋') {
    const notif = document.getElementById('loginNotif');
    if (!notif) return;
    const msgElement = document.getElementById('notifMessage');
    const iconElement = document.querySelector('.notif-icon');
    if (msgElement) msgElement.textContent = message;
    if (iconElement) iconElement.textContent = icon;
    notif.classList.add('show');
    setTimeout(() => {
        notif.classList.remove('show');
    }, 4000);
}

// Show notification on page load
document.addEventListener('DOMContentLoaded', function() {
    const hour = new Date().getHours();
    let greeting = 'Welcome back! 👋';
    let icon = '👋';
    if (hour < 12) { greeting = 'Good Morning! ☀️'; icon = '🌅'; }
    else if (hour < 17) { greeting = 'Good Afternoon! 🌤️'; icon = '☀️'; }
    else { greeting = 'Good Evening! 🌙'; icon = '🌙'; }
    
    setTimeout(function() {
        showNotification(greeting + ' Ready to manage your hotel?', icon);
    }, 1500);
});

    // ============================================
    // START ALL FUNCTIONS ON PAGE LOAD
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        // Start typing animation
        typeEffect();

        // Setup password toggle
        togglePassword();

        // Show welcome notification
        const hour = new Date().getHours();
        let greeting = 'Welcome!';
        let icon = '👋';
        if (hour < 12) { greeting = 'Good Morning! ☀️'; icon = '🌅'; }
        else if (hour < 17) { greeting = 'Good Afternoon! ☀️'; icon = '☀️'; }
        else { greeting = 'Good Evening! 🌙'; icon = '🌙'; }

        setTimeout(() => {
            showNotification(greeting + ' Ready to manage your hotel?', icon);
        }, 1000);
    });
</script>
 
</body>
</html>