// Global variables
window.closingDateLoaded = window.closingDateLoaded || false;
window.availableDegrees = window.availableDegrees || [];


function initializeDocumentHandling(passportNo) {
    if (!passportNo) {
        console.error('Passport number is required for document handling');
        return;
    }
    window.DocumentHandler.initialize(passportNo);
}

// Document removal handler
function removeDocument(filePath, item) {
    if (!filePath) return;

    const passportNo = $('#dec_nic_no').val() || $('#passportNo').val();
    if (!passportNo) {
        toastr.error('Could not determine passport/NIC number');
        return;
    }

    $.ajax({
        url: '../data/remove_document.php',
        type: 'POST',
        data: { passportNo, filePath },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                item.fadeOut(300, function () { $(this).remove(); });
                toastr.success('Document removed successfully');
            } else {
                toastr.error(response.message || 'Failed to remove document');
            }
        },
        error: function (xhr) {
            console.error('Remove document error:', xhr.responseText);
            toastr.error('Error occurred while removing document');
        }
    });
}


window.DocumentHandler = window.DocumentHandler || (function (window) {
    'use strict';

    // Private state
    const state = {
        fileUploadArea: null,
        fileInput: null,
        filesList: null,
        selectedFiles: new Set(), // new files (File objects)
        existingFiles: new Set(), // filenames of already-uploaded files
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
        // Create a local object URL for the file so it can be viewed
        const fileUrl = URL.createObjectURL(file);
        li.innerHTML = `
            <span class="file-name">${file.name}</span>
            <div class="btn-group">
                <a href="${fileUrl}" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-eye"></i> View</a>
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
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center existing-file';
        li.innerHTML = `
            <span class="file-name">${filename}</span>
            <div class="btn-group">
                <a href="../uploads/documents/${state.passportNo}/${filename}" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-eye"></i> View</a>
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
        state.fileInput.addEventListener('change', function (e) {
            console.log('File input change event triggered', e);
            if (this.files && this.files.length > 0) {
                handleFiles(this.files);
            }
        });
        // Prevent default drag behaviors on document
        document.addEventListener('dragover', function (e) {
            e.preventDefault();
        });
        document.addEventListener('drop', function (e) {
            e.preventDefault();
        });
        // Remove file handler is now handled globally outside the DocumentHandler
    }

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
        // Make the upload area clickable to trigger file input
        state.fileUploadArea.style.cursor = 'pointer';
        state.fileUploadArea.addEventListener('click', function (e) {
            state.fileInput.click();
        });
        // Load existing files from DOM (if any)
        $(state.filesList).find('li.existing-file .file-name').each(function () {
            const filename = $(this).text().trim();
            if (filename) state.existingFiles.add(filename);
        });
        setupEventListeners();
        state.initialized = true;
        console.log('Document handler initialized successfully');
    }

    // Return public interface
    return {
        initialize,
        getState: () => ({ ...state }),
        getSelectedFiles: () => Array.from(state.selectedFiles),
        getExistingFiles: () => Array.from(state.existingFiles)
    };
})(window);

// Initialize document handling when document is ready
$(function () {
    const passportValue = window.passportNumber || $('#passportNo').val() || $('#dec_nic_no').val();
    if (passportValue) {
        window.DocumentHandler.initialize(passportValue);
        // Always fetch the latest file list from the server
        $.getJSON('../data/list_documents.php', { nic: passportValue, t: Date.now() }, function (files) {
            if (Array.isArray(files)) {
                files.forEach(function (filename) {
                    if (typeof addExistingFileToList === 'function') {
                        addExistingFileToList(filename);
                    } else if (window.DocumentHandler && window.DocumentHandler.addExistingFileToList) {
                        window.DocumentHandler.addExistingFileToList(filename);
                    }
                });
            }
        });
    }
});

// Single handler for all document removals
$(document).on('click', '.remove-file', function (e) {
    e.preventDefault();
    const $item = $(this).closest('li');
    if (!$item.length) return;
    const fileName = $item.find('.file-name').text().trim();
    // Remove from DocumentHandler state if possible
    if (window.DocumentHandler && window.DocumentHandler.getState) {
        const state = window.DocumentHandler.getState();
        if ($item.hasClass('existing-file')) {
            // Call backend to remove the file from server
            const passportNo = window.passportNumber || $('#passportNo').val() || $('#dec_nic_no').val();
            if (passportNo && fileName) {
                $.ajax({
                    url: '../data/remove_document.php',
                    type: 'POST',
                    data: { passportNo: passportNo, filePath: fileName },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            state.existingFiles.delete(fileName);
                            $item.fadeOut(300, function () { $(this).remove(); });
                            toastr.success('Document removed successfully');
                        } else {
                            toastr.error(response.message || 'Failed to remove document');
                        }
                    },
                    error: function (xhr) {
                        toastr.error('Error occurred while removing document');
                    }
                });
                return;
            }
        } else if ($item.hasClass('new-file')) {
            // Find the File object by name and remove from selectedFiles
            for (let file of state.selectedFiles) {
                if (file.name === fileName) {
                    state.selectedFiles.delete(file);
                    break;
                }
            }
            $item.fadeOut(300, function () { $(this).remove(); });
        }
    } else {
        $item.fadeOut(300, function () { $(this).remove(); });
    }
});

// Initialize all form handlers when document is ready
$(document).ready(function () {
    // Get passport number from all possible sources
    const passportValue = window.passportNumber || $('#passportNo').val() || $('#dec_nic_no').val();

    if (passportValue) {
        initializeDocumentHandling(passportValue);
        console.log('Document handling initialized for passport/NIC:', passportValue);
    } else {
        console.warn('No passport number found for document handling initialization');
    }

    // Initialize validation handlers
    $('#inputEmailAddress').on('blur', validateEmailField);
    $('#inputDob, #inputCourse').on('change', validateAge);
    $('input[name="citizenship_type"]').on('change', updateCitizenshipSections);
});

// Form validation and helper functions
function updateCitizenshipSections() {
    var selectedValue = $('input[name="citizenship_type"]:checked').val();
    $('#section1, #section2, #section3').hide();
    $('#inputCitizenship, #inputCitizenship1, #inputCitizenship2').removeClass('is-invalid');

    if (selectedValue === 'Foreign Citizenship') {
        $('#section1').css('display', 'block');
        $('#inputCitizenship1, #inputCitizenship2').val('');
    } else if (selectedValue === 'Dual Citizenship') {
        $('#section2, #section3').css('display', 'block');
        $('#inputCitizenship').val('');
    } else if (selectedValue === 'Sri Lankan Citizenship Only') {
        $('#inputCitizenship, #inputCitizenship1, #inputCitizenship2').val('');
    }
}

function validateAge() {
    if (!closingDateLoaded) {
        toastr.error("Please wait, loading application closing date...", '', { timeOut: 2000 });
        return false;
    }
    const dob = new Date($('#inputDob').val());
    const closingDate = new Date($('#closingDate').val());
    let age = closingDate.getFullYear() - dob.getFullYear();
    const m = closingDate.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && closingDate.getDate() < dob.getDate())) {
        age--;
    }

    const degreeCode = $('#inputCourse').val();
    if (degreeCode === 'MED') {
        if (age < 16 || age > 29) {
            toastr.error("For medicine programs, age must be between 16 and 29 years", '', { timeOut: 2000 });
            return false;
        }
    } else {
        if (age < 16 || age > 25) {
            toastr.error("For non-medicine programs, age must be between 16 and 25 years", '', { timeOut: 2000 });
            return false;
        }
    }
    return true;
}

function validateEmailField() {
    var email = $('#inputEmailAddress').val().trim();
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email) {
        toastr.error("Please enter Email address", '', { timeOut: 1000 });
        return false;
    } else if (!emailPattern.test(email)) {
        toastr.error("Please enter a valid Email address", '', { timeOut: 1000 });
        return false;
    }
    return true;
}

