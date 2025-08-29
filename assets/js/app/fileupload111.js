$(document).ready(function() {
    const fileUploadArea = $('#fileUploadArea');
    const fileInput = $('#document');
    const selectedFiles = $('#selectedFiles');
    const filesList = $('#filesList');
    const clearAllBtn = $('#clearAllBtn');
    let totalSize = 0;
    const maxTotalSize = 2 * 1024 * 1024; // 2MB in bytes

    // Click to upload
    fileUploadArea.on('click', function() {
        fileInput.click();
    });

    // Drag and drop
    fileUploadArea.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('drag-over');
    });

    fileUploadArea.on('dragleave drop', function(e) {
        e.preventDefault();
        $(this).removeClass('drag-over');
        
        if (e.type === 'drop') {
            const files = Array.from(e.originalEvent.dataTransfer.files);
            handleFiles(files);
        }
    });

    // File input change
    fileInput.on('change', function(e) {
        const files = Array.from(e.target.files);
        handleFiles(files);
    });

    // Clear all files
    clearAllBtn.on('click', function() {
        filesList.empty();
        selectedFiles.hide();
        fileInput.val('');
        totalSize = 0;
    });

    // Remove individual file
    $(document).on('click', '.btn-remove', function() {
        const fileItem = $(this).closest('.file-item');
        const fileSize = parseInt(fileItem.data('size'));
        totalSize -= fileSize;
        fileItem.remove();
        
        if (filesList.children().length === 0) {
            selectedFiles.hide();
        }
    });

    function handleFiles(files) {
        let validFiles = [];
        
        files.forEach(file => {
            // Check file type
            const ext = file.name.split('.').pop().toLowerCase();
            const validTypes = ['pdf', 'docx', 'jpg', 'jpeg', 'gif', 'png'];
            
            if (!validTypes.includes(ext)) {
                toastr.error(`Invalid file type: ${file.name}. Only PDF, DOCX, JPG, GIF, and PNG files are allowed.`);
                return;
            }

            // Check individual file size (1MB)
            if (file.size > 1024 * 1024) {
                toastr.error(`File ${file.name} is too large. Maximum size is 1MB.`);
                return;
            }

            // Check total size
            if (totalSize + file.size > maxTotalSize) {
                toastr.error('Total file size would exceed 2MB limit.');
                return;
            }

            validFiles.push(file);
            totalSize += file.size;
        });

        if (validFiles.length > 0) {
            addFilesToList(validFiles);
        }
    }

    function addFilesToList(files) {
        files.forEach(file => {
            const fileItem = $(`
                <div class="file-item" data-size="${file.size}">
                    <div class="file-info">
                        <div class="file-icon">
                            ${getFileIcon(file.name)}
                        </div>
                        <div class="file-details">
                            <div class="file-name">${file.name}</div>
                            <div class="file-size">${formatFileSize(file.size)}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-remove">Remove</button>
                </div>
            `);
            
            filesList.append(fileItem);
        });
        
        selectedFiles.show();
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const icons = {
            pdf: '<i class="far fa-file-pdf text-danger"></i>',
            docx: '<i class="far fa-file-word text-primary"></i>',
            jpg: '<i class="far fa-file-image text-success"></i>',
            jpeg: '<i class="far fa-file-image text-success"></i>',
            png: '<i class="far fa-file-image text-success"></i>',
            gif: '<i class="far fa-file-image text-success"></i>'
        };
        return icons[ext] || '<i class="far fa-file"></i>';
    }
});
