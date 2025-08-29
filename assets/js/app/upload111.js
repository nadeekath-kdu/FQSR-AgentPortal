
function FileUpload() {

    // File Upload Variables (moved outside to be globally accessible)
    let selectedFilesArray = [];
    let fileUploadArea, fileInput, selectedFiles, filesList, clearAllBtn;

    // Initialize file upload elements
    fileUploadArea = document.getElementById('fileUploadArea');
    fileInput = document.getElementById('document');
    selectedFiles = document.getElementById('selectedFiles');
    filesList = document.getElementById('filesList');
    clearAllBtn = document.getElementById('clearAllBtn');

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
}
