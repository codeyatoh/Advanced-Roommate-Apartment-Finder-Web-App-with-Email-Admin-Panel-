# Database Schema Documentation

## Overview

This folder contains the complete database schema for the **Roommate Finder** application, organized by functional domains.

## Database Name

`roommate_finder`

## Installation

### Quick Install (All Tables)

Run the complete installation script:

```bash
mysql -u your_username -p < install.sql
```

### Individual Schema Files

You can also run schema files individually in order:

```bash
mysql -u your_username -p roommate_finder < schema/01_users.sql
mysql -u your_username -p roommate_finder < schema/02_listings.sql
mysql -u your_username -p roommate_finder < schema/03_roommate.sql
mysql -u your_username -p roommate_finder < schema/04_interactions.sql
mysql -u your_username -p roommate_finder < schema/05_admin.sql
```

## Schema Organization

### 📁 schema/

Contains individual SQL files organized by domain:

#### `01_users.sql` - User Management

- `users` - Core user accounts (seekers, landlords, admins)

#### `02_listings.sql` - Property Listings

- `listings` - Room/apartment listings
- `listing_images` - Property photos

#### `03_roommate.sql` - Roommate Matching

- `roommate_profile` - User preferences and lifestyle
- `roommate_matches` - Compatibility matches between users

#### `04_interactions.sql` - User Interactions

- `favorites` - Saved listings
- `inquiries` - Messages to landlords
- `appointments` - Viewing schedules
- `messages` - Chat system

#### `05_admin.sql` - Admin & System

- `notifications` - Email notification logs
- `reports` - User complaints/reports
- `admin_logs` - Admin activity tracking

## Table Relationships

```
users (1) ─────── (n) listings
  │                      │
  │                      ├── (n) listing_images
  │                      ├── (n) favorites
  │                      ├── (n) inquiries
  │                      └── (n) appointments
  │
  ├── (1) roommate_profile
  ├── (n) roommate_matches
  ├── (n) messages (as sender/receiver)
  ├── (n) notifications
  ├── (n) reports (as reporter/target)
  └── (n) admin_logs
```

## Key Features

### CASCADE Deletes

All foreign keys use `ON DELETE CASCADE` to automatically clean up related records when a parent record is deleted.

### Timestamps

Most tables include:

- `created_at` - Auto-populated on insert
- `updated_at` - Auto-updated on modification

### Enums

Status and type fields use ENUMs for data integrity:

- User roles: `room_seeker`, `landlord`, `admin`
- Room types: `apartment`, `studio`, `shared_room`, `private_room`
- Appointment status: `pending`, `approved`, `declined`, `completed`

## Notes

- **Email Notifications Only**: The system uses email notifications (not SMS), though the notifications table retains the `type` enum for flexibility
- **Soft Deletes**: Currently using hard deletes with CASCADE. Consider adding `deleted_at` columns if soft deletes are needed
- **Indexes**: Add indexes on frequently queried columns (e.g., `email`, `location`, foreign keys) for better performance

## Sample Queries

### Get all available listings

```sql
SELECT * FROM listings WHERE availability_status = 'available';
```

### Find compatible roommates

```sql
SELECT u.*, rm.compatibility_score
FROM roommate_matches rm
JOIN users u ON rm.user2_id = u.user_id
WHERE rm.user1_id = ? AND rm.status = 'pending'
ORDER BY rm.compatibility_score DESC;
```

### Get user's appointments

```sql
SELECT a.*, l.title, l.location
FROM appointments a
JOIN listings l ON a.listing_id = l.listing_id
WHERE a.user_id = ?
ORDER BY a.schedule_datetime DESC;
```
