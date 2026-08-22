# 🏨 Trinity Hotel Management System

A complete Hotel Management System built with PHP, MySQL, HTML, CSS, and JavaScript. Designed for hotels to manage rooms, bookings, customers, payments, and staff efficiently.

---

## 📋 Features

### 🔐 Authentication
- Admin Login - Full system control
- Reception Login - Manage bookings & guests
- Customer Login - Book rooms & view history
- Chef Login - Manage room service orders

### 🛏️ Room Management
- Add, edit, delete rooms
- Room types: Single, Double, Suite, Deluxe, Presidential
- Room status: Available, Booked, Maintenance, Cleaning
- Room images upload

### 📅 Booking Management
- Create new bookings
- Check-in / Check-out guests
- Booking status: Pending, Confirmed, Checked-in, Checked-out, Cancelled
- View booking history

### 👤 Customer Management
- Add, edit, delete customers
- Customer profiles with contact details
- Booking history per customer

### 💰 Payment Management
- Record payments
- Payment methods: Cash, Credit Card, Bank Transfer, Mobile Money
- Payment status: Pending, Paid, Refunded
- Payment history

### 👨‍🍳 Chef Dashboard
- View pending orders
- Update order status: Pending → Preparing → Delivered → Completed
- Cancel orders
- Order notifications

### 🍽️ Room Service
- Menu with categories (Breakfast, Lunch, Dinner, Drinks, Snacks, Desserts)
- Place orders
- Special requests
- Order history

### ⭐ Reviews & Ratings
- Customers can rate rooms
- View all reviews (Admin)
- Rating distribution

### 🌍 Multi-Language Support
- English 🇬🇧
- Amharic 🇪🇹
- French 🇫🇷
- Portuguese 🇵🇹

### 📊 Reports & Analytics
- Revenue reports
- Booking statistics
- Room occupancy rate
- Monthly revenue

### 🎨 UI Features
- Slideshow background
- Typing animation
- Floating bubbles
- Falling stars
- Show/Hide password
- Mobile responsive

---

## 🚀 Demo

### Live URL
[https://trinity-hotel.infinityfree.io/](https://trinity-hotel.infinityfree.io/)

### Login Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | admin123 |
| Reception | reception | reception123 |
| Customer | customer | customer123 |
| Chef | chef | chef123 |

---

## 🛠️ Tech Stack

| Technology | Purpose |
|------------|---------|
| PHP | Backend logic |
| MySQL | Database |
| HTML5 | Structure |
| CSS3 | Styling |
| JavaScript | Interactivity |
| Font Awesome | Icons |
| Google Fonts | Typography |

---

## 📁 Project Structure
hotel/
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── dark-mode.css
│   ├── js/
│   │   └── script.js
│   └── images/
├── languages/
│   ├── en.php
│   ├── am.php
│   ├── fr.php
│   └── pt.php
├── uploads/
│   └── rooms/
├── index.php
├── dashboard.php
├── rooms.php
├── bookings.php
├── customers.php
├── payments.php
├── reports.php
├── reception-dashboard.php
├── customer-dashboard.php
├── chef-dashboard.php
├── config.php
├── language_helper.php
└── README.md
---

## 🗄️ Database Schema

### Tables
| Table | Description |
|-------|-------------|
| users | User accounts (admin, reception, customer, chef) |
| rooms | Room details |
| customers | Customer profiles |
| bookings | Booking records |
| payments | Payment records |
| menu_items | Room service menu |
| room_service_orders | Room service orders |
| reviews | Customer reviews |
| staff_courses | Staff assignments |

---

## 🔧 Installation

### 1. Clone the Repository
`bash
git clone https://github.com/YOUR_USERNAME/trinity-hotel.git