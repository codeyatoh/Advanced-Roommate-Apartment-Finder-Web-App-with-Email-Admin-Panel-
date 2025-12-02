# 🏗️ RoomFinder - System Overview

Welcome to the technical deep dive! This document explains how RoomFinder works under the hood. Perfect for developers who want to understand or contribute to the project.

## 📐 System Architecture

RoomFinder follows a classic **MVC (Model-View-Controller)** architecture with some modern twists:

```
┌─────────────┐
│   Browser   │ ← User Interface (Views)
└──────┬──────┘
       │ HTTP Requests
       ↓
┌─────────────────────┐
│   Controllers       │ ← Business Logic & Routing
│  (PHP Classes)      │
└──────┬──────────────┘
       │ Database Queries
       ↓
┌─────────────────────┐
│   Models            │ ← Data Layer
│  (PHP Classes)      │
└──────┬──────────────┘
       │ SQL
       ↓
┌─────────────────────┐
│   MySQL Database    │ ← Data Storage
└─────────────────────┘
```

### Component Breakdown

#### 1. **Views** (Frontend - `app/views/`)

Pure HTML/CSS/JavaScript pages that users interact with. No complex frameworks - just clean, vanilla code!

**Structure:**

- `public/` - Landing page, login, registration (public access)
- `seeker/` - Room seeker dashboard and features
- `landlord/` - Landlord management pages
- `admin/` - Admin control panel
- `includes/` - Shared components (navbar, modals)

#### 2. **Controllers** (Backend - `app/controllers/`)

Handle HTTP requests, process data, and return responses. Think of them as the traffic cops of your app!

**Key Controllers:**

- `AuthController.php` - Handles login/logout/registration
- `ListingController.php` - Manages property listings
- `MatchController.php` - Roommate matching logic
- `MessageController.php` - Real-time messaging
- `RentalController.php` - Payment & rental management
- `ProfileController.php` - User profile updates

#### 3. **Models** (Data Layer - `app/models/`)

Interact with the database. Each model represents a database table.

**Core Models:**

- `User.php` - User accounts & authentication
- `Listing.php` - Property listings
- `Match.php` - Roommate matching
- `Message.php` - Chat messages
- `Rental.php` - Rental agreements & payments
- `Notification.php` - System notifications

#### 4. **Database** (MySQL)

Stores all application data. See schema details below.

## 🗄️ Database Schema

### Main Tables

```sql
users
├── user_id (PK)
├── email
├── password (hashed)
├── first_name
├── last_name
├── role (admin/landlord/room_seeker)
├── is_verified
├── is_active
└── created_at

seeker_profiles
├── profile_id (PK)
├── user_id (FK → users)
├── occupation
├── budget
├── move_in_date
├── preferences (JSON)
├── sleep_schedule
├── social_level
├── cleanliness
└── work_schedule

landlord_profiles
├── profile_id (PK)
├── user_id (FK → users)
├── business_name
├── business_address
├── phone_number
└── verification_status

listings
├── listing_id (PK)
├── landlord_id (FK → users)
├── title
├── description
├── price
├── location
├── room_type
├── amenities (JSON)
├── house_rules (JSON)
├── approval_status (pending/approved/rejected)
└── created_at

listing_images
├── image_id (PK)
├── listing_id (FK → listings)
├── image_url
└── display_order

roommate_matches
├── match_id (PK)
├── seeker_id (FK → users)
├── target_seeker_id (FK → users)
├── action (pass/match)
├── is_mutual (BOOLEAN)
└── created_at

messages
├── message_id (PK)
├── sender_id (FK → users)
├── receiver_id (FK → users)
├── message_content
├── listing_id (FK → listings) [optional]
├── is_read
└── created_at

rentals
├── rental_id (PK)
├── listing_id (FK → listings)
├── seeker_id (FK → users)
├── landlord_id (FK → users)
├── start_date
├── end_date
├── monthly_rent
├── payment_status
├── stripe_payment_id
├── is_seen (BOOLEAN)
└── created_at

notifications
├── notification_id (PK)
├── user_id (FK → users)
├── type (listing_approved/match/message/etc.)
├── title
├── message
├── is_read
└── created_at
```

### Relationships

```
users (1) ──→ (N) listings
users (1) ──→ (N) seeker_profiles
users (1) ──→ (N) landlord_profiles
users (1) ──→ (N) roommate_matches
users (1) ──→ (N) messages (sender)
users (1) ──→ (N) messages (receiver)
listings (1) ──→ (N) listing_images
listings (1) ──→ (N) rentals
rentals (1) ──→ (1) payment
```

## 🔄 User Workflows

### Room Seeker Journey

