$(document).on("submit", "#changePasswordForm", function (e) {
    e.preventDefault();

    const currentPassword = $("#currentPassword").val();
    const newPassword = $("#newPassword").val();
    const confirmPassword = $("#confirmPassword").val();
    //console.log("current pw: ", currentPassword);
    if (newPassword !== confirmPassword) {
        toastr.error("New passwords do not match.");
        return;
    }

    $.ajax({
        url: "../pages/change_password.php",
        type: "POST",
        data: {
            currentPassword: currentPassword,
            newPassword: newPassword
        },
        success: function (response) {
            console.log("response: ", response);
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
                toastr.success(response.message);
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 2000);
            } else {
                toastr.error(response.message || "Password change failed.");
            }
        },
        error: function () {
            toastr.error("An error occurred while changing password.");
        }
    });
});