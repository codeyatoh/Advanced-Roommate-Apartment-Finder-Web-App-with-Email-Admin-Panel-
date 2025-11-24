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

            <form id="addListingForm" class="animate-slide-up" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Image Upload -->
                <div class="glass-card" style="padding: 1.5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Property Images</h2>
                    <div id="imageGrid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
                        <!-- Images will be added here dynamically -->
                        <label class="image-upload-label" style="width: 100%; height: 8rem; border: 2px dashed rgba(0,0,0,0.2); border-radius: 0.75rem; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                            <i data-lucide="upload" style="width: 2rem; height: 2rem; color: rgba(0,0,0,0.4); margin-bottom: 0.5rem;"></i>
                            <span style="font-size: 0.875rem; color: rgba(0,0,0,0.6);">Upload Image</span>
                            <input type="file" accept="image/*" multiple id="imageInput" style="display: none;">
                        </label>
                    </div>
                    <p style="font-size: 0.75rem; color: rgba(0,0,0,0.5);">Upload up to 10 images. First image will be the cover photo.</p>
                </div>

                <!-- Basic Information -->
                <div class="glass-card" style="padding: 1.5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Basic Information</h2>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <label class="form-label">Property Title</label>
                            <div style="position: relative;">
                                <i data-lucide="home" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.4);"></i>
                                <input type="text" class="form-input" placeholder="e.g., Modern Studio in Downtown" style="padding-left: 2.75rem;" required>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Description</label>
                            <textarea class="form-input" placeholder="Describe your property..." style="min-height: 120px; resize: vertical;" required></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                            <div>
                                <label class="form-label">Monthly Rent</label>
                                <div style="position: relative;">
                                    <i data-lucide="dollar-sign" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.4);"></i>
                                    <input type="number" class="form-input" placeholder="1200" style="padding-left: 2.75rem;" required>
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Available From</label>
                                <input type="date" class="form-input" required>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Location</label>
                            <div style="position: relative;">
                                <i data-lucide="map-pin" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: rgba(0,0,0,0.4);"></i>
                                <input type="text" class="form-input" placeholder="City, State" style="padding-left: 2.75rem;" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Property Details -->
                <div class="glass-card" style="padding: 1.5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Property Details</h2>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label">Bedrooms</label>
                            <select class="form-input">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4+</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Bathrooms</label>
                            <select class="form-input">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4+</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Current Roommates</label>
                            <select class="form-input">
                                <option>0</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3+</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Amenities -->
                <div class="glass-card" style="padding: 1.5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #000; margin-bottom: 1rem;">Amenities</h2>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <label class="glass-subtle" style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border-radius: 0.75rem; cursor: pointer; transition: all 0.2s;">
                            <input type="checkbox" style="width: 1rem; height: 1rem; border-radius: 0.25rem; accent-color: var(--deep-blue);">
                            <i data-lucide="wifi" style="width: 1.25rem; height: 1.25rem; color: var(--deep-blue);"></i>
                            <span style="font-size: 0.875rem; font-weight: 500; color: #000;">WiFi</span>
                        </label>
                        <label class="glass-subtle" style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border-radius: 0.75rem; cursor: pointer; transition: all 0.2s;">
                            <input type="checkbox" style="width: 1rem; height: 1rem; border-radius: 0.25rem; accent-color: var(--deep-blue);">
                            <i data-lucide="car" style="width: 1.25rem; height: 1.25rem; color: var(--deep-blue);"></i>
                            <span style="font-size: 0.875rem; font-weight: 500; color: #000;">Parking</span>
                        </label>
                        <label class="glass-subtle" style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border-radius: 0.75rem; cursor: pointer; transition: all 0.2s;">
                            <input type="checkbox" style="width: 1rem; height: 1rem; border-radius: 0.25rem; accent-color: var(--deep-blue);">
                            <i data-lucide="coffee" style="width: 1.25rem; height: 1.25rem; color: var(--deep-blue);"></i>
                            <span style="font-size: 0.875rem; font-weight: 500; color: #000;">Kitchen</span>
                        </label>
                        <label class="glass-subtle" style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border-radius: 0.75rem; cursor: pointer; transition: all 0.2s;">
                            <input type="checkbox" style="width: 1rem; height: 1rem; border-radius: 0.25rem; accent-color: var(--deep-blue);">
                            <i data-lucide="dumbbell" style="width: 1.25rem; height: 1.25rem; color: var(--deep-blue);"></i>
                            <span style="font-size: 0.875rem; font-weight: 500; color: #000;">Gym</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div style="display: flex; gap: 1rem;">
                    <button type="button" class="btn btn-ghost btn-lg" style="flex: 1;" onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-lg" style="flex: 1;">Publish Listing</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        // Image Upload Logic
        const imageInput = document.getElementById('imageInput');
        const imageGrid = document.getElementById('imageGrid');
        const uploadLabel = document.querySelector('.image-upload-label');

        imageInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const imageUrl = e.target.result;
                    createImageElement(imageUrl);
                };
                reader.readAsDataURL(file);
            });
        });

        function createImageElement(url) {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.style.position = 'relative';
            
            div.innerHTML = `
                <img src="${url}" style="width: 100%; height: 8rem; object-fit: cover; border-radius: 0.75rem;">
                <button type="button" class="remove-btn" style="position: absolute; top: 0.5rem; right: 0.5rem; padding: 0.25rem; background-color: #ef4444; color: white; border-radius: 9999px; border: none; cursor: pointer; opacity: 0; transition: opacity 0.2s;">
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
            div.querySelector('.remove-btn').addEventListener('click', () => {
                div.remove();
            });

            // Insert before the upload label
            imageGrid.insertBefore(div, uploadLabel);
            lucide.createIcons();
        }

        // Form Submit
        document.getElementById('addListingForm').addEventListener('submit', (e) => {
            e.preventDefault();
            console.log('Form submitted');
            // Add actual submission logic here
        });
    </script>
</body>
</html>