```
1. Registration
   └→ app/views/public/register.php
      └→ AuthController::register()
         └→ Creates user in database
         └→ Creates seeker_profile

2. Browse Rooms
   └→ app/views/seeker/browse_rooms.php
      └→ ListingController (fetches approved listings)
      └→ Filter by location, price, amenities

3. Save Favorites
   └→ Click "Save" button
      └→ ListingController::toggle_save()
         └→ Adds to saved_listings table

4. Find Roommates
   └→ app/views/seeker/roommate_finder.php
      └→ MatchController::getUnseenProfiles()
      └→ Calculates match percentage
      └→ User swipes: pass or match

5. Send Messages
   └→ Click "Message" on listing/profile
      └→ app/views/seeker/messages.php
      └→ MessageController::send()

6. Book Viewing
   └→ Click "Schedule Viewing"
      └→ AppointmentController::create()

7. Pay Rent
   └→ app/views/seeker/payment_success.php
      └→ RentalController::processPayment()
      └→ Stripe API integration
```

### Landlord Journey

```
1. Registration
   └→ Role: landlord
   └→ Creates landlord_profile

2. Create Listing
   └→ app/views/landlord/listings.php
      └→ ListingController::create()
      └→ Upload images
      └→ Status: pending (awaits admin approval)

3. Manage Inquiries
   └→ app/views/landlord/inquiries.php
      └→ MessageController::getConversation()

4. Track Rentals
   └→ app/views/landlord/rentals.php
      └→ View active rentals
      └→ Check payment status
```

### Admin Journey

```
1. Review Listings
   └→ app/views/admin/listings.php
      └→ ListingController::updateStatus()
      └→ Approve or reject with notes

2. Manage Users
   └→ app/views/admin/users.php
      └→ Verify landlords
      └→ Ban problematic users

3. View Reports
   └→ app/views/admin/reports.php
      └→ Handle user reports
      └→ Take action on violations
```

## 🔐 Authentication & Authorization

### Session Management

```php
// Login flow
AuthController::login()
  ↓
1. Validate credentials
2. Query users table
3. Verify password (password_verify)
4. Create session:
   $_SESSION['user_id'] = $user['user_id'];
   $_SESSION['role'] = $user['role'];
   $_SESSION['first_name'] = $user['first_name'];
```

### Role-Based Access Control

```php
// Middleware checks in controllers
if ($_SESSION['role'] !== 'admin') {
    // Redirect or show error
}
```

**Roles:**

- `admin` - Full system access
- `landlord` - List properties, manage rentals
- `room_seeker` - Browse, match, rent

## 💾 Data Flow Example: Creating a Listing

Let's trace what happens when a landlord creates a listing:

```
1. User fills form
   └→ app/views/landlord/listings.php

2. Form submission (AJAX)
   └→ POST to /app/controllers/ListingController.php?action=create

3. ListingController::create()
   ├→ Validate input data
   ├→ Handle image uploads
   │  └→ Move to public/uploads/listings/
   ├→ Create listing record
   │  └→ Listing::create($data)
   │     └→ INSERT INTO listings (...)
   └→ Create image records
      └→ foreach images: INSERT INTO listing_images

4. Response
   └→ JSON: { success: true, message: "..." }

5. Frontend updates
   └→ Show success notification
   └→ Redirect to listings page
```

## 🎨 Frontend Architecture

### CSS Organization

```
public/assets/css/
├── variables.css      # Color palette, spacing
├── globals.css        # Reset, base styles
└── modules/
    ├── navbar.module.css
    ├── cards.module.css
    ├── forms.module.css
    ├── profile-card.module.css
    └── messaging.module.css
```

**Design Philosophy:**

- Modular CSS (scoped to components)
- CSS variables for theming
- No preprocessors (pure CSS)
- Mobile-first responsive design

### JavaScript Approach

**No frameworks!** We use vanilla JavaScript with:

- Fetch API for AJAX
- ES6+ features
- Event delegation
- Module pattern for organization

Example:

```javascript
// Real-time messaging
async function sendMessage(receiverId, message) {
  const response = await fetch(
    "/app/controllers/MessageController.php?action=send",
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ receiver_id: receiverId, message }),
    }
  );
  return response.json();
}
```

## 🔌 External APIs

### EmailJS

- **Purpose**: Send emails (OTPs, notifications, receipts)
- **Config**: `config/emailjs.php`
- **Usage**:
  - OTP for password reset
  - Payment receipts
  - Match notifications

### Stripe (Optional)

- **Purpose**: Payment processing
- **Integration**: `RentalController.php`
- **Usage**: Monthly rent payments

### Lucide Icons

- **Purpose**: Icon library
- **CDN**: Loaded in views
- **Usage**: UI icons across the app

## 🔄 Matching Algorithm

The roommate matching uses the **Jaccard Index** for compatibility:

