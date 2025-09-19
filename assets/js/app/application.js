// Add loading indicator styles
$('<style>')
    .text(`
        .loading {
            background-image: url('assets/img/loading.gif') !important;
            background-repeat: no-repeat !important;
            background-position: right center !important;
            background-size: 20px !important;
        }
    `)
    .appendTo('head');

var closingDateLoaded = false;

// File Upload Variables (moved outside to be globally accessible)
let selectedFilesArray = [];
let fileUploadArea, fileInput, selectedFiles, filesList, clearAllBtn;

// File handling functions
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

// Degree selection (application form - Step 2)
function initializeDegreeSelectionApp() {
    const degreeChoices = document.getElementById('degreeChoices');
    const addBtn = document.getElementById('addDegreeChoice');
    if (!degreeChoices || !addBtn) return;

    let availableDegreesApp = [];
    const originalAddHtml = addBtn.innerHTML;
    addBtn.disabled = true;
    addBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading degrees...';

    function buildOptionsUnique(excludeSet = new Set(), keepCode = '') {
        if (!Array.isArray(availableDegreesApp)) return '';
        const out = [];
        let keepInjected = false;
        if (keepCode && !availableDegreesApp.some(d => String(d.degree_code) === String(keepCode))) {
            out.push(`<option value="${keepCode}" selected>${keepCode}</option>`);
            keepInjected = true;
        }
        availableDegreesApp.forEach(d => {
            const code = String(d.degree_code);
            if (excludeSet.has(code) && code !== String(keepCode)) return;
            const sel = code === String(keepCode) && !keepInjected ? ' selected' : '';
            const label = (d.degree_name && String(d.degree_name).trim().length > 0) ? d.degree_name : code;
            out.push(`<option value="${code}"${sel}>${label}</option>`);
        });
        return out.join('');
    }

    function refreshAll(changed = null) {
        const selects = Array.from(degreeChoices.querySelectorAll('select.degree-select'));
        const current = new Map();
        selects.forEach(sel => current.set(sel, sel.value || ''));
        selects.forEach(sel => {
            const keep = current.get(sel);
            const exclude = new Set();
            current.forEach((val, other) => { if (other !== sel && val) exclude.add(String(val)); });
            sel.innerHTML = '<option value="">Select a course</option>' + buildOptionsUnique(exclude, keep);
            if (keep) sel.value = keep;
        });
    }

    function wireChanges() {
        degreeChoices.querySelectorAll('select.degree-select').forEach(sel => {
            if (!sel._wired) {
                sel.addEventListener('change', function () { refreshAll(this); });
                sel._wired = true;
            }
        });
    }

    // Load degrees
    $.getJSON('../data/get_degree_list.php', { t: Date.now() })
        .done(function (data) {
            if (Array.isArray(data)) availableDegreesApp = data; else availableDegreesApp = [];
            // Populate first select
            const first = degreeChoices.querySelector('select.degree-select');
            if (first) {
                first.innerHTML = '<option value="">Select a course</option>' + buildOptionsUnique(new Set());
            }
            wireChanges();
        })
        .fail(function () {
            console.warn('Failed to load degree list');
        })
        .always(function () {
            addBtn.disabled = false;
            addBtn.innerHTML = originalAddHtml;
        });

    // Add another degree choice
    addBtn.addEventListener('click', function () {
        if (!Array.isArray(availableDegreesApp) || availableDegreesApp.length === 0) {
            toastr.info('Loading degree options, please wait...');
            return;
        }
        const count = degreeChoices.querySelectorAll('.degree-choice-item').length;
        const selectedSet = new Set();
        degreeChoices.querySelectorAll('select.degree-select').forEach(sel => { if (sel.value) selectedSet.add(sel.value); });

        const wrap = document.createElement('div');
        wrap.className = 'degree-choice-item mb-3';
        wrap.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <span class="preference-number">${count + 1}</span>
                <select name="courses[]" class="form-select form-select-lg degree-select">
                    <option value="">Select a course</option>
                    ${buildOptionsUnique(selectedSet)}
                </select>
                <button type="button" class="btn btn-danger remove-degree" ${count === 0 ? 'style="display:none;"' : ''}>
                    <i class="fa fa-times"></i>
                </button>
            </div>`;
        degreeChoices.appendChild(wrap);

        // Update numbering and buttons visibility
        degreeChoices.querySelectorAll('.degree-choice-item').forEach((item, idx) => {
            const pref = item.querySelector('.preference-number');
            if (pref) pref.textContent = String(idx + 1);
            const rm = item.querySelector('.remove-degree');
            if (rm) rm.style.display = idx === 0 ? 'none' : '';
        });
        wireChanges();
        refreshAll();

        // Auto-focus and open the newly added dropdown to avoid “double click to open”
        const newSelect = wrap.querySelector('select.degree-select');
        if (newSelect) {
            setTimeout(() => {
                try {
                    newSelect.focus({ preventScroll: true });
                    // Try to open the native select dropdown
                    const ev = new MouseEvent('mousedown', { bubbles: true, cancelable: true, view: window });
                    const opened = newSelect.dispatchEvent(ev);
                    if (!opened) {
                        // Fallback to a programmatic click if needed
                        newSelect.click();
                    }
                } catch (e) {
                    // As a last resort, at least focus the element
                    newSelect.focus({ preventScroll: true });
                }
            }, 50);
        }
    });

    // Remove degree handler
    degreeChoices.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-degree');
        if (!btn) return;
        const item = btn.closest('.degree-choice-item');
        if (item && degreeChoices.children.length > 1) {
            item.remove();
        } else {
            toastr.warning('At least one degree choice must be selected');
        }
        degreeChoices.querySelectorAll('.degree-choice-item').forEach((it, idx) => {
            const pref = it.querySelector('.preference-number');
            if (pref) pref.textContent = String(idx + 1);
            const rm = it.querySelector('.remove-degree');
            if (rm) rm.style.display = idx === 0 ? 'none' : '';
        });
        refreshAll();
    });
}

$(document).ready(function () {
    //console.log('application.js loaded');
    var serverUrl;
    var adminUrl;

    // Initialize file upload elements only if we're on the file upload step
    function initializeFileUpload() {
        fileUploadArea = document.getElementById('fileUploadArea');
        fileInput = document.getElementById('document');
        selectedFiles = document.getElementById('selectedFiles');
        filesList = document.getElementById('filesList');
        clearAllBtn = document.getElementById('clearAllBtn');

        // Only add event listeners if elements exist
        if (fileUploadArea && fileInput) {
            setupFileUploadHandlers();
        }
    }

    // Setup file upload event handlers
    function setupFileUploadHandlers() {
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
    }

    // Initialize file upload when navigating to step-8 (document upload step)
    $(document).on('click', 'a[href="#step-8"]', function () {
        setTimeout(initializeFileUpload, 100); // Short delay to ensure DOM is ready
    });

    // Initialize degree selection (Step 2)
    initializeDegreeSelectionApp();

    // Get academic year
    $.ajax({
        url: '../data/get_academic_year.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.academic_year) {
                $('#academicYear').text(response.academic_year);
            } else {
                console.error('No academic year received');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching academic year:', error);
        }
    });

    // Get application closing date
    $.ajax({
        url: '../data/get_closing_date.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.closing_date) {
                $('#closingDate').val(response.closing_date);
                closingDateLoaded = true;
            } else {
                //console.error('No closing date received from server');
                toastr.error("Error: Could not get application closing date", '', { timeOut: 2000 });
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching closing date:', error);
            toastr.error("Error: Could not get application closing date", '', { timeOut: 2000 });
        }
    });

    // Handle URL parameters for agent code
    const urlParams = new URLSearchParams(window.location.search);
    const agentCode = urlParams.get('agent_code');
    if (agentCode) {
        $('#agent_code').val(agentCode);
    }



    var navListItems = $('div.setup-panel div a'),
        allWells = $('.setup-content'),
        allNextBtn = $('.nextBtn'),
        allPrevBtn = $('.prevBtn');

    allWells.hide();

    // Navigate to the first step
    $('div.setup-panel div a.btn-primary').trigger('click');

    // Disable click on step links
    $('div.setup-panel div a.step-link').click(function (e) {
        e.preventDefault();
    });

    navListItems.click(function (e) {
        e.preventDefault();
        var $item = $(this),
            target = $($item.attr('href')),
            currentStep = parseInt($item.data('step')),
            previousStep = currentStep - 1;
        isValid = true;
        //console.log('Current step:', currentStep); // Debugging line to verify current step
        //console.log('Previous step:', previousStep);

        // Validate previous step before proceeding
        for (var i = 1; i < currentStep; i++) {
            if (!validateStep(i)) {
                isValid = false;
                break;
            }
        }

        if (!isValid) {
            return;
        }

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-primary').addClass('btn-default');
            $item.addClass('btn-primary');
            allWells.hide();
            target.show();
            target.find('input:eq(0)').focus();
        }
    });

    allPrevBtn.click(function () {
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

        prevStepWizard.removeAttr('disabled').trigger('click');
    });

    allNextBtn.click(function () {
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='url']"),
            isValid = true;
        currentStep = $(this).closest('.setup-content').attr('id').split('-')[1];
        isValid = validateStep(parseInt(currentStep));

        $(".form-group").removeClass("has-error");
        for (var i = 0; i < curInputs.length; i++) {
            if (!curInputs[i].validity.valid) {
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid)
            nextStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-primary').trigger('click');



    // Removed legacy degree selection logic and duplicate loaders. Using initializeDegreeSelectionApp() instead.

    $("#foreign").click(function () {
        //alert(document.forms["my-form"]["citizenship_type"].value);
        document.getElementById("section1").style.display = 'flex';
        document.getElementById("section2").style.display = 'none';
        document.getElementById("section3").style.display = 'none';
        $("#inputCitizenship1").val("");
        $("#inputCitizenship2").val("");

    });
    $("#dual").click(function () {
        //alert("A");
        document.getElementById("section1").style.display = 'none';
        document.getElementById("section2").style.display = 'flex';
        document.getElementById("section3").style.display = 'flex';
        $("#inputCitizenship").val("");

    });
    $("#sriLanakan").click(function () {
        document.getElementById("section1").style.display = 'none';
        document.getElementById("section2").style.display = 'none';
        document.getElementById("section3").style.display = 'none';
        $("#inputCitizenship").val("");
        $("#inputCitizenship1").val("");
        $("#inputCitizenship2").val("");

    });

    function getUrls() {
        $.ajax({
            url: '../data/get_server_url.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                //console.log('Server URL:', response.server_url);
                //console.log('Admin URL:', response.url_admin);
                serverUrl = response.server_url;
                adminUrl = response.url_admin;
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
    $('#my-form2').submit(function (event) {
        //console.log('Form submit handler triggered');
        event.preventDefault();
        // ...existing code...
    });
    $('#my-form').submit(function (event) {
        event.preventDefault();
        //console.log('Form submitted');

        // Require at least one document on first save
        if (!selectedFilesArray || selectedFilesArray.length === 0) {
            toastr.error('Please upload required supporting documents before saving.', '', { timeOut: 2000 });
            // Optionally scroll to step-8
            const step8 = document.querySelector('#step-8');
            if (step8) {
                $('div.setup-panel div a[href="#step-8"]').removeAttr('disabled').trigger('click');
            }
            return;
        }

        // Confirmation prompt to verify documents are correct
        Swal.fire({
            title: 'Confirm before saving',
            html: '<div style="text-align:left">Please review your uploaded documents in Step 8 and ensure they are correct and complete.<br><br><strong>Are all your documents correct?</strong></div>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save application',
            cancelButtonText: 'Go back',
        }).then((result) => {
            if (!result.isConfirmed) {
                // User chose to review again
                return;
            }

            const form = $("#my-form")[0];
            const formData = new FormData(form);

            // Ensure agent_code is included in form submission
            const agentCode = $('#agent_code').val();
            if (agentCode) {
                formData.append('agent_code', agentCode);
            }

            // Collect all selected degrees and their preference order
            const degreeSelects = document.querySelectorAll('#degreeChoices .degree-select');
            const selectedDegrees = Array.from(degreeSelects).map((select, index) => ({
                degree_code: select.value,
                preference_order: index + 1
            })).filter(deg => deg.degree_code); // Filter out empty selections

            // Add degrees to form data
            formData.append('selected_degrees', JSON.stringify(selectedDegrees));

            // Add files from the custom file upload area
            if (selectedFilesArray && selectedFilesArray.length > 0) {
                const maxFileSize = 5 * 1024 * 1024; // 5MB
                for (let i = 0; i < selectedFilesArray.length; i++) {
                    const file = selectedFilesArray[i];
                    if (file.size > maxFileSize) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Too Large',
                            text: `File "${file.name}" is larger than 5MB. Please choose a smaller file.`
                        });
                        return;
                    }
                    formData.append('documents[]', file);
                }
            }

            // Show loading indicator
            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we save your application',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '../pages/formsave.php', // Use relative path
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    Swal.close();

                    // Check if response is a string (error) or JSON object
                    /*  if (typeof response === 'string') {
                         try {
                             response = JSON.parse(response);
                         } catch (e) {
                             // If it's not valid JSON and contains PHP errors
                             if (response.includes('<b>Notice</b>') || response.includes('<b>Warning</b>')) {
                                 console.error('PHP Error:', response);
                                 Swal.fire({
                                     icon: 'error',
                                     title: 'Server Error',
                                     text: 'There was an error processing your application.',
                                     footer: 'Please contact support with error details.'
                                 });
                                 return;
                             }
                         }
                     } */

                    // Ensure we have a proper object to work with
                    var res = response;
                    /* if (typeof response === "string") {
                        try {
                            res = JSON.parse(response);
                            console.log('Parsed response:', res);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            Swal.fire({
                                icon: 'error',
                                title: 'Response Error',
                                text: 'Invalid response from server',
                                footer: 'Please try again or contact support.'
                            });
                            return;
                        }
                    } */

                    // Handle success response
                    if (res.status === "success") {
                        toastr.success(res.message || "Saved successfully", "");
                        if (res.passport_no) {
                            console.log('Loading view page with passport:', res.passport_no);
                            var page = "../content/view_applicationform.php?nic=" + res.passport_no;
                            $("#content").load(page, function (response, status, xhr) {
                                if (status == "error") {
                                    toastr.error("Error loading view page: " + xhr.statusText, "", { timeOut: 1000 });
                                } else {
                                    console.log('View page loaded successfully');
                                }
                            });
                        } else {
                            toastr.error("Error loading application view", "", { timeOut: 1000 });
                        }
                    } else {
                        toastr.error(res.message || "Data not saved.", "", { timeOut: 1000 });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.close();
                    console.error('Error details:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });

                    // Show a more informative error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Error',
                        text: 'There was a problem saving your application. Please check your internet connection and try again.',
                        footer: 'If the problem persists, please contact support.',
                        confirmButtonText: 'OK'
                    });

                    // Also show toastr notification
                    toastr.error("Form submission failed. Please try again.", '', {
                        timeOut: 3000,
                        closeButton: true,
                        progressBar: true
                    });
                }
            });
        });
    });


    // File Upload Handler (initialize event listeners)
    if (fileUploadArea && fileInput) {

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

        // Make clearAttachedFiles available globally
        window.clearAttachedFiles = function () {
            selectedFilesArray = [];
            updateFileInput();
            updateDisplay();
        };

    } // End of if (fileUploadArea && fileInput) block

});

function previewImage(event) {
    var input = event.target;
    var reader = new FileReader();
    reader.onload = function () {
        var dataURL = reader.result;
        var output = document.getElementById('wizardPicturePreview');
        output.src = dataURL;
    };
    reader.readAsDataURL(input.files[0]);
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

    // Check if medicine program (MED)
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

// Call on blur
$('#inputEmailAddress').on('blur', function () {
    validateEmailField();
});

// Validate age on date of birth change
$('#inputDob').on('change', function () {
    validateAge();
});

function validateStep(step) {
    var isValid = true;
    //console.log(step);

    if (step === 1) {
        if (!$('#passportno').val().trim()) {
            toastr.error("Please enter Passport Number", '', { timeOut: 1000, });
            //showToastMessage("Please enter Passport Number");
            isValid = false;
            return;
        } else {
            var passportno = $("#passportno").val();
            //isValid = true; 
            $.ajax({
                url: '../data/check_passport.php', // The URL of the PHP file that checks the passport number
                type: 'POST',
                async: false,
                data: { passportno: passportno },
                success: function (response) {
                    //console.log(response[0]);
                    if (response[0] === 'exist') {
                        toastr.error("Passport number already exists.", '', { timeOut: 1000, });
                        isValid = false;
                        // Redirect to application status
                        window.location.href = '../content/applicationstatus.php?idn=' + encodeURIComponent(passportno);
                        return;
                    } else {
                        isValid = true;
                    }
                },
                error: function () {
                    toastr.error("Error while checking passport number. Please try again later.", '', { timeOut: 1000, });
                    isValid = false;
                    return;
                }
            });
            //console.log('isValid ff:',isValid);

        }
    } else if (step === 2) {
        const firstDegree = document.querySelector('#degreeChoices .degree-select');
        if (!firstDegree || !firstDegree.value) {
            toastr.error('Please select at least one degree choice', '', { timeOut: 1000 });
            isValid = false;
            return;
        }
    } else if (step === 3) {
        var photoInput = $('#inputPhoto')[0];
        if (!photoInput.files || !photoInput.files[0]) {
            toastr.error("Please select a photo", '', { timeOut: 1000 });
            isValid = false;
            return;
        }

        // Validate photo size and type
        const file = photoInput.files[0];
        const fileSize = file.size / 1024 / 1024; // in MB
        const allowedTypes = ['image/jpeg', 'image/png'];

        if (fileSize > 2) {
            toastr.error("Photo size should not exceed 2MB", '', { timeOut: 2000 });
            isValid = false;
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            toastr.error("Photo must be in JPG or PNG format", '', { timeOut: 2000 });
            isValid = false;
            return;
        }

        if (!$('#inputInitials').val().trim()) {
            toastr.error("Please enter Name with Initial", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (!$('#inputFullname').val().trim()) {
            toastr.error("Please enter Full Name", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (!$('#inputDob').val().trim()) {
            toastr.error("Please enter Date of Birth", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (!validateAge()) {
            isValid = false;
            return;
        }
        if ($('#inputGender').val() === "Select Gender") {
            toastr.error("Please select Gender", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if ($('#inputCivilSts').val() === "Select Civil Status") {
            toastr.error("Please select Civil Ststus", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (!$('#inputEmailAddress').val().trim()) {
            toastr.error("Please enter Email address", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (!validateEmailField()) {
            isValid = false;
            return;
        }
        if (!$('#inputCountryBirth').val().trim()) {
            toastr.error("Please enter Birth country", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (!$('#periodStudy').val().trim()) {
            toastr.error("Please enter period of study", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (!$('#addressPermanent').val().trim()) {
            toastr.error("Please enter Permanent Address", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (document.forms["my-form"]["citizenship_type"].value === "") {
            if (!$('#inputCitizenship').val().trim()) {
                toastr.error("Please enter Citizenship type", '', { timeOut: 1000 });
                isValid = false;
                return;
            }
        }
        if (document.forms["my-form"]["citizenship_type"].value === "Foreign Citizenship") {
            if (!$('#inputCitizenship').val().trim()) {
                toastr.error("Please enter Citizenship", '', { timeOut: 1000 });
                isValid = false;
                return;
            }
        }
        if (document.forms["my-form"]["citizenship_type"].value === "Dual Citizenship") {
            if (!$('#inputCitizenship1').val().trim()) {
                toastr.error("Please enter Citizenship 1", '', { timeOut: 1000 });
                isValid = false;
                return;
            }
            if (!$('#inputCitizenship2').val().trim()) {
                toastr.error("Please enter Citizenship 2", '', { timeOut: 1000 });
                isValid = false;
                return;
            }
        }
        if ($('#citizenship_type').val() === "Dual Citizenship") {
            if (!$('#inputCitizenship1').val().trim()) {
                toastr.error("Please enter Citizenship 1", '', { timeOut: 1000 });
                isValid = false;
                return;
            }
            if (!$('#inputCitizenship2').val().trim()) {
                toastr.error("Please enter Citizenship 2", '', { timeOut: 1000 });
                isValid = false;
                return;
            }
        }

    } else if (step === 4) {
        if ($('#elegibleState').val() === "Please Select") {
            toastr.error("Please select eligibility", '', { timeOut: 1000, });
            isValid = false;
            return;
        }

        // Validate educational results
        isValid = validateEducationalResults();
        if (!isValid) {
            return false;
        }
    } else if (step === 6) {
        if (!$('#fatherName').val().trim()) {
            toastr.error("Please enter Father's Name", '', { timeOut: 1000, });
            isValid = false;
            return;
        } if (!$('#fatherMobileNo').val().trim()) {
            toastr.error("Please enter Father's Mobile No", '', { timeOut: 1000, });
            isValid = false;
            return;
        } if (!$('#motherName').val().trim()) {
            toastr.error("Please enter Mother's Name", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
    } else if (step === 7) {
        if (!$('#refree1_details').val().trim()) {
            toastr.error("Please enter Refree1 details", '', { timeOut: 1000, });
            isValid = false;
            return;
        } if (!$('#refree1_phone').val().trim()) {
            toastr.error("Please enter Refree1 Phone No", '', { timeOut: 1000, });
            isValid = false;
            return;
        } if (!$('#refree2_details').val().trim()) {
            toastr.error("Please enter Refree2 details", '', { timeOut: 1000, });
            isValid = false;
            return;
        } if (!$('#refree2_phone').val().trim()) {
            toastr.error("Please enter Refree2 Phone No", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
    } else if (step === 8) {
        // Validate file uploads
        if (selectedFilesArray.length === 0) {
            toastr.error("Please upload required documents", '', { timeOut: 1000 });
            isValid = false;
            return;
        }
    }

    return isValid;
}
function showToastMessage(message) {
    var toast = $('<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">')
        .addClass('bg-primary') // Example: Add background color
        .appendTo(document.body); // Append to the body

    // Create toast header
    var toastHeader = $('<div class="toast-header">').appendTo(toast);
    $('<strong class="me-auto">').text('Notification').appendTo(toastHeader);
    $('<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close">').appendTo(toastHeader);

    // Create toast body with the provided message
    $('<div class="toast-body">').text(message).appendTo(toast);

    // Show the toast
    toast.toast('show');


    toast.on('hidden.bs.toast', function () {
        toast.remove();
    });
}


function validateForm111() {

    if (document.forms["my-form"]["inputCourse"].value == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Select Course!',
            onAfterClose: () => {
                document.forms["my-form"]["inputCourse"].focus();
            }
        })
        return false;
    }
    if (document.forms["my-form"]["inputDob"].value == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Enter Birth Day!',
            onAfterClose: () => {
                document.forms["my-form"]["inputDob"].focus();
            }
        })
        return false;
    } if (document.forms["my-form"]["citizenship_type"].value == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Select citizenship Type!',
            onAfterClose: () => {
                document.forms["my-form"]["citizenship_type"].focus();
            }
        })
        return false;
    }
    if (document.forms["my-form"]["inputGender"].value == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Select Gender!',
            onAfterClose: () => {
                document.forms["my-form"]["inputGender"].focus();
            }
        })
        return false;
    } if (document.forms["my-form"]["inputCivilSts"].value == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Select Status!',
            onAfterClose: () => {
                document.forms["my-form"]["inputCivilSts"].focus();
            }
        })
        return false;
    } if (document.forms["my-form"]["refree1_details"].value == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Enter Refree Details!',
            onAfterClose: () => {
                document.forms["my-form"]["refree1_details"].focus();
            }
        })
        return false;
    }
    if (document.forms["my-form"]["refree1_phone"].value == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Enter Refree Contact No!',
            onAfterClose: () => {
                document.forms["my-form"]["refree1_phone"].focus();
            }
        })
        return false;
    }
    if (document.forms["my-form"]["citizenship_type"].value == "Foreign Citizenship") {
        if (document.forms["my-form"]["inputCitizenship"].value == "") {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Data',
                text: 'Please Enter Citizenship!',
                onAfterClose: () => {
                    document.forms["my-form"]["inputCitizenship"].focus();
                }
            })
            return false;
        }
    } if (document.forms["my-form"]["citizenship_type"].value == "Dual Citizenship") {
        if (document.forms["my-form"]["inputCitizenship1"].value == "") {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Data',
                text: 'Please Enter Dual Citizenship!',
                onAfterClose: () => {
                    document.forms["my-form"]["inputCitizenship1"].focus();
                }
            })
            return false;
        } if (document.forms["my-form"]["inputCitizenship2"].value == "") {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Data',
                text: 'Please Enter Dual Citizenship!',
                onAfterClose: () => {
                    document.forms["my-form"]["inputCitizenship2"].focus();
                }
            })
            return false;
        }
    }



    var closingDate = document.forms["my-form"]["closingDate"].value;
    var dob1 = document.forms["my-form"]["inputDob"].value;
    var dob = moment(dob1, 'YYYY-MM-DD').format('MM/DD/YYYY');
    var endDate = moment(closingDate, 'YYYY-MM-DD').format('MM/DD/YYYY');

    var year25 = moment(endDate, 'MM/DD/YYYY').subtract(30, 'years').format('MM/DD/YYYY');

    var year17 = moment(endDate, 'MM/DD/YYYY').subtract(17, 'years').format('MM/DD/YYYY');

    var d_dob = dob.split("/");
    var d_25 = year25.split("/");
    var d_17 = year17.split("/");


    var bday = new Date(d_dob[2], parseInt(d_dob[0]) - 1, d_dob[1]);  // -1 because months are from 0 to 11
    var date25 = new Date(d_25[2], parseInt(d_25[0]) - 1, d_25[1]);
    var date17 = new Date(d_17[2], parseInt(d_17[0]) - 1, d_17[1]);
    if (date17 < bday) {
        Swal.fire({
            icon: 'warning',
            title: 'Age Limit',
            text: 'You are younger than 17!',
            onAfterClose: () => {
                document.forms["my-form"]["inputDob"].focus();
            }
        })
        return false;
    } if (bday < date25) {
        Swal.fire({
            icon: 'warning',
            title: 'Age Limit',
            text: 'You are older than 30!',
            onAfterClose: () => {
                document.forms["my-form"]["inputDob"].focus();
            }
        })
        return false;
    }
    $("#minDate").val(date25);
    $("#maxDate").val(date17);
    $("#dob").val(bday);
    console.log('17: ' + date17);
    console.log('25:' + date25);
    console.log('17 > age: ' + date17 > bday);
    console.log('age > 25: ' + bday > date25);

}