function validateForm() {
    let isValid = true;
    let errorMessage = '';

    // Required fields validation
    const requiredFields = [
        { name: 'inputFullname', label: 'Full Name' },
        { name: 'inputNameInitials', label: 'Name with Initials' },
        { name: 'inputDob', label: 'Date of Birth' },
        { name: 'inputGender', label: 'Gender' },
        { name: 'inputCivilSts', label: 'Civil Status' },
        { name: 'citizenship_type', label: 'Citizenship' },
        { name: 'inputCountryBirth', label: 'Country of Birth' },
        { name: 'periodStudy', label: 'Period of Study apart from Sri Lanka' },
        { name: 'addressPermanent', label: 'Permanent Address' },
        { name: 'inputEmailAddress', label: 'Email Address' },
        { name: 'elegibleState', label: 'State University Eligibility' },
        { name: 'fatherName', label: "Father's Name" },
        { name: 'fatherMobileNo', label: "Father's Contact" },
        { name: 'motherName', label: "Mother's Name" },
        { name: 'inputCourse', label: "Course" },
        { name: 'refree1_details', label: "First Referee Name" },
        { name: 'refree1_phone', label: "First Referee Contact" },
        { name: 'refree2_details', label: "Second Referee Name" },
        { name: 'refree2_phone', label: "Second Referee Contact" }
    ];

    // Check required fields
    for (let field of requiredFields) {
        const value = $(`[name="${field.name}"]`).val();
        if (!value || value.trim() === '') {
            isValid = false;
            errorMessage += `${field.label} is required.\n`;
            $(`[name="${field.name}"]`).addClass('is-invalid');
        } else {
            $(`[name="${field.name}"]`).removeClass('is-invalid');
        }
    }

    // Check if files are uploaded
    const fileInput = document.getElementById('fileInput');

    // Validate age
    if (!validateAge()) {
        isValid = false;
        return false;
    }

    // Validate citizenship
    let citizenshipType = document.forms["my-form"]["citizenship_type"].value;
    if (citizenshipType === "") {
        toastr.error("Please select Citizenship type", '', { timeOut: 1000 });
        return false;
    }

    if (citizenshipType === "Foreign Citizenship" && !$('#inputCitizenship').val().trim()) {
        toastr.error("Please enter Citizenship", '', { timeOut: 1000 });
        return false;
    }

    if (citizenshipType === "Dual Citizenship") {
        if (!$('#inputCitizenship1').val().trim()) {
            toastr.error("Please enter first Citizenship", '', { timeOut: 1000 });
            return false;
        }
        if (!$('#inputCitizenship2').val().trim()) {
            toastr.error("Please enter second Citizenship", '', { timeOut: 1000 });
            return false;
        }
    }

    // Email validation
    if (!validateEmailField()) {
        return false;
    }

    // Eligibility check
    if ($('#elegibleState').val() === "Please Select") {
        toastr.error("Please select eligibility state", '', { timeOut: 1000 });
        $('#elegibleState').focus();
        return false;
    }

    // Educational results validation
    if (!validateEducationalResults()) {
        return false;
    }

    // Photo validation
    const photoInput = $('#Photo').length > 0 ? $('#Photo') : $('#inputPhoto');
    if (photoInput.length > 0 && photoInput[0].files.length > 0) {
        const file = photoInput[0].files[0];
        const fileSize = file.size / 1024 / 1024;
        const allowedTypes = ['image/jpeg', 'image/png'];

        if (fileSize > 2) {
            errorMessage += 'Photo size should not exceed 2MB.\n';
            isValid = false;
        }

        if (!allowedTypes.includes(file.type)) {
            errorMessage += 'Photo must be in JPG or PNG format.\n';
            isValid = false;
        }
    }

    if (!isValid) {
        toastr.error(errorMessage, "Validation Error");
    }

    return isValid;
}

