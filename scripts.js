$(document).ready(function () {
    $("#loginForm").submit(function (e) {
        e.preventDefault();
        var username = $("#username").val();
        var password = $("#password").val();

        $.ajax({
            type: "POST",
            url: "login.php",
            data: { username: username, password: password },
            success: function (response) {
                var data = JSON.parse(response);
                if (data.error) {
                    // Error message using SweetAlert
                    swal({
                        title: "Login Error",
                        text: data.message,
                        icon: "error",
                        button: "OK",
                    });
                } else {
                    // Success message using SweetAlert
                    swal({
                        title: "Login Successful",
                        text: "Redirecting...",
                        icon: "success",
                        button: false,
                    });
                    // Redirect based on user role
                    setTimeout(function () {
                        if (data.role == "Manager") {
                            window.location.href = "manager_dashboard.html";
                        } else if (data.role == "Worker") {
                            window.location.href = "employee_dashboard.html";
                        } else {
                            // In case of 'Owner' role, handle as per your application logic
                            // Redirect to appropriate dashboard or page
                            window.location.href = "manager_dashboard.html"; // Change as needed
                        }
                    }, 2000); // 2 second delay to show the success message
                }
            },
            error: function (xhr, status, error) {
                // Error message using SweetAlert
                swal({
                    title: "Login Error",
                    text: "An error occurred: " + xhr.responseText,
                    icon: "error",
                    button: "OK",
                });
            },
        });
    });
});
