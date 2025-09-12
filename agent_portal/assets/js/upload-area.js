document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM Content Loaded');
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    const clearAllBtn = document.getElementById('clearAll');

    // Debug checks
    console.log('Upload Area:', uploadArea);
    console.log('File Input:', fileInput);
    console.log('File List:', fileList);

    if (!uploadArea || !fileInput || !fileList) {
        console.error('Required elements not found!');
        return;
    }

    const maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
    let files = [];

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    // Highlight drop zone when dragging over it
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });

    // Handle dropped files
    uploadArea.addEventListener('drop', handleDrop, false);

    // Handle click to upload
    uploadArea.addEventListener('click', function (e) {
        console.log('Upload area clicked');
        e.preventDefault();
        e.stopPropagation();
        try {
            console.log('Triggering file input click');
            fileInput.click();
        } catch (err) {
            console.error('Error triggering file input:', err);
        }
    });

    // Handle file input change
    fileInput.addEventListener('change', function (e) {
        console.log('File input changed:', e.target.files);
        e.preventDefault();
        handleFiles(e);
    });

    // Handle clear all button
    clearAllBtn.addEventListener('click', function (e) {
        console.log('Clear all clicked');
        e.preventDefault();
        clearFiles();
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        uploadArea.classList.add('dragover');
    }

    function unhighlight(e) {
        uploadArea.classList.remove('dragover');
    }

    function handleDrop(e) {
        console.log('File dropped');
        const dt = e.dataTransfer;
        const newFiles = [...dt.files];
        handleFileAddition(newFiles);
    }

    function handleFiles(e) {
        console.log('Files selected:', e.target.files);
        if (e.target.files && e.target.files.length > 0) {
            const newFiles = Array.from(e.target.files);
            handleFileAddition(newFiles);
        }
    }

    function handleFileAddition(newFiles) {
        console.log('Processing files:', newFiles);

        newFiles.forEach(file => {
            // Check file size
            if (file.size > maxFileSize) {
                alert(`File ${file.name} is too large. Maximum file size is 2MB.`);
                return;
            }

            // Check if file already exists
            if (!files.some(f => f.name === file.name)) {
                files.push(file);
                console.log('Added file:', file.name);
            }
        });

        updateFileList();
    }

    function updateFileList() {
        console.log('Updating file list with files:', files);
        fileList.innerHTML = '';

        files.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';

            const fileInfo = document.createElement('div');
            fileInfo.className = 'file-info';

            // Simplified file type icon
            const icon = document.createElement('span');
            icon.className = 'file-icon';
            icon.innerHTML = '📄';

            const fileDetails = document.createElement('div');
            fileDetails.className = 'file-details';
            fileDetails.innerHTML = `
                <div class="file-name">${file.name}</div>
                <div class="file-size">${formatFileSize(file.size)}</div>
            `;

            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-file';
            removeBtn.innerHTML = '✕';
            removeBtn.onclick = (e) => {
                e.preventDefault();
                e.stopPropagation();
                removeFile(index);
            };

            fileInfo.appendChild(icon);
            fileInfo.appendChild(fileDetails);
            fileItem.appendChild(fileInfo);
            fileItem.appendChild(removeBtn);
            fileList.appendChild(fileItem);
        });

        clearAllBtn.style.display = files.length > 0 ? 'block' : 'none';
        console.log('File list updated');
    } function removeFile(index) {
        files.splice(index, 1);
        updateFileList();
    }

    function clearFiles() {
        files = [];
        updateFileList();
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
