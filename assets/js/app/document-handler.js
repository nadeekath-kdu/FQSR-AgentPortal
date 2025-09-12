// document-handler.js
window.DocumentHandler = (function (window) {
    'use strict';

    // Private state
    const state = {
        fileUploadArea: null,
        fileInput: null,
        filesList: null,
        selectedFiles: new Set(),
        existingFiles: new Set(),
        passportNo: null,
        initialized: false
    };

    // Private functions
    function handleFiles(files) {
        console.log('Handling files:', files);
        if (!state.filesList || !state.initialized) {
            console.warn('Document handler not properly initialized');
            return;
        }
        Array.from(files).forEach(file => {
            console.log('Processing file:', file.name);
            if (!state.selectedFiles.has(file)) {
                addFileToList(file);
            }
        });
    }

    function addFileToList(file) {
        console.log('Adding file to list:', file.name);
        if (!state.filesList || !state.initialized) {
            console.warn('Cannot add file, handler not initialized');
            return;
        }

        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center new-file';
        const fileUrl = URL.createObjectURL(file);
        
        // Escape filename to prevent HTML injection
        const safeFileName = file.name.replace(/[<>&"']/g, function(match) {
            const escapeMap = {
                '<': '&lt;',
                '>': '&gt;',
                '&': '&amp;',
                '"': '&quot;',
                "'": '&#x27;'
            };
            return escapeMap[match];
        });
        
        li.innerHTML = `
            <span class="file-name">${safeFileName}</span>
            <div class="btn-group">
                <a href="${fileUrl}" class="btn btn-sm btn-primary" target="_blank">
                    <i class="fa fa-eye"></i> View
                </a>
                <button type="button" class="btn btn-sm btn-danger remove-file">
                    <i class="fa fa-trash"></i> Remove
                </button>
            </div>
        `;

        const removeBtn = li.querySelector('.remove-file');
        removeBtn.addEventListener('click', function () {
            state.selectedFiles.delete(file);
            li.remove();
        });

        state.filesList.appendChild(li);
        state.selectedFiles.add(file);
    }

    function addExistingFileToList(filename) {
        if (!state.filesList || !state.initialized) return;

        // Check if file already exists in the list
        if (state.existingFiles.has(filename)) return;
        
        const existingItems = Array.from(state.filesList.querySelectorAll('li.existing-file .file-name'));
        const fileExists = existingItems.some(item => item.textContent.trim() === filename);
        if (fileExists) return;

        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center existing-file';
        
        // Escape filename to prevent HTML injection
        const safeFileName = filename.replace(/[<>&"']/g, function(match) {
            const escapeMap = {
                '<': '&lt;',
                '>': '&gt;',
                '&': '&amp;',
                '"': '&quot;',
                "'": '&#x27;'
            };
            return escapeMap[match];
        });
        
        // Escape filename for URL
        const safeUrlFileName = encodeURIComponent(filename);
        
        li.innerHTML = `
            <span class="file-name">${safeFileName}</span>
            <div class="btn-group">
                <a href="../uploads/documents/${state.passportNo}/${safeUrlFileName}" 
                   class="btn btn-sm btn-primary" target="_blank">
                    <i class="fa fa-eye"></i> View
                </a>
                <button type="button" class="btn btn-sm btn-danger remove-file">
                    <i class="fa fa-trash"></i> Remove
                </button>
            </div>
        `;

        const removeBtn = li.querySelector('.remove-file');
        removeBtn.addEventListener('click', function () {
            state.existingFiles.delete(filename);
            li.remove();
        });

        state.filesList.appendChild(li);
        state.existingFiles.add(filename);
    }

    function setupEventListeners() {
        // Drag and drop events
        state.fileUploadArea.addEventListener('dragenter', function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('dragover');
        });

        state.fileUploadArea.addEventListener('dragleave', function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');
        });

        state.fileUploadArea.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
        });

        state.fileUploadArea.addEventListener('drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        // File input change event
        state.fileInput.addEventListener('change', function () {
            console.log('File input change event triggered');
            if (this.files && this.files.length > 0) {
                handleFiles(this.files);
            }
        });

        // Prevent default drag behaviors
        document.addEventListener('dragover', function (e) {
            e.preventDefault();
        });

        document.addEventListener('drop', function (e) {
            e.preventDefault();
        });
    }

    // API Functions for document operations
    const api = {
        removeDocument: function (filePath, passportNo) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '../data/remove_document.php',
                    type: 'POST',
                    data: { passportNo, filePath },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            resolve(response);
                        } else {
                            reject(new Error(response.message || 'Failed to remove document'));
                        }
                    },
                    error: function (xhr) {
                        reject(new Error('Error occurred while removing document'));
                    }
                });
            });
        },

        clearDocuments: function (passportNo) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '../data/clear_documents.php',
                    type: 'POST',
                    data: { passportNo },
                    success: function (response) {
                        resolve(response);
                    },
                    error: function (xhr) {
                        reject(new Error('Failed to clear documents'));
                    }
                });
            });
        },

        loadDocuments: function (passportNo) {
            return new Promise((resolve, reject) => {
                $.getJSON('../data/list_documents.php', {
                    nic: passportNo,
                    t: Date.now()
                })
                    .then(files => {
                        if (Array.isArray(files)) {
                            files.forEach(filename => {
                                addExistingFileToList(filename);
                            });
                            resolve(files);
                        } else {
                            reject(new Error('Invalid response format'));
                        }
                    })
                    .fail(reject);
            });
        }
    };

    // Public methods
    function initialize(passportNo) {
        if (!passportNo) {
            console.warn('PassportNo is required for document handling');
            return;
        }

        state.passportNo = passportNo;
        state.fileUploadArea = document.getElementById('fileUploadArea');
        state.fileInput = document.getElementById('document') || document.getElementById('documentFile');
        state.filesList = document.getElementById('filesList');

        console.log('Initializing with elements:', {
            fileUploadArea: state.fileUploadArea,
            fileInput: state.fileInput,
            filesList: state.filesList
        });

        if (!state.fileUploadArea || !state.fileInput || !state.filesList) {
            console.warn('Document upload elements not found');
            return;
        }

        // Make upload area clickable
        state.fileUploadArea.style.cursor = 'pointer';
        state.fileUploadArea.addEventListener('click', function () {
            state.fileInput.click();
        });

        // Load existing files
        $(state.filesList).find('li.existing-file .file-name').each(function () {
            const filename = $(this).text().trim();
            if (filename) state.existingFiles.add(filename);
        });

        setupEventListeners();
        state.initialized = true;
        console.log('Document handler initialized successfully');

        // Load existing documents
        api.loadDocuments(passportNo).catch(console.error);
    }

    // Return public interface
    return {
        initialize,
        getState: () => ({ ...state }),
        getSelectedFiles: () => Array.from(state.selectedFiles),
        getExistingFiles: () => Array.from(state.existingFiles),
        addExistingFileToList,
        api
    };
})(window);