// Document handling functions
function handleDocumentUpload(fileInput, documentType, passportNo) {
    var formData = new FormData();

    if (fileInput.files.length === 0) {
        toastr.error('Please select a file to upload');
        return;
    }

    formData.append('file', fileInput.files[0]);
    formData.append('documentType', documentType);
    formData.append('passportNo', passportNo);

    $.ajax({
        url: '../data/upload_document.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
                location.reload(); // Reload to show updated document list
            } else {
                toastr.error(response.message || 'Upload failed');
            }
        },
        error: function () {
            toastr.error('Upload failed. Please try again.');
        }
    });
}

// Unified document removal function
function handleDocumentRemoval(documentPath, passportNo, $item = null) {
    if (!confirm('Are you sure you want to remove this document?')) {
        return;
    }

    $.ajax({
        url: '../data/remove_document.php',
        type: 'POST',
        data: {
            passportNo: passportNo,
            filePath: documentPath,
        },
        success: function (response) {
            try {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                if (result.success) {
                    if ($item) {
                        $item.fadeOut(300, function () { $(this).remove(); });
                        toastr.success('Document removed successfully');
                    } else {
                        location.reload();
                    }
                } else {
                    toastr.error(result.message || 'Failed to remove file');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                toastr.error('Failed to process server response');
            }
        },
        error: function (xhr) {
            console.error('Remove document error:', xhr.responseText);
            toastr.error('Error occurred while removing file');
        }
    });
}

function clearDocumentsFolder(passportNo) {
    return new Promise((resolve, reject) => {
        console.log('[clearDocumentsFolder] Called with passportNo:', passportNo);
        $.ajax({
            url: '../data/clear_documents.php',
            type: 'POST',
            data: {
                passportNo: passportNo
            },
            success: function (data, textStatus, jqXHR) {
                console.log('[clearDocumentsFolder] Success:', { data, textStatus, jqXHR });
                resolve();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('[clearDocumentsFolder] Error:', { jqXHR, textStatus, errorThrown });
                toastr.error('Failed to clear documents. Please try again.');
                reject();
            }
        });
    });
}


// Document ready handler
$(document).ready(function () {
    const passportValue = $('#passportNo').val() || $('#dec_nic_no').val();
    if (passportValue) {
        window.DocumentHandler.initialize(passportValue);
    }

    //window.DocumentHandler.initialize(passportNo);

    // Initialize validation handlers
    $('#inputEmailAddress').on('blur', validateEmailField);
    $('#inputDob, #inputCourse').on('change', validateAge);

    // Initialize citizenship handlers
    $('input[name="citizenship_type"]').on('change', updateCitizenshipSections);

    // Initialize citizenship field validation
    $('#inputCitizenship').on('blur', function () {
        if ($('input[name="citizenship_type"]:checked').val() === 'Foreign Citizenship') {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                toastr.error('Please enter Citizenship', '', { timeOut: 1000 });
            } else {
                $(this).removeClass('is-invalid');
            }
        }
    });

    $('#inputCitizenship1, #inputCitizenship2').on('blur', function () {
        if ($('input[name="citizenship_type"]:checked').val() === 'Dual Citizenship') {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                toastr.error('Please enter ' + (this.id === 'inputCitizenship1' ? 'first' : 'second') + ' Citizenship', '', { timeOut: 1000 });
            } else {
                $(this).removeClass('is-invalid');
            }
        }
    });

    // Add trim on blur for referee fields
    $('#refree1_details, #refree2_details, #refree1_phone, #refree2_phone').on('blur', function () {
        $(this).val($(this).val().trim());
    });

    // Initialize sections
    updateCitizenshipSections();

    // Initialize degree selection
    initializeDegreeSelection();

    // Load closing date
    $.ajax({
        url: '../data/get_closing_date.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.closing_date) {
                $('#closingDate').val(response.closing_date);
                closingDateLoaded = true;
            } else {
                console.error('No closing date received:', response);
                toastr.error("Could not load closing date", '', { timeOut: 2000 });
            }
        },
        error: function (xhr, status, error) {
            console.error('Error details:', { xhr: xhr.responseText, status, error });
            toastr.error("Could not connect to server", '', { timeOut: 2000 });
        }
    });

    // Button handlers
    $('.btn-update').click(async function (e) {
        e.preventDefault();
        if (!validateForm()) return;

        // Always define state at the top
        const state = window.DocumentHandler.getState ? window.DocumentHandler.getState() : null;

        // Clear documents folder before update
        const passportNo = ($('#inputNic').val() || $('#dec_nic_no').val() || $('#passportNo').val() || '').trim();
        if (passportNo) {
            try {
                await clearDocumentsFolder(passportNo);
            } catch (err) {
                return;
            }
        }

        // Trim all text inputs
        $('#my-form input[type="text"], #my-form textarea').each(function () {
            $(this).val($(this).val().trim());
        });

        var formData = new FormData($('#my-form')[0]);

        // Add photo if present
        var photoInput = $('#Photo').length > 0 ? $('#Photo')[0] : $('#inputPhoto')[0];
        if (photoInput && photoInput.files.length > 0) {
            formData.append('Photo', photoInput.files[0]);
        }

        // Collect all files currently shown in the UI
        const filesToKeep = [];
        if (state && state.filesList) {
            $(state.filesList).find('li.existing-file .file-name').each(function () {
                const filename = $(this).text().trim();
                if (filename) filesToKeep.push(filename);
            });
        }
        // Add new files from upload area
        const newFiles = [];
        if (state && state.selectedFiles && state.selectedFiles.size > 0) {
            state.selectedFiles.forEach(file => {
                formData.append('documents[]', file);
                newFiles.push(file.name);
            });
        }
        // Add files_to_keep[] to formData
        filesToKeep.forEach(filename => formData.append('files_to_keep[]', filename));
        // Debug log
        console.log('Submitting form. files_to_keep:', filesToKeep, 'new uploads:', newFiles);

        // Add documents from legacy file input if present
        var fileInput = document.getElementById('fileInput');
        if (fileInput && fileInput.files.length > 0) {
            for (var i = 0; i < fileInput.files.length; i++) {
                formData.append('documents[]', fileInput.files[i]);
            }
        }

        if (!(($('#inputNic').val() || $('#dec_nic_no').val() || $('#passportNo').val() || '').trim())) {
            toastr.error("NIC/Passport number is required", "Error");
            return;
        }

        $.ajax({
            url: '../pages/formupdate.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                try {
                    console.log('Raw response:', response);
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }

                    if (response.status === 'success') {
                        toastr.success(response.message || "Saved successfully");

                        // Handle file operations feedback
                        if (response.uploaded_files?.length > 0) {
                            console.log('Uploaded files:', response.uploaded_files);
                        }
                        if (response.upload_errors?.length > 0) {
                            console.error('Upload errors:', response.upload_errors);
                            response.upload_errors.forEach(error => {
                                toastr.warning(error, "File Upload Warning");
                            });
                        }

                        // Redirect if passport number is present
                        if (response.passport_no) {
                            $("#content").load("../content/view_applicationform.php?nic=" + response.passport_no);
                        } else {
                            console.warn('Success response missing passport_no:', response);
                            toastr.warning("Success but no passport number returned");
                        }
                    } else {
                        toastr.error(response.message || "Error updating form", "Error");
                        if (response.upload_errors?.length > 0) {
                            response.upload_errors.forEach(error => {
                                toastr.error(error, "File Upload Error");
                            });
                        }
                        console.error('Error response:', response);
                    }
                } catch (e) {
                    console.error('Response processing error:', e, response);
                    toastr.error("Error processing response: " + e.message, "Error");
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', { status, error, response: xhr.responseText });
                if (xhr.status === 404) {
                    toastr.error("Server script not found", "Error 404");
                } else if (xhr.status === 500) {
                    toastr.error("Server error occurred", "Error 500");
                } else {
                    toastr.error("Connection error: " + error, "Error " + xhr.status);
                }
            }
        });
    });

    // Checkout button handler
    $(".btn-checkout").on("click", function (e) {
        e.preventDefault();
        var nic = $(this).data('nic');
        if (!nic) {
            toastr.error("No passport/NIC number found", "Error");
            return;
        }
        // Fetch total amount before redirecting
        $.getJSON('../data/get_total_amount.php', { nic: nic }, function (response) {
            if (response.success) {
                var totalAmount = response.total_amount;
                // Open payment page with amount as query param
                window.open('http://enlistment.kdu.ac.lk/pg_sampath/pgrequest_check_fsr.php?idn=' + nic + '&amount=' + totalAmount, '_blank');
                window.location.href = '../content/application_formpdf.php?nic=' + encodeURIComponent(nic);
            } else {
                toastr.error('Could not calculate total amount.');
                window.location.href = '../content/application_formpdf.php?nic=' + encodeURIComponent(nic);
            }
        }).fail(function () {
            toastr.error('Could not fetch total amount.');
        });
    });

    // Edit button handler
    $('.btn-edit').click(function (e) {
        e.preventDefault();
        var nic = $(this).data('nic') || $('#inputNic').val();
        if (!nic) {
            toastr.error("No passport/NIC number found", "Error");
            return;
        }

        $("#content").load("../content/edit_applicationform.php?nic=" + encodeURIComponent(nic),
            function (response, status, xhr) {
                if (status === "success") {
                    if (response.includes("No application ID provided") ||
                        response.includes("Application not found")) {
                        toastr.error(response, "Error");
                    } else {
                        toastr.info("Form is ready for editing", "");
                    }
                } else {
                    toastr.error("Error loading form", "");
                }
            });
    });

    // Cancel button handler
    $(".btn-cancel").on("click", function (e) {
        e.preventDefault();
        $("#content").load("../content/viewappdatalist.html");
    });
});



