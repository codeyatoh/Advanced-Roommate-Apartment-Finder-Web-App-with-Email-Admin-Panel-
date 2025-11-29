<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Listing - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/variables.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/globals.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/navbar.module.css">
    <link rel="stylesheet" href="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/public/assets/css/modules/landlord.module.css">
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body>
    <div class="landlord-page">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="landlord-container-narrow">
            <!-- Header -->
            <div class="page-header animate-slide-up" style="display: block; margin-bottom: 2rem;">
                <h1 class="page-title">Add New Listing</h1>
                <p class="page-subtitle">Fill in the details to list your property</p>
            </div>

            <form id="addListingForm" class="animate-slide-up add-listing-grid" method="POST" action="/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/controllers/ListingController.php?action=create">
                <input type="hidden" name="room_type" value="private_room">
                <div id="formStatus" style="grid-column: 1 / -1; display: none; padding: 0.75rem 1rem; border-radius: 0.75rem;"></div>
                <!-- Image Upload -->
                <div class="glass-card add-listing-card add-listing-card-full">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Property Images</h2>
                    <div id="imageGrid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
                        <!-- Images will be added here dynamically -->
                        <label class="image-upload-label" style="width: 100%; height: 8rem; border: 2px dashed rgba(0,0,0,0.2); border-radius: 0.75rem; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                            <i data-lucide="upload" style="width: 2rem; height: 2rem; color: rgba(0,0,0,0.4); margin-bottom: 0.5rem;"></i>
                            <span style="font-size: 0.875rem; color: rgba(0,0,0,0.6);">Upload Image</span>
                            <input type="file" accept="image/*" multiple id="imageInput" name="images[]" style="display: none;">
                        </label>
                    </div>
                    <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5);">Upload up to 10 images. First image will be the cover photo.</p>
                </div>

                <div class="add-listing-columns">
                    <div class="add-listing-column">
                        <!-- Basic Information -->
                        <div class="glass-card add-listing-card">
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Basic Information</h2>
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                <div>
                                    <label class="form-label">Property Title</label>
                                    <div class="input-wrapper">
                                        <i data-lucide="home" class="input-icon-blue"></i>
                                        <input type="text" name="title" class="form-input with-icon" placeholder="e.g., Modern Studio in Downtown" required>
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-input" placeholder="Describe your property..." style="min-height: 120px; resize: vertical;" required></textarea>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div>
                                        <label class="form-label">Monthly Rent</label>
                                        <div class="input-wrapper">
                                            <i data-lucide="coins" class="input-icon-blue"></i>
                                            <input type="number" name="price" class="form-input with-icon" placeholder="1200" min="0" step="50" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label">Security Deposit</label>
                                        <div class="input-wrapper">
                                            <i data-lucide="shield" class="input-icon-blue"></i>
                                            <input type="number" name="security_deposit" class="form-input with-icon" placeholder="1200" min="0" step="50">
                                        </div>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div>
                                        <label class="form-label">Available From</label>
                                        <input type="date" name="available_from" class="form-input">
                                    </div>
                                    <div style="display: flex; align-items: flex-end;">
                                        <label class="glass-subtle" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: 0.75rem; cursor: pointer; transition: all 0.2s; width: 100%; height: 3rem;">
                                            <input type="checkbox" name="utilities_included" value="1" style="width: 1rem; height: 1rem; border-radius: 0.25rem; accent-color: var(--deep-blue);">
                                            <span style="font-size: 0.875rem; font-weight: 500; color: #000;">Utilities Included</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label">Full Address</label>
                                    <div class="input-wrapper">
                                    <i data-lucide="map-pin" class="input-icon-blue"></i>
                                    <input type="text" name="location" class="form-input with-icon" placeholder="e.g., 123 Market St, San Francisco, CA" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amenities -->
                        <div class="glass-card add-listing-card">
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Amenities</h2>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="wifi">
                            <i data-lucide="wifi"></i>
                            <span>WiFi</span>
                        </label>
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="parking">
                            <i data-lucide="car"></i>
                            <span>Parking</span>
                        </label>
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="kitchen">
                            <i data-lucide="coffee"></i>
                            <span>Kitchen</span>
                        </label>
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="gym">
                            <i data-lucide="dumbbell"></i>
                            <span>Gym</span>
                        </label>
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="air_conditioning">
                            <i data-lucide="wind"></i>
                            <span>Air Conditioning</span>
                        </label>
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="heating">
                            <i data-lucide="flame"></i>
                            <span>Heating</span>
                        </label>
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="washer_dryer">
                            <i data-lucide="shirt"></i>
                            <span>Washer/Dryer</span>
                        </label>
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="dishwasher">
                            <i data-lucide="utensils"></i>
                            <span>Dishwasher</span>
                        </label>
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="elevator">
                            <i data-lucide="arrow-up-circle"></i>
                            <span>Elevator</span>
                        </label>
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="balcony">
                            <i data-lucide="sun"></i>
                            <span>Balcony/Patio</span>
                        </label>
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="pool">
                            <i data-lucide="waves"></i>
                            <span>Pool</span>
                        </label>
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="security">
                            <i data-lucide="shield-check"></i>
                            <span>Security System</span>
                        </label>
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="tv">
                            <i data-lucide="tv"></i>
                            <span>TV</span>
                        </label>
                        <label class="glass-subtle amenity-option">
                            <input type="checkbox" name="amenities[]" value="essentials">
                            <i data-lucide="package"></i>
                            <span>Essentials</span>
                        </label>
                    </div>
                        </div>
                    </div>

                    <div class="add-listing-column">
                        <!-- Property Details -->
                        <div class="glass-card add-listing-card">
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Property Details</h2>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                                <div>
                                    <label class="form-label">Bedrooms</label>
                                    <select name="bedrooms" class="form-input">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4+</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Bathrooms</label>
                                    <select name="bathrooms" class="form-input">
                                        <option value="1">1</option>
                                        <option value="1.5">1.5</option>
                                        <option value="2">2</option>
                                        <option value="3">3+</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Current Roommates</label>
                                    <select name="current_roommates" class="form-input">
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3+</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- House Rules -->
                        <div class="glass-card add-listing-card">
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 1rem;">House Rules</h2>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: rgba(255,255,255,0.4); border-radius: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i data-lucide="cigarette" class="rule-icon"></i>
                                <span style="font-weight: 500;">Smoking Allowed</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="house_rules[smoking_allowed]" value="1">
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: rgba(255,255,255,0.4); border-radius: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i data-lucide="paw-print" class="rule-icon"></i>
                                    <span style="font-weight: 500;">Pets Allowed</span>
                                </div>
                                <label class="switch">
                                <input type="checkbox" name="house_rules[pets_allowed]" value="1" id="petsAllowedCheckbox" onchange="togglePetsDetails()">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div id="petsDetails" style="display: none; padding-left: 0.75rem;">
                                <input type="text" name="house_rules[pets_details]" class="form-input" placeholder="e.g., Cats only, Small dogs under 20lbs" style="font-size: 0.875rem;">
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: rgba(255,255,255,0.4); border-radius: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i data-lucide="music" class="rule-icon"></i>
                                <span style="font-weight: 500;">No Parties/Events</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="house_rules[no_parties]" value="1">
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: rgba(255,255,255,0.4); border-radius: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i data-lucide="users" class="rule-icon"></i>
                                <span style="font-weight: 500;">No Overnight Guests</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="house_rules[no_guests]" value="1">
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: rgba(255,255,255,0.4); border-radius: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i data-lucide="trash-2" class="rule-icon"></i>
                                <span style="font-weight: 500;">Clean Up After Yourself</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="house_rules[clean_up]" value="1">
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: rgba(255,255,255,0.4); border-radius: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i data-lucide="footprints" class="rule-icon"></i>
                                <span style="font-weight: 500;">No Shoes Inside</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="house_rules[no_shoes]" value="1">
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: rgba(255,255,255,0.4); border-radius: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i data-lucide="recycle" class="rule-icon"></i>
                                <span style="font-weight: 500;">Recycling Required</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="house_rules[recycling_required]" value="1">
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: rgba(255,255,255,0.4); border-radius: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i data-lucide="sparkles" class="rule-icon"></i>
                                <span style="font-weight: 500;">Keep Common Areas Clean</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="house_rules[keep_common_areas_clean]" value="1">
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: rgba(255,255,255,0.4); border-radius: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i data-lucide="lightbulb" class="rule-icon"></i>
                                <span style="font-weight: 500;">Turn Off Lights When Leaving</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="house_rules[turn_off_lights]" value="1">
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div>
                            <label class="form-label">Quiet Hours</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="time" name="house_rules[quiet_hours_start]" class="form-input">
                                <span style="color: rgba(0,0,0,0.4);">to</span>
                                <input type="time" name="house_rules[quiet_hours_end]" class="form-input">
                            </div>
                        </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="add-listing-actions">
                    <button type="button" class="btn btn-ghost btn-lg" onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-lg">Publish Listing</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        lucide.createIcons();

        // Image Upload Logic
        const imageInput = document.getElementById('imageInput');
        const imageGrid = document.getElementById('imageGrid');
        const uploadLabel = document.querySelector('.image-upload-label');
        let allFiles = [];

        imageInput.addEventListener('change', (e) => {
            const newFiles = Array.from(e.target.files);
            
            // Append new files
            allFiles = allFiles.concat(newFiles);
            
            // Update input files
            updateImageInput();
            
            // Re-render previews
            renderPreviews();
        });

        function updateImageInput() {
            const dt = new DataTransfer();
            allFiles.forEach(file => dt.items.add(file));
            imageInput.files = dt.files;
        }

        function renderPreviews() {
            // Clear existing previews (keep the label)
            const previews = imageGrid.querySelectorAll('.relative.group');
            previews.forEach(p => p.remove());

            allFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    createImageElement(e.target.result, index);
                };
                reader.readAsDataURL(file);
            });
        }

        function createImageElement(url, index) {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.style.position = 'relative';
            
            div.innerHTML = `
                <img src="${url}" style="width: 100%; height: 8rem; object-fit: cover; border-radius: 0.75rem;">
                <button type="button" class="remove-btn" data-index="${index}" style="position: absolute; top: 0.5rem; right: 0.5rem; padding: 0.25rem; background-color: #ef4444; color: white; border-radius: 9999px; border: none; cursor: pointer; opacity: 0; transition: opacity 0.2s;">
                    <i data-lucide="x" style="width: 1rem; height: 1rem;"></i>
                </button>
            `;

            // Add hover effect for remove button
            div.addEventListener('mouseenter', () => {
                div.querySelector('.remove-btn').style.opacity = '1';
            });
            div.addEventListener('mouseleave', () => {
                div.querySelector('.remove-btn').style.opacity = '0';
            });

            // Remove functionality
            div.querySelector('.remove-btn').addEventListener('click', (e) => {
                const idx = parseInt(e.currentTarget.dataset.index);
                allFiles.splice(idx, 1);
                updateImageInput();
                renderPreviews();
            });

            // Insert before the upload label
            imageGrid.insertBefore(div, uploadLabel);
            lucide.createIcons();
        }

        const formStatus = document.getElementById('formStatus');
        const addListingForm = document.getElementById('addListingForm');
        const submitButton = addListingForm.querySelector('button[type="submit"]');
        let isSubmitting = false;
        addListingForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (isSubmitting) return;
            isSubmitting = true;
            submitButton.disabled = true;
            submitButton.style.opacity = '0.7';
            submitButton.style.cursor = 'not-allowed';

            // Show loading toast
            const loadingToast = Toastify({
                text: "Submitting your listing...",
                duration: -1, // Keep open until manually closed
                close: false,
                gravity: "top",
                position: "right",
                style: {
                    background: "var(--deep-blue)",
                }
            }).showToast();

            const formData = new FormData(addListingForm);
            
            // Append images from our custom array
            // Remove any empty file inputs that might be there by default
            formData.delete('images[]');
            allFiles.forEach(file => {
                formData.append('images[]', file);
            });

            try {
                const response = await fetch(addListingForm.action, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                // Hide loading toast
                loadingToast.hideToast();
                
                if (data.success) {
                    Toastify({
                        text: data.message, // "Listing submitted for review..."
                        duration: 5000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#10B981", // Green for success
                        }
                    }).showToast();

                    addListingForm.reset();
                    const petsDetails = document.getElementById('petsDetails');
                    if (petsDetails) petsDetails.style.display = 'none';
                    
                    // Clear images except label
                    const images = imageGrid.querySelectorAll('.relative.group');
                    images.forEach(img => img.remove());
                    allFiles = [];

                    setTimeout(() => {
                        window.location.href = '/Advanced-Roommate-Apartment-Finder-Web-App-with-Email-Admin-Panel-/app/views/landlord/listings.php';
                    }, 2000);
                    
                } else {
                    // Re-enable submit button on error
                    isSubmitting = false;
                    submitButton.disabled = false;
                    submitButton.style.opacity = '1';
                    submitButton.style.cursor = 'pointer';

                    Toastify({
                        text: data.message || "Failed to submit listing",
                        duration: 4000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#EF4444", // Red for error
                        }
                    }).showToast();
                }

            } catch (error) {
                console.error(error);
                loadingToast.hideToast(); // Ensure loading toast is hidden on error
                
                // Re-enable submit button on error
                isSubmitting = false;
                submitButton.disabled = false;
                submitButton.style.opacity = '1';
                submitButton.style.cursor = 'pointer';

                Toastify({
                    text: "Something went wrong while saving the listing.",
                    duration: 4000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "#EF4444",
                    }
                }).showToast();
            }
        });

        function togglePetsDetails() {
            const checkbox = document.getElementById('petsAllowedCheckbox');
            const details = document.getElementById('petsDetails');
            if (checkbox.checked) {
                details.style.display = 'block';
                details.classList.add('animate-slide-up');
            } else {
                details.style.display = 'none';
            }
        }
    </script>
</body>
</html>
