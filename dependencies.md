# External Dependencies

This document lists all external dependencies required for the RoomFinder application.

## Required CDN Links

### 1. Lucide Icons
Lucide is a modern icon library used throughout the application for UI icons.

**CDN Link:**
```html
<script src="https://unpkg.com/lucide@latest"></script>
```

**Usage:**
After including the script, initialize icons with:
```javascript
lucide.createIcons();
```

**Icons Used:**
- Search
- Home
- Users
- Calendar
- Shield
- Star
- TrendingUp
- Mail
- Lock
- User
- Eye
- EyeOff
- MapPin
- Bed
- Bath
- UserPlus
- ChevronLeft
- ChevronRight

### 2. Google Fonts - Inter
Modern, clean sans-serif font family used for all typography.

**CDN Link:**
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
```

**CSS Usage:**
Already configured in `variables.css`:
```css
--font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
```

## File Structure

### CSS Files (Load in Order)
1. `public/assets/css/variables.css` - Design tokens and CSS custom properties
2. `public/assets/css/globals.css` - Global styles, reset, and utilities
3. `public/assets/css/modules/cards.module.css` - Card component styles
4. `public/assets/css/modules/forms.module.css` - Form component styles
5. `public/assets/css/modules/carousel.module.css` - Carousel component styles
6. `public/assets/css/modules/room-card.module.css` - Room card styles
7. `public/assets/css/modules/navbar.module.css` - Navigation styles
8. `public/assets/css/modules/footer.module.css` - Footer styles
9. Page-specific CSS (load based on page):
   - `public/assets/css/modules/landing.module.css` - Landing page
   - `public/assets/css/modules/register.module.css` - Register page
   - `public/assets/css/modules/login.module.css` - Login page

### JavaScript Files (Load in Order)
1. `public/assets/js/forms.js` - Form validation and password toggle (load first)
2. `public/assets/js/carousel.js` - Carousel functionality
3. Page-specific JS (load based on page):
   - `public/assets/js/register.js` - Register page logic
   - `public/assets/js/login.js` - Login page logic

## Example HTML Head Section

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RoomFinder - Find Your Perfect Room</title>
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- CSS Files -->
  <link rel="stylesheet" href="/assets/css/variables.css">
  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/modules/cards.module.css">
  <link rel="stylesheet" href="/assets/css/modules/forms.module.css">
  <!-- Add other module CSS as needed -->
</head>
<body>
  <!-- Content -->
  
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    lucide.createIcons();
  </script>
  
  <!-- JavaScript Files -->
  <script src="/assets/js/forms.js"></script>
  <!-- Add other JS files as needed -->
</body>
</html>
```

## Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- CSS Features used:
  - CSS Custom Properties (CSS Variables)
  - CSS Grid
  - Flexbox
  - backdrop-filter (for glassmorphism)
  - CSS Animations

**Note:** For older browsers, consider adding autoprefixer for vendor prefixes.

## No Build Step Required

This implementation uses vanilla HTML, CSS, and JavaScript with no build tools required. Simply include the files in the correct order and the application will work.