// Function to initialize degree selection functionality
function initializeDegreeSelection() {
    const degreeChoices = document.getElementById('degreeChoices');
    const addDegreeBtn = document.getElementById('addDegreeChoice');

    if (!degreeChoices || !addDegreeBtn) {
        console.warn('Degree selection elements not found');
        return;
    }

    // Maximum number of degree choices allowed
    const MAX_DEGREE_CHOICES = 3;

    // Load available degrees
    $.ajax({
        url: '../data/get_degree_list.php',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            window.availableDegrees = data;
            console.log('Available degrees loaded:', data);

            // First clear existing degree choices
            degreeChoices.innerHTML = '';

            // Create initial degree choices based on selected degrees
            if (window.selectedDegrees && window.selectedDegrees.length > 0) {
                console.log('Setting up selected degrees:', window.selectedDegrees);

                // Sort selected degrees by preference order
                window.selectedDegrees.sort((a, b) => a.preference_order - b.preference_order);

                window.selectedDegrees.forEach((selectedDegree, index) => {
                    // Find matching degree before creating the element
                    const degree = window.availableDegrees.find(d => {
                        const matchByCode = d.degree_code === selectedDegree.degree_code;
                        const matchByName = d.degree_name === selectedDegree.degree_name;
                        if (matchByCode || matchByName) {
                            console.log('Found match for:', selectedDegree, 'Match:', d);
                        }
                        return matchByCode || matchByName;
                    });

                    const choiceItem = document.createElement('div');
                    choiceItem.className = 'degree-choice-item mb-3';
                    // Create options with the correct degree pre-selected
                    const options = window.availableDegrees.map(degree =>
                        `<option value="${degree.degree_code}" ${degree.degree_code === selectedDegree.degree_code ? 'selected' : ''}>
                            ${degree.degree_name}
                        </option>`
                    ).join('');

                    choiceItem.innerHTML = `
                        <div class="d-flex align-items-center gap-2">
                            <span class="preference-number">${index + 1}</span>
                            <select name="courses[]" class="form-select form-select-lg degree-select" required>
                                ${options}
                            </select>
                            ${index > 0 ? `
                                <button type="button" class="btn btn-danger remove-degree">
                                    <i class="fa fa-times"></i>
                                </button>
                            ` : ''}
                        </div>
                    `;
                    degreeChoices.appendChild(choiceItem);
                    console.log('Added degree choice:', selectedDegree.degree_code);
                });
            } else {
                // If no degrees selected, create one empty choice
                const newChoice = document.createElement('div');
                newChoice.className = 'degree-choice-item mb-3';
                newChoice.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <span class="preference-number">1</span>
                        <select name="courses[]" class="form-select form-select-lg degree-select" required>
                            <option value="">Select a course</option>
                            ${getAvailableDegreeOptions()}
                        </select>
                    </div>
                `;
                degreeChoices.appendChild(newChoice);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching degrees:', error);
            toastr.error('Failed to load degree options');
        }
    });

    // Add new degree choice
    addDegreeBtn.addEventListener('click', function () {
        const choicesCount = degreeChoices.querySelectorAll('.degree-choice-item').length;

        /* if (choicesCount >= MAX_DEGREE_CHOICES) {
            toastr.warning(`Maximum ${MAX_DEGREE_CHOICES} degree choices allowed`);
            return;
        } */

        const newChoice = document.createElement('div');
        newChoice.className = 'degree-choice-item mb-3';
        newChoice.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <span class="preference-number">${choicesCount + 1}</span>
                <select name="courses[]" class="form-select form-select-lg degree-select" required>
                    <option value="">Select a course</option>
                    ${window.availableDegrees.map(degree =>
            `<option value="${degree.degree_code}">${degree.degree_name}</option>`
        ).join('')}
                </select>
                <button type="button" class="btn btn-danger remove-degree">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        `;
        degreeChoices.appendChild(newChoice);
        updateDegreePreferences();
    });

    // Handle degree removal
    degreeChoices.addEventListener('click', function (e) {
        if (e.target.closest('.remove-degree')) {
            const choiceItem = e.target.closest('.degree-choice-item');
            if (choiceItem && degreeChoices.children.length > 1) {
                choiceItem.remove();
                updateDegreePreferences();
            } else {
                toastr.warning('At least one degree choice must be selected');
            }
        }
    });
}

// Function to get HTML options for available degrees
function getAvailableDegreeOptions(selectedCode = '') {
    return window.availableDegrees.map(degree =>
        `<option value="${degree.degree_code}" ${degree.degree_code === selectedCode ? 'selected' : ''}>${degree.degree_name}</option>`
    ).join('');
}

// Function to update all degree select options
function updateDegreeOptions() {
    const selects = document.querySelectorAll('.degree-select');
    if (!window.availableDegrees) {
        console.warn('Available degrees not loaded yet');
        return;
    }

    const selectedValues = new Set();

    selects.forEach(select => {
        const currentValue = select.value;
        select.innerHTML = '<option value="">Select a course</option>' + getAvailableDegreeOptions();

        if (currentValue) {
            // Check if the degree is still available
            const degreeStillExists = window.availableDegrees.some(d => d.degree_code === currentValue);
            if (degreeStillExists && !selectedValues.has(currentValue)) {
                select.value = currentValue;
                selectedValues.add(currentValue);
            } else {
                select.value = '';
                if (currentValue) {
                    toastr.warning('A previously selected degree is no longer available');
                }
            }
        }
    });
}

// Function to update preference numbers and remove buttons
function updateDegreePreferences() {
    const degreeChoices = document.getElementById('degreeChoices');
    if (!degreeChoices) return;

    const items = degreeChoices.querySelectorAll('.degree-choice-item');
    items.forEach((item, index) => {
        const numberSpan = item.querySelector('.preference-number');
        if (numberSpan) {
            numberSpan.textContent = index + 1;
        }
        // Show/hide remove button for first item
        const removeBtn = item.querySelector('.remove-degree');
        if (removeBtn) {
            removeBtn.style.display = index === 0 ? 'none' : '';
        }
    });
}