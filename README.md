# 🏠 RoomFinder - Advanced Roommate & Apartment Finder

Hey there! Welcome to **RoomFinder**, your one-stop solution for finding the perfect roommate or apartment. This web app connects room seekers with landlords, making the whole rental process super smooth and easy.

## 🎯 What's This All About?

RoomFinder is a full-featured web application that helps:

- **Room Seekers**: Find available rooms, connect with compatible roommates, and manage rentals
- **Landlords**: List properties, manage inquiries, and handle rental payments
- **Admins**: Oversee the entire platform, approve listings, and manage users

Think of it as a dating app... but for finding your next home! 😄

## ✨ Cool Features

### For Room Seekers

- 🔍 **Browse Rooms**: Filter by location, price, and amenities
- 💕 **Roommate Matching**: Swipe-style matching based on preferences with compatibility percentage
- 💬 **Real-time Messaging**: Chat with landlords and potential roommates
- 📅 **Appointment Booking**: Schedule property viewings
- 💳 **Secure Payments**: Pay rent online (Stripe integration)
- ⭐ **Save Favorites**: Bookmark your favorite listings

### For Landlords

- 📝 **List Properties**: Easy listing creation with image uploads
- 💰 **Rental Management**: Track payments, view proof of payment, and manage tenant information
- 🔔 **Notifications**: Get notified of new rentals with a badge indicator
- 📨 **Inquiry Management**: Handle messages from interested seekers
- 📊 **Dashboard**: View your listings and rental stats

### For Admins

- 👥 **User Management**: Approve, verify, or ban users
- 🏘️ **Listing Approval**: Review and approve property listings
- 📊 **Analytics**: View platform-wide statistics
- 📧 **Notifications**: Monitor email logs and system activities

## 🛠️ Tech Stack

- **Frontend**: HTML, CSS, JavaScript (Vanilla)
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Email Service**: EmailJS
- **Payment**: Stripe API
- **Icons**: Lucide Icons
- **Server**: WAMP/XAMPP

## 📁 Folder Structure

Here's what's in the box:

```
Advanced-Roommate-Apartment-Finder/
│
├── 📂 app/                          # Application core
│   ├── 📂 config/                   # Database & other configs
│   ├── 📂 controllers/              # API endpoints & logic
│   │   ├── AppointmentController.php
│   │   ├── AuthController.php
│   │   ├── ListingController.php
│   │   ├── MatchController.php
│   │   ├── MessageController.php
│   │   ├── NotificationController.php
│   │   ├── PasswordResetController.php
│   │   ├── ProfileController.php
│   │   ├── RentalController.php
│   │   ├── ReportController.php
│   │   └── 📂 admin/               # Admin-specific controllers
│   ├── 📂 core/                    # Core utilities
│   ├── 📂 models/                  # Database models
│   │   ├── Appointment.php
│   │   ├── BaseModel.php
│   │   ├── Listing.php
│   │   ├── Match.php
│   │   ├── Message.php
│   │   ├── Notification.php
│   │   ├── Report.php
│   │   ├── Rental.php
│   │   ├── SavedListing.php
│   │   └── User.php
│   ├── 📂 services/                # Business logic services
│   └── 📂 views/                   # Frontend pages
│       ├── 📂 admin/               # Admin dashboard pages
│       ├── 📂 auth/                # Login, register, password reset
│       ├── 📂 landlord/            # Landlord-specific pages
│       ├── 📂 seeker/              # Room seeker pages
│       ├── 📂 public/              # Public pages (landing, login)
│       └── 📂 includes/            # Shared components (navbar)
│
├── 📂 config/                       # Configuration files
│   └── emailjs.php                 # EmailJS credentials
│
├── 📂 database/                     # Database files
│   ├── 📂 schema/                  # SQL schema files
│   │   └── RoomFinder.sql          # Main database schema
│   ├── 📂 migrations/              # Database updates
│   └── 📂 seeds/                   # Sample data
│
├── 📂 public/                       # Publicly accessible files
│   ├── 📂 assets/                  # CSS, JS, images
│   │   ├── 📂 css/                 # Stylesheets
│   │   ├── 📂 js/                  # JavaScript files
│   │   └── 📂 images/              # Static images
│   ├── 📂 uploads/                 # User-uploaded files
│   │   ├── 📂 listings/            # Property images
│   │   ├── 📂 profiles/            # Profile photos
│   │   └── 📂 payments/            # Payment receipts
│   └── index.php                   # Landing page entry point
│
└── 📂 storage/                      # Temporary storage & logs
```

## 🚀 Installation Guide

### Prerequisites

Make sure you have these installed:

