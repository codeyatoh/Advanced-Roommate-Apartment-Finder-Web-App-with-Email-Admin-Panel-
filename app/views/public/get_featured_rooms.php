<?php
/**
 * Helper file to fetch featured rooms for landing page
 * Returns array of up to 3 available listings with images
 */

require_once __DIR__ . '/../../models/Listing.php';

// Initialize Listing model
$listingModel = new Listing();

// Fetch available listings (limited to 3 for featured section)
$allListings = $listingModel->search();
$featuredListings = array_slice($allListings, 0, 3);

/**
 * Get availability badge text based on available_from date
 * @param array $listing
 * @return string
 */
function getAvailabilityBadge($listing) {
    if (!empty($listing['available_from'])) {
        $availableDate = new DateTime($listing['available_from']);
        $now = new DateTime();
        
        if ($availableDate <= $now) {
            return 'Available Now';
        } else {
            return 'Available ' . $availableDate->format('M j');
        }
    }
    return 'Available Now';
}

/**
 * Get listing image with fallback
 * @param array $listing
 * @return string
 */
function getListingImage($listing) {
    if (!empty($listing['primary_image'])) {
        return htmlspecialchars($listing['primary_image']);
    }
    // Fallback to Unsplash placeholder based on index
    $placeholders = [
        'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800',
        'https://images.unsplash.com/photo-1502672260066-6bc2c9f0e6c7?w=800',
        'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800'
    ];
    static $index = 0;
    $image = $placeholders[$index % 3];
    $index++;
    return $image;
}

/**
 * Format price to peso currency
 * @param float $price
 * @return string
 */
function formatPrice($price) {
    return '₱' . number_format($price, 0);
}
?>
