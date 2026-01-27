# 🏀 Court Reservation System - SQLite Edition

A complete Philippine sports facility booking system with user authentication using SQLite database.

**Database: SQLite** (No MySQL setup required!)

## Quick Start

### 1. Database Setup

The system uses **SQLite** - no MySQL needed!

Simply run: `http://localhost:8000/setup.php`

This will:
- ✓ Create SQLite database (`storage/database.sqlite`)
- ✓ Create all tables
- ✓ Add admin account
- ✓ Add test user

### 2. Login Credentials

**Admin Account:**
```
Email: admin@courtreservation.ph
Password: password
```

**Test Account:**
```
Email: user@example.com
Password: password
```

### 3. Access Application

- **Home**: `http://localhost:8000/`
- **Login**: `http://localhost:8000/login`
- **Register**: `http://localhost:8000/register`
- **Setup**: `http://localhost:8000/setup.php`

## Features

✓ User Authentication (Login/Register/Logout)
✓ User Profile Dashboard
✓ Admin Dashboard
✓ Court Management
✓ Booking System
✓ Payment Integration Ready
✓ SQLite Database (No MySQL needed)
✓ Responsive Design
✓ Session Management
✓ Password Hashing (Bcrypt)

## Configuration

Edit `.env` file:

```env
APP_NAME="Court Reservation System"
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=storage/database.sqlite
```

## Project Structure

```
Court-Reservation/
├── app/
│   ├── Http/Controllers/
│   │   └── Auth/
│   │       └── AuthController.php
│   └── Models/
│       └── User.php (SQLite queries)
├── config/
│   ├── app.php
│   └── database.php (SQLite config)
├── resources/views/
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── home/
│   │   └── index.php
│   └── errors/
│       └── 404.php
├── storage/
│   └── database.sqlite (auto-created)
├── public/
│   └── index.php (entry point)
├── .env (configuration)
├── .htaccess (routing)
└── setup.php (database setup)
```

## Database

**SQLite Database:**
- Single file: `storage/database.sqlite` (28KB)
- No server setup required
- Easy to backup (just copy file)
- Foreign keys enabled
- Perfect for development & production

**Tables:**
- `users` - User accounts with authentication

## Requirements

- PHP 7.4+ with PDO SQLite support
- Apache with mod_rewrite
- No MySQL required!

## User Roles

- **Admin** - Full system access + dashboard
- **User** - Book courts + manage reservations
- **Staff** - Court management + payments

## Security

✓ Bcrypt password hashing
✓ Session authentication
✓ Input validation
✓ Prepared statements (SQL injection prevention)
✓ Secure password reset ready
✓ User blacklist system

## How to Test

### 1. Setup (First Time)
Visit: `http://localhost:8000/setup.php`

### 2. Login as Admin
- Email: `admin@courtreservation.ph`
- Password: `password`

### 3. Register New User
- Visit: `/register`
- Create account with your details

### 4. Test Features
- Home page
- User dashboard
- Admin controls
- Logout

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Database not found | Run `http://localhost:8000/setup.php` |
| Login fails | Clear cookies, check DB exists |
| 404 errors | Enable Apache mod_rewrite |
| Permission denied | Make `storage/` writable |

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: SQLite 3
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Custom lightweight MVC
- **Authentication**: Session-based

## File Permissions

Ensure these directories are writable:
```
storage/              (755 or writable)
```

## Backup Database

SQLite database is a single file, easy to backup:
```bash
# Copy the file
cp storage/database.sqlite storage/database.sqlite.backup
```

## Contact & Support

For issues or questions about the system, check:
- `.env` configuration
- Database permissions
- Apache mod_rewrite status
- Browser console for errors

---

**Version**: 1.0  
**Database**: SQLite 3  
**PHP**: 7.4+  
**License**: MIT
