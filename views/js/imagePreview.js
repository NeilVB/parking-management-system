document.addEventListener("DOMContentLoaded", () => {
    /* ===========================
    imagePreview.js
    ============================ */
    function bindImagePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (input && preview) {
            input.addEventListener("change", () => {
                const file = input.files[0];
                if (file) {
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) {
                        alert("Please select a valid image file (JPEG, PNG, GIF, or WebP).");
                        input.value = "";
                        return;
                    }
                    
                    // Validate file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        alert("File size must be less than 5MB.");
                        input.value = "";
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.src = e.target.result;
                        preview.style.display = "block";
                    };
                    reader.onerror = () => {
                        alert("Error reading file.");
                        input.value = "";
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = "";
                    preview.style.display = "none";
                }
            });
        }
    }

    // Bind image preview handlers
    bindImagePreview("new-space-image", "add-space-preview");
    bindImagePreview("edit-space-image", "edit-space-preview");
});