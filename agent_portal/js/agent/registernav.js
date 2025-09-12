$(document).ready(function () {
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
                    registrationForm.reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful!',
                        html: `
                                <p>✅ <strong>Thank you!</strong></p>
                                <p>Your registration was successful.</p>
                                <p>KDU will send your login credentials via email shortly.</p>
                            `,
                        confirmButtonText: 'Go to Home',
                        allowOutsideClick: false,
                        customClass: {
                            popup: 'shadow'
                        }
                    }).then(() => {
                        window.location.href = '/agent_portal/includes/login.html';
                    });

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
            const isValidPhone = (phone) => /^[0-9\s\-]+$/.test(phone.trim());
            const isValidFile = (file) => {
                const allowedExtensions = ["pdf", "docx", "jpg", "png"];
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
                isValid = false;
            }

            if (!addressLine1.value.trim()) {
                toastr.error("Address is required.");
                isValid = false;
            }

            if (!fullname.value.trim()) {
                toastr.error("Full Name is required.");
                isValid = false;
            }

            if (!nic.value.trim()) {
                toastr.error("NIC/Passport Number is required.");
                isValid = false;
            }

            if (email.value.trim() && !isValidEmail(email.value)) {
                toastr.error("Invalid Email Address.");
                isValid = false;
            }

            if (telephone1.value.trim() && !isValidPhone(telephone1.value)) {
                toastr.error("Invalid Phone Number.");
                isValid = false;
            }

            // Validate file uploads
            if (documentField.files.length > 0) {
                for (let file of documentField.files) {
                    if (!isValidFile(file)) {
                        toastr.error(
                            `Invalid file: ${file.name}. Allowed formats: PDF, DOCX, JPG, PNG. Max size: 5MB.`
                        );
                        isValid = false;
                        break;
                    }
                }
            }

            // Display general error message if the form is invalid
            if (!isValid) {
                toastr.warning("Please fill all required fields correctly.");
            }

            return isValid; // Allow or prevent form submission
        }
    });
});