- **WAMP Server** (or XAMPP) - [Download WAMP](https://www.wampserver.com/en/)
- **Modern Browser** (Chrome, Firefox, Edge)
- A cup of coffee ☕ (optional but recommended)

### Step 1: Install WAMP

1. Download WAMP from [wampserver.com](https://www.wampserver.com/en/)
2. Run the installer
3. Install to `C:\wamp64` (default location)
4. Start WAMP - you should see a green icon in your system tray

### Step 2: Clone/Download the Project

1. **Option A: Using Git**

   ```bash
   cd C:\wamp64\www
   git clone https://github.com/codeyatoh/Advanced-Roommate-Apartment-Finder-Web-App-with-SMS-Admin-Panel-.git
   ```

2. **Option B: Manual Download**
   - Download the ZIP from GitHub
   - Extract to `C:\wamp64\www\Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-`

### Step 3: Set Up the Database

1. **Open phpMyAdmin**

   - Click the WAMP icon in your system tray
   - Select "phpMyAdmin"
   - Or go to: `http://localhost/phpmyadmin`

2. **Create the Database**

   - Click "New" in the left sidebar
   - Database name: `roomfinder`
   - Collation: `utf8mb4_general_ci`
   - Click "Create"

3. **Import the SQL File**
   - Select your `roomfinder` database from the left sidebar
   - Click the "Import" tab at the top
   - Click "Choose File"
   - Navigate to: `C:\wamp64\www\Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-\database\schema\RoomFinder.sql`
   - Scroll down and click "Go"
   - Wait for success message ✅

### Step 4: Configure Database Connection

1. Open `app/config/Database.php` in your text editor
2. Make sure the settings match:
   ```php
   private $host = "localhost";
   private $db_name = "roomfinder";
   private $username = "root";
   private $password = "";  // Empty for WAMP default
   ```

### Step 5: Configure EmailJS (Optional)

If you want email notifications to work:

1. Sign up at [EmailJS](https://www.emailjs.com/)
2. Create a service and templates
3. Update `config/emailjs.php` with your credentials:
   ```php
   return [
       'public_key' => 'your_public_key',
       'service_id' => 'your_service_id',
       'otp_template_id' => 'your_otp_template_id',
       'payment_template_id' => 'your_payment_template_id'
   ];
   ```

## 🎬 Let's Run It!

### Starting the Application

1. **Make sure WAMP is running** (green icon)
2. Open your browser
3. Go to:
   ```
   http://localhost/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/public/landing.php
   ```

### Default Login Credentials

After importing the database, you can use these accounts:

**Admin Account:**

- Email: `admin@roomfinder.com`
- Password: `password`

**Test Seeker:**

- Email: `seeker@test.com`
- Password: `password123`

**Test Landlord:**

- Email: `landlord@test.com`
- Password: `password123`

## 🗺️ Where to Go

Here are the main entry points:

| Role                   | URL                                 | Description           |
| ---------------------- | ----------------------------------- | --------------------- |
| **Landing**            | `/app/views/public/landing.php`     | Main landing page     |
| **Login**              | `/app/views/public/login.php`       | User login            |
| **Register**           | `/app/views/public/register.php`    | New user registration |
| **Seeker Dashboard**   | `/app/views/seeker/dashboard.php`   | Room seeker home      |
| **Landlord Dashboard** | `/app/views/landlord/dashboard.php` | Landlord home         |
| **Admin Dashboard**    | `/app/views/admin/dashboard.php`    | Admin panel           |

## 🔧 Configuration Files

Important config files you might need to edit:

| File                                   | Purpose             | What to Change                      |
| -------------------------------------- | ------------------- | ----------------------------------- |
| `app/config/Database.php`              | Database connection | Host, DB name, credentials          |
| `config/emailjs.php`                   | Email service       | EmailJS keys & template IDs         |
| `app/controllers/RentalController.php` | Payment settings    | Stripe API keys (if using payments) |

## 🐛 Troubleshooting

**Problem: Can't access localhost**

- Make sure WAMP is running (green icon)
- Check if Apache is started in WAMP menu

**Problem: Database connection error**

- Verify database name is `roomfinder`
- Check if MySQL is running in WAMP
- Confirm credentials in `Database.php`

**Problem: Images not uploading**

- Check folder permissions for `public/uploads/`
- Make sure folder exists

**Problem: Emails not sending**

- Verify EmailJS configuration in `config/emailjs.php`
- Check browser console for errors

## 📝 Quick Start Tutorial

1. **Start WAMP** - Wait for green icon
2. **Access Landing Page** - Go to the URL above
3. **Register an Account** - Choose your role (Seeker/Landlord)
4. **Explore the Dashboard** - Based on your role
5. **Have Fun!** 🎉

## 🤝 Contributing

Found a bug? Have a cool feature idea? Feel free to:

1. Fork the repo
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📄 License

This project is open source. Use it, modify it, make it your own!

## 💬 Need Help?

Stuck? Confused? We've all been there! Check out:

- The `SYSTEM_OVERVIEW.md` for technical details
- Database schema in `database/schema/RoomFinder.sql`
- Or just ask! Open an issue on GitHub

---

**Made with ❤️ for finding the perfect home**

Happy Room Hunting! 🏠✨
