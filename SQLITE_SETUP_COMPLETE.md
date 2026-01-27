# SQLite Database Configuration - Complete

## Status: ✅ COMPLETE

Your Court Reservation System is now fully configured to use **SQLite** instead of MySQL.

## What Was Done

### 1. Database Configuration
- **File**: `config/database.php`
- Configured to use SQLite with PDO
- Database file location: `storage/database.sqlite`
- Foreign keys enabled by default

### 2. Environment Variables (.env)
```
DB_CONNECTION=sqlite
DB_DATABASE=storage/database.sqlite
```

### 3. Database Schema
All 12 tables successfully created:

#### Core Tables:
- **users** - User accounts with authentication
- **courts** - Court/facility listings
- **court_types** - Classification of courts (Basketball, Badminton, etc.)
- **bookings** - Court reservations and booking information
- **payments** - Payment transactions and records

#### Support Tables:
- **reviews** - User reviews and ratings
- **notifications** - User notifications
- **activity_logs** - Admin audit trail
- **court_schedules** - Operating hours and exceptions
- **player_lookups** - Community player finder
- **settings** - System configuration
- **user_blacklists** - User blacklist management

### 4. Sample Data Included

#### Court Types (8):
- Basketball Court
- Badminton Court
- Tennis Court
- Volleyball Court
- Futsal Court
- Covered Court
- Barangay Gym
- Swimming Pool

#### Sample Courts (7):
- QC Badminton Center (2 courts)
- Brgy. San Antonio Basketball Court
- Makati Hoops Arena
- Manila Futsal Club
- Barangay Covered Court - Pasig
- Rizal Tennis Club

#### Default Users:
- **Admin Account**
  - Email: `admin@courtreservation.ph`
  - Password: `password`
  - Role: Admin

- **Test Account**
  - Email: `user@example.com`
  - Password: `password`
  - Role: User

### 5. Migration Files Converted
All migration files converted from MySQL to SQLite syntax:
- `001_create_users_table.sql` - User table creation
- `002_create_courts_table.sql` - Courts and court types
- `003_create_bookings_table.sql` - Booking system
- `004_create_payments_table.sql` - Payment processing
- `005_create_additional_tables.sql` - Supporting tables

## Database File Info
- **Location**: `storage/database.sqlite`
- **Size**: 200 KB (initial)
- **Format**: SQLite 3
- **Encoding**: UTF-8

## Setup Scripts Created
1. **setup.php** - Original setup (still references old MySQL code for reference)
2. **sqlite_setup.php** - New SQLite setup script ✅ (Use this for future setups)

## How to Re-initialize the Database

If you need to reset the database:

```bash
# Delete the current database
Remove-Item storage/database.sqlite -Force

# Re-run the setup
php sqlite_setup.php
```

## Testing the Connection

Run the verification script:
```bash
php check_schema.php
```

Or verify.php for quick status:
```bash
php verify.php
```

## Key Features

### Foreign Key Support
All foreign key constraints are enabled:
- Bookings reference Users and Courts
- Payments reference Bookings and Users
- Reviews reference Users, Courts, and Bookings
- Activity logs track changes across all tables

### Indexes for Performance
Created indexes on:
- User emails and roles
- Court type, city, and active status
- Booking dates and user IDs
- Payment status and references
- Player lookup filters

### Data Validation
SQLite CHECK constraints implemented for:
- Booking status values
- Payment method types
- Notification channels
- User roles
- Rating ranges (1-5)
- Skill levels

## Migration Notes

The application now uses:
- ✅ SQLite (not MySQL)
- ✅ PDO for database abstraction
- ✅ Prepared statements (SQL injection protection)
- ✅ Automatic timestamp management
- ✅ Auto-increment IDs

## Next Steps

1. Use the application as normal
2. The SQLite database will automatically grow as you add data
3. No special maintenance required for SQLite
4. Database file is portable - can be copied to other systems

## Support

For any issues:
1. Check database schema: `php check_schema.php`
2. Verify connection: `php verify.php`
3. Re-run setup if needed: `php sqlite_setup.php`

---
**Setup Date**: January 27, 2026
**Database Version**: SQLite 3
**Configuration Status**: ✅ Complete