```php
// app/views/seeker/roommate_finder.php

$myPreferences = ['clean', 'quiet', 'no_smoking'];
$theirPreferences = ['clean', 'social', 'no_smoking'];

$intersection = array_intersect($myPreferences, $theirPreferences);
// ['clean', 'no_smoking'] = 2 items

$union = array_unique(array_merge($myPreferences, $theirPreferences));
// ['clean', 'quiet', 'no_smoking', 'social'] = 4 items

$matchPercentage = (count($intersection) / count($union)) * 100;
// (2 / 4) * 100 = 50%
```

**Color Coding:**

- 🟢 80-100%: High Match (Green)
- 🟠 50-79%: Medium Match (Orange)
- 🔴 0-49%: Low Match (Red)

## 📊 Performance Considerations

### Database Optimization

- Indexes on foreign keys
- Indexes on frequently queried columns (email, user_id)
- Soft deletes (is_active flag) instead of hard deletes

### Caching Strategy

- Session-based user data caching
- Image optimization (upload size limits)
- Lazy loading for listings

### Security Measures

- Password hashing (bcrypt via PHP's `password_hash()`)
- SQL injection prevention (prepared statements)
- XSS protection (htmlspecialchars on output)
- CSRF tokens (session-based)
- File upload validation

## 🧪 Testing Approach

### Manual Testing Checklist

- [ ] Registration (seeker, landlord)
- [ ] Login/Logout
- [ ] Password reset flow
- [ ] Create listing (landlord)
- [ ] Browse & filter listings
- [ ] Roommate matching (swipe)
- [ ] Messaging
- [ ] Appointment booking
- [ ] Payment flow
- [ ] Admin approval workflow

## 🚀 Deployment Tips

### For Production

1. **Environment Variables**

   - Move sensitive config to `.env` file
   - Use `getenv()` instead of hardcoded values

2. **Security Hardening**

   - Enable HTTPS
   - Set secure session cookies
   - Implement rate limiting
   - Add CAPTCHA to forms

3. **Performance**

   - Enable opcode caching (OPcache)
   - Use CDN for static assets
   - Minify CSS/JS
   - Enable gzip compression

4. **Monitoring**
   - Set up error logging
   - Monitor database performance
   - Track user analytics

## 🛠️ Development Workflow

### Local Development

1. Start WAMP
2. Access via `localhost`
3. Use browser DevTools for debugging
4. Check PHP error logs in `C:\wamp64\logs\php_error.log`

### Making Changes

1. Edit files
2. Refresh browser (no build step!)
3. Check database if CRUD operations
4. Test user flow end-to-end

### Git Workflow

```bash
# Create feature branch
git checkout -b feature/my-new-feature

# Make changes
git add .
git commit -m "Add: Description of changes"

# Push
git push origin feature/my-new-feature
```

## 📚 Common Patterns

### Controller Pattern

```php
class MyController {
    public function handleRequest() {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'create':
                $this->create();
                break;
            case 'update':
                $this->update();
                break;
            // ...
        }
    }

    private function create() {
        // Handle creation
        $this->jsonResponse(['success' => true]);
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
```

### Model Pattern

```php
class MyModel extends BaseModel {
    protected $table = 'my_table';

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}
```

## 🐛 Common Issues & Solutions

**Issue**: Database connection error

- Check if MySQL is running in WAMP
- Verify database name in `Database.php`
- Ensure user has proper permissions

**Issue**: Session not persisting

- Check `session_start()` is called
- Verify session cookie settings
- Check browser cookie settings

**Issue**: Images not uploading

- Check folder permissions (777 for uploads/)
- Verify max upload size in `php.ini`
- Check file type validation

**Issue**: Emails not sending

- Verify EmailJS configuration
- Check browser console for errors
- Ensure internet connection

## 🎓 Learning Resources

Want to understand the code better?

- **PHP**: [PHP.net Documentation](https://www.php.net/manual/en/)
- **MySQL**: [MySQL Tutorial](https://www.mysqltutorial.org/)
- **JavaScript**: [MDN Web Docs](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
- **MVC Pattern**: [MVC Explained](https://www.freecodecamp.org/news/model-view-controller-mvc-explained/)

## 🔮 Future Enhancements

Ideas for improvement:

- [ ] Email notifications via Mailgun/SendGrid
- [ ] Advanced search with Elasticsearch
- [ ] Mobile app (React Native)
- [ ] Real-time notifications (WebSockets)
- [ ] AI-powered roommate recommendations
- [ ] Virtual property tours
- [ ] Multi-language support
- [ ] Dark mode toggle
- [ ] Export reports to PDF

---

**Built with 💻 and lots of ☕**

Got questions? Check the README.md or open an issue!
