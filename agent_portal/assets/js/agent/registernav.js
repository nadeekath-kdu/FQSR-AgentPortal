// File Upload Variables (moved outside to be globally accessible)
let selectedFilesArray = [];
let fileUploadArea, fileInput, selectedFiles, filesList, clearAllBtn;

$(document).ready(function () {
    // Initialize file upload elements
    fileUploadArea = document.getElementById('fileUploadArea');
    fileInput = document.getElementById('document');
    selectedFiles = document.getElementById('selectedFiles');
    filesList = document.getElementById('filesList');
    clearAllBtn = document.getElementById('clearAllBtn');

    $('#registrationForm').off('submit').on('submit', function (e) {
        e.preventDefault();
        if (!validateForm()) return;

        const formData = new FormData(registrationForm);
        $.ajax({
            url: "../pages/agencySave.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log("Raw Response:", response);
                if (typeof response !== "object") {
                    try {
                        response = JSON.parse(response); // Parse string response
                    } catch (e) {
                        toastr.error("Invalid response format.");
                        console.error("Parsing error:", e);
                        return;
                    }
                }
                console.log("Parsed Response:", response);

                if (response.success) {
                    toastr.success(response.message || "Form submitted successfully!");

                    // Reset the form
                    registrationForm.reset();

                    // Clear attached documents using global function
                    if (typeof window.clearAttachedFiles === 'function') {
                        window.clearAttachedFiles();
                    }

                    // Show success message
                    $('#successMessage')
                        .removeClass('d-none')
                        .html('✅ <strong>Thank you!</strong> Your registration was successful. KDU will send your login credentials via email once the review process is complete.');

                    $('#goHomeDiv').removeClass('d-none');

                } else {
                    toastr.error(response.message || "An error occurred while saving data.");
                }
            },
            error: function () {
                toastr.error("Failed to submit the form... Please try again later.");
            },
        });


        function validateForm() {
            let isValid = true; // Track overall form validity
            const form = document.forms["registrationForm"];

            // Get form fields
            const organisation = form["organisation"];
            const addressLine1 = form["addressLine1"];
            const fullname = form["fullname"];
            const nic = form["nic"];
            const email = form["email"];
            const telephone1 = form["telephone1"];
            const documentField = form["document"];

            // Helper functions
            const isValidEmail = (email) =>
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
            const isValidPhone = (phone) => {
                const cleanPhone = phone.trim().replace(/[\s\-\+\(\)]/g, '');
                return /^[0-9]{7,15}$/.test(cleanPhone);
            };
            const isValidFile = (file) => {
                const allowedExtensions = ["pdf", "docx", "jpg", "gif", "png"];
                const fileExtension = file.name.split(".").pop().toLowerCase();
                const maxFileSize = 5 * 1024 * 1024; // 5MB
                return (
                    allowedExtensions.includes(fileExtension) && file.size <= maxFileSize
                );
            };

            // Reset toastr
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: "3000",
            };

            // Check required fields
            if (!organisation.value.trim()) {
                toastr.error("Organization Name is required.");
                organisation.focus();
                isValid = false;
            }

            if (!addressLine1.value.trim()) {
                toastr.error("Address is required.");
                if (isValid) addressLine1.focus(); // Focus only if it's the first error
                isValid = false;
            }

            if (!fullname.value.trim()) {
                toastr.error("Full Name is required.");
                if (isValid) fullname.focus();
                isValid = false;
            }

            if (!nic.value.trim()) {
                toastr.error("NIC/Passport Number is required.");
                if (isValid) nic.focus();
                isValid = false;
            }

            if (!email.value.trim()) {
                toastr.error("Email Address is required.");
                if (isValid) email.focus();
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                toastr.error("Please enter a valid Email Address.");
                if (isValid) email.focus();
                isValid = false;
            }

            if (!form["mobile"].value.trim()) {
                toastr.error("Mobile Number is required.");
                if (isValid) form["mobile"].focus();
                isValid = false;
            } else if (!isValidPhone(form["mobile"].value)) {
                toastr.error("Please enter a valid Mobile Number.");
                if (isValid) form["mobile"].focus();
                isValid = false;
            }

            // Optional phone validation
            if (telephone1.value.trim() && !isValidPhone(telephone1.value)) {
                toastr.error("Please enter a valid Phone Number.");
                if (isValid) telephone1.focus();
                isValid = false;
            }

            // Validate file uploads
            if (documentField.files.length > 0) {
                for (let file of documentField.files) {
                    if (!isValidFile(file)) {
                        toastr.error(
                            `Invalid file: ${file.name}. Allowed formats: PDF, DOCX, JPG, GIF, PNG. Max size: 5MB.`
                        );
                        isValid = false;
                        break;
                    }
                }
            }

            // Display general error message if the form is invalid
            if (!isValid) {
                toastr.warning("Please correct the errors above before submitting.");
            }

            return isValid; // Allow or prevent form submission
        }
    });

    // File Upload Handler (initialize event listeners)
    if (fileUploadArea && fileInput) {
        // Click to upload
        fileUploadArea.addEventListener('click', function () {
            fileInput.click();
        });

        // Drag and drop functionality
        fileUploadArea.addEventListener('dragover', function (e) {
            e.preventDefault();
            fileUploadArea.classList.add('drag-over');
        });

        fileUploadArea.addEventListener('dragleave', function (e) {
            e.preventDefault();
            fileUploadArea.classList.remove('drag-over');
        });

        fileUploadArea.addEventListener('drop', function (e) {
            e.preventDefault();
            fileUploadArea.classList.remove('drag-over');

            const files = Array.from(e.dataTransfer.files);
            handleFiles(files);
        });

        // File input change
        fileInput.addEventListener('change', function (e) {
            const files = Array.from(e.target.files);
            handleFiles(files);
        });

        // Clear all files
        clearAllBtn.addEventListener('click', function () {
            if (selectedFilesArray.length > 0) {
                const fileCount = selectedFilesArray.length;
                selectedFilesArray = [];
                updateFileInput();
                updateDisplay();
                toastr.info(`${fileCount} file(s) removed successfully.`);
            } else {
                toastr.warning('No files to remove.');
            }
        });

        function handleFiles(files) {
            let validFileCount = 0;
            let totalSize = 0;

            // Calculate current total size
            selectedFilesArray.forEach(file => {
                totalSize += file.size;
            });

            files.forEach(file => {
                // Check if file already exists
                const exists = selectedFilesArray.some(f => f.name === file.name && f.size === file.size);
                if (exists) {
                    toastr.warning(`File "${file.name}" is already selected.`);
                    return;
                }

                // Validate file type
                const allowedExtensions = ["pdf", "docx", "jpg", "gif", "png"];
                const fileExtension = file.name.split(".").pop().toLowerCase();
                if (!allowedExtensions.includes(fileExtension)) {
                    toastr.error(`Invalid file type: ${file.name}. Allowed formats: PDF, DOCX, JPG, GIF, PNG.`);
                    return;
                }

                // Validate file size (individual file max 5MB)
                const maxFileSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxFileSize) {
                    toastr.error(`File "${file.name}" is too large. Maximum size is 5MB.`);
                    return;
                }

                // Check total size (max 10MB total)
                const maxTotalSize = 10 * 1024 * 1024; // 10MB total
                if (totalSize + file.size > maxTotalSize) {
                    toastr.error(`Total file size would exceed 10MB limit. Current: ${formatFileSize(totalSize)}, Adding: ${formatFileSize(file.size)}`);
                    return;
                }

                // File is valid, add it
                selectedFilesArray.push(file);
                totalSize += file.size;
                validFileCount++;
            });

            /* if (validFileCount > 0) {
                toastr.success(`${validFileCount} file(s) added successfully.`);
            } */

            updateFileInput();
            updateDisplay();
        }

        function updateFileInput() {
            const dt = new DataTransfer();
            selectedFilesArray.forEach(file => {
                dt.items.add(file);
            });
            fileInput.files = dt.files;
        }

        function updateDisplay() {
            if (selectedFilesArray.length === 0) {
                selectedFiles.style.display = 'none';
                return;
            }

            selectedFiles.style.display = 'block';
            filesList.innerHTML = '';

            selectedFilesArray.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';

                const fileExtension = file.name.split('.').pop().toLowerCase();

                fileItem.innerHTML = `
                <div class="file-info">
                    <div class="file-icon ${fileExtension}">
                        ${getFileIcon(fileExtension)}
                    </div>
                    <div class="file-details">
                        <div class="file-name">${file.name}</div>
                        <div class="file-size">${formatFileSize(file.size)}</div>
                    </div>
                </div>
                <button type="button" class="remove-file-btn" data-index="${index}">Remove</button>
            `;

                filesList.appendChild(fileItem);
            });

            // Add remove functionality
            document.querySelectorAll('.remove-file-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const index = parseInt(this.dataset.index);
                    removeFile(index);
                });
            });
        }

        function removeFile(index) {
            const fileName = selectedFilesArray[index].name;
            selectedFilesArray.splice(index, 1);
            updateFileInput();
            updateDisplay();
            toastr.info(`File "${fileName}" removed successfully.`);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function getFileIcon(extension) {
            const icons = {
                pdf: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>',
                docx: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>',
                jpg: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z"/></svg>',
                gif: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z"/></svg>',
                png: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z"/></svg>',
                default: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>'
            };
            return icons[extension] || icons.default;
        }

        // Make functions globally available for form reset
        window.clearAttachedFiles = function () {
            selectedFilesArray = [];
            updateFileInput();
            updateDisplay();
        };

    } // End of if (fileUploadArea && fileInput) block
});
