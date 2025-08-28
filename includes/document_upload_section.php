<!-- Document Upload Section -->
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Document Management</h5>
            </div>
            <div class="card-body">
                <!-- Upload Area -->
                <div id="fileUploadArea" class="mb-4">
                    <div class="drop-zone-text">
                        <i class="fa fa-cloud-upload"></i>
                        <p>Drag & Drop files here or click to browse</p>
                        <small>Supported formats: PDF, DOCX, JPG, GIF, PNG (Max 5MB each)</small>
                    </div>
                    <input type="file" id="document" name="documents[]" multiple style="display: none;">
                </div>

                <!-- File List -->
                <div id="selectedFiles" style="display: none;">
                    <div id="filesList"></div>
                    <button type="button" id="clearAllBtn" class="btn btn-warning btn-sm mt-2">Clear All</button>
                </div>

                <!-- Currently Uploaded Files -->
                <?php if (!empty($uploadedDocuments)): ?>
                    <div class="current-files mt-4">
                        <h6 class="mb-3">Current Documents</h6>
                        <?php foreach ($uploadedDocuments as $doc): ?>
                            <div class="file-item">
                                <div class="file-info">
                                    <div class="file-icon <?php echo strtolower(pathinfo($doc['name'], PATHINFO_EXTENSION)); ?>">
                                        <?php echo getFileIcon(pathinfo($doc['name'], PATHINFO_EXTENSION)); ?>
                                    </div>
                                    <div class="file-details">
                                        <div class="file-name"><?php echo htmlspecialchars($doc['name']); ?></div>
                                        <div class="file-size"><?php echo formatFileSize($doc['size']); ?></div>
                                    </div>
                                </div>
                                <div class="file-actions">
                                    <a href="<?php echo htmlspecialchars($doc['path']); ?>" 
                                       class="btn btn-sm btn-primary me-2" 
                                       target="_blank">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                    <form method="post" style="display: inline-block;">
                                        <input type="hidden" name="document_name" 
                                               value="<?php echo htmlspecialchars($doc['name']); ?>">
                                        <button type="submit" name="delete_document" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this document?')">
                                            <i class="fa fa-trash"></i> Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No documents uploaded yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.file-upload-section {
    margin-top: 30px;
}
#fileUploadArea {
    border: 2px dashed #ccc;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
}
#fileUploadArea:hover, #fileUploadArea.drag-over {
    background: #fff;
    border-color: #0d6efd;
}
#fileUploadArea .drop-zone-text {
    color: #666;
}
#fileUploadArea .fa-cloud-upload {
    font-size: 48px;
    color: #0d6efd;
    margin-bottom: 15px;
}
.file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    margin: 5px 0;
    background: #f8f9fa;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
}
.file-info {
    display: flex;
    align-items: center;
    gap: 10px;
}
.file-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9ecef;
    border-radius: 4px;
}
.file-details {
    display: flex;
    flex-direction: column;
}
.file-name {
    font-weight: 500;
    color: #333;
}
.file-size {
    font-size: 12px;
    color: #666;
}
.file-actions {
    display: flex;
    gap: 10px;
}
.me-2 {
    margin-right: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('document');
    const selectedFiles = document.getElementById('selectedFiles');
    const filesList = document.getElementById('filesList');
    const clearAllBtn = document.getElementById('clearAllBtn');

    fileUploadArea.addEventListener('click', () => fileInput.click());
    
    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadArea.classList.add('drag-over');
    });

    fileUploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        fileUploadArea.classList.remove('drag-over');
    });

    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUploadArea.classList.remove('drag-over');
        handleFiles(Array.from(e.dataTransfer.files));
    });

    fileInput.addEventListener('change', (e) => {
        handleFiles(Array.from(e.target.files));
    });

    function handleFiles(files) {
        files.forEach(file => {
            // Add file to display
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <div class="file-info">
                    <div class="file-icon">
                        <i class="fa fa-file-o"></i>
                    </div>
                    <div class="file-details">
                        <div class="file-name">${file.name}</div>
                        <div class="file-size">${formatFileSize(file.size)}</div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger">Remove</button>
            `;

            filesList.appendChild(fileItem);
            selectedFiles.style.display = 'block';
        });
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
