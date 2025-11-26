# Struktur Folder Resources - Toko Buku Online

## Folder Structure

```
resources/
├── css/
│   ├── app.css                    # Main CSS (existing)
│   ├── admin/
│   │   └── admin.css             # Admin dashboard styles (dark theme)
│   └── user/
│       └── user.css              # User/customer styles (bookstore theme)
│
├── js/
│   ├── app.js                    # Main JS (existing)
│   ├── bootstrap.js              # Bootstrap (existing)
│   ├── admin/
│   │   └── admin.js              # Admin dashboard JavaScript
│   └── user/
│       └── user.js               # User frontend JavaScript
│
└── views/
    ├── welcome.blade.php         # Original welcome page
    ├── admin/
    │   ├── layout.blade.php      # Admin master layout with sidebar
    │   └── dashboard.blade.php   # Admin dashboard homepage
    ├── user/
    │   ├── layout.blade.php      # User master layout with navbar
    │   └── home.blade.php        # User homepage
    └── auth/
        ├── layout.blade.php      # Auth pages layout
        ├── login.blade.php       # Login page
        └── register.blade.php    # Registration page
```

## User Accounts Created

### Admin Account
- **Email**: admin@tokobuku.com
- **Password**: admin123
- **Role**: admin
- **Access**: Admin Dashboard (/admin)

### User Account
- **Email**: user@tokobuku.com
- **Password**: user123
- **Role**: user
- **Access**: Customer Frontend (/user)

## Features

### Admin Features (Dark Professional Theme)
- ✅ Sidebar navigation
- ✅ Dashboard with statistics cards
- ✅ Manage books, categories, orders
- ✅ User management
- ✅ Payment verification
- ✅ Reports

### User Features (Bookstore Theme)
- ✅ Top navbar with search
- ✅ Shopping cart
- ✅ Book catalog
- ✅ Categories browsing
- ✅ Order management
- ✅ User profile

## Middleware

### AdminMiddleware
- Protects admin routes
- Redirects non-admin users
- Requires authentication

### UserMiddleware
- Protects user routes
- Redirects non-user roles
- Requires authentication

## CSS Theme Colors

### Admin Theme
- Primary: #2c3e50 (Dark Blue)
- Accent: #3498db (Blue)
- Background: #1a252f (Very Dark)
- Success: #27ae60
- Danger: #e74c3c

### User Theme
- Primary: #8b4513 (Saddle Brown)
- Secondary: #d2691e (Chocolate)
- Accent: #ff8c42 (Orange)
- Light: #f5f1ed (Beige)
- Success: #2ecc71

## Next Steps

1. Create Controllers for:
   - AuthController (login, register, logout)
   - AdminController (dashboard, CRUD operations)
   - UserController (frontend, cart, orders)

2. Setup Routes in web.php:
   - Auth routes (login, register, logout)
   - Admin routes (with 'admin' middleware)
   - User routes (with 'user' middleware)

3. Implement Features:
   - Book catalog and search
   - Shopping cart functionality
   - Order processing
   - Payment system
   - Admin CRUD operations

## No Errors Detected
✅ All folders created successfully
✅ All views created successfully
✅ Middleware registered correctly
✅ Users seeded successfully
✅ Database structure intact
