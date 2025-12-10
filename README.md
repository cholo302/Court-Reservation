# 🏀 Court Reservation System

**Philippine Sports Facility Booking with QR Payment Integration**

A comprehensive web application for booking sports courts in the Philippines, featuring GCash/Maya QR payment integration, real-time availability checking, and an admin dashboard for facility management.

## Features

### For Users
- 📅 **Easy Booking** - Browse available courts and book in a few clicks
- 💳 **QR Payments** - Pay via GCash, Maya, or Bank QR Ph
- 📱 **QR Entry Code** - Get a unique QR code for court entry
- 📧 **Notifications** - Receive SMS and email confirmations
- ⭐ **Reviews** - Rate and review courts after your game
- 📊 **Booking History** - Track all your reservations

### For Admins
- 📈 **Dashboard** - Real-time stats and analytics
- 🏟️ **Court Management** - Add, edit, and manage courts
- 💰 **Payment Verification** - Approve payments manually or automatically
- 👥 **User Management** - Handle users and blacklist no-shows
- 📋 **Reports** - Export booking and revenue reports
- 📲 **QR Scanner** - Verify customer entry at the facility

## Tech Stack

- **Backend**: PHP 7.4+ (Custom MVC Framework)
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, Tailwind CSS, JavaScript
- **Payment**: PayMongo API (GCash, Maya integration)
- **SMS**: Semaphore API
- **QR Codes**: QuickChart.io API

## Installation

### Prerequisites
- XAMPP (or similar with Apache, MySQL, PHP 7.4+)
- Web browser

### Quick Setup

1. **Clone/Download to XAMPP htdocs**
   ```
   C:\xampp\htdocs\Court-Reservation
   ```

2. **Start XAMPP**
   - Start Apache
   - Start MySQL

3. **Run Setup Script**
   
   Open in browser:
   ```
   http://localhost/Court-Reservation/setup.php
   ```
   
   This will:
   - Create the database
   - Run all migrations
   - Seed sample data
   - Create admin user

4. **Access the Application**
   - Homepage: `http://localhost/Court-Reservation/`
   - Admin: `http://localhost/Court-Reservation/admin`

## Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@courtreservation.ph | admin123 |
| Test User | test@example.com | password123 |

## Project Structure

```
Court-Reservation/
├── app/
│   ├── Controllers/     # Request handlers
│   ├── Models/          # Database models
│   ├── Router.php       # URL routing
│   └── helpers.php      # Helper functions
├── assets/
│   ├── css/            # Stylesheets
│   └── js/             # JavaScript files
├── config/
│   ├── app.php         # App configuration
│   └── database.php    # Database settings
├── database/
│   ├── migrations/     # SQL schema files
│   └── seed.php        # Sample data seeder
├── routes/
│   ├── web.php         # Web routes
│   └── api.php         # API routes
├── storage/
│   ├── courts/         # Court images
│   ├── proofs/         # Payment proofs
│   └── logs/           # Error logs
├── views/
│   ├── admin/          # Admin views
│   ├── auth/           # Login/Register
│   ├── bookings/       # Booking pages
│   ├── courts/         # Court listings
│   ├── layouts/        # Page templates
│   └── profile/        # User profile
├── index.php           # Entry point
├── setup.php           # Installation script
└── .htaccess           # URL rewriting
```

## Configuration

### Payment Integration (config/app.php)

```php
'paymongo' => [
    'public_key' => 'pk_test_xxx',
    'secret_key' => 'sk_test_xxx',
],
```

### SMS Notifications

```php
'semaphore' => [
    'api_key' => 'your_api_key',
    'sender_name' => 'CourtRes',
],
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/courts/{id}/slots | Get available time slots |
| POST | /api/calculate-price | Calculate booking price |
| POST | /api/verify-entry | Verify entry QR code |
| GET | /api/bookings/{id}/status | Check booking status |

## Booking Flow

1. **User browses courts** → Select court type and location
2. **Select date & time** → Choose available slot
3. **Review & confirm** → Enter player count, notes
4. **Payment** → Scan QR code (GCash/Maya)
5. **Upload proof** → Submit payment screenshot
6. **Admin verifies** → Payment approved
7. **Get entry QR** → Show at court entrance
8. **Play!** → Enjoy your game

## Admin Features

### Dashboard
- Today's bookings count
- Monthly revenue tracking
- Pending payment approvals
- User statistics

### Court Management
- Add/edit courts with images
- Set regular, peak, and weekend pricing
- Define operating hours
- Enable/disable courts

### Reports
- Revenue by period
- Booking trends
- Payment method analytics
- Export to Excel

## Customization

### Pricing Rules (app/helpers.php)
```php
function isPeakHour($time) {
    $hour = (int)date('G', strtotime($time));
    return $hour >= 17 && $hour < 21; // 5 PM - 9 PM
}
```

### Color Theme (assets/css/app.css)
```css
:root {
    --ph-blue: #0038a8;   /* Philippine flag blue */
    --ph-red: #ce1126;    /* Philippine flag red */
    --ph-yellow: #fcd116; /* Philippine flag yellow */
}
```

## Security

- CSRF protection on all forms
- Password hashing (bcrypt)
- Prepared SQL statements
- Input sanitization
- Session management

## Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the MIT license.

## Support

For issues and feature requests, please create an issue in the repository.

---

**Made with ❤️ for Philippine sports enthusiasts**
