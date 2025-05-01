// Custom JavaScript for handling dynamic user interactions
$(document).ready(function () {
    // Toggle password visibility
    $(".toggle-password").click(function () {
        var passwordInput = $(this).siblings(".password-input");
        var icon = $(this);

        if (passwordInput.attr("type") === "password") {
            passwordInput.attr("type", "text");
            icon.removeClass("fa-eye").addClass("fa-eye-slash");
        } else {
            passwordInput.attr("type", "password");
            icon.removeClass("fa-eye-slash").addClass("fa-eye");
        }
    });

    // Handle user registration
    $("#registerSubmit").click(function () {
        var fullName = $("#fullName").val();
        var email = $("#email").val();
        var password = $("#password").val();

        $("#registerSubmit").prop("disabled", true);
        $("#msg").html(
            '<div class="alert alert-info"><img src="/img/loading.gif" alt="Loading"/> Please wait...</div>'
        );

        $.ajax({
            url: "/core/configureSystem.php",
            type: "POST",
            data: {
                action: "register",
                fullName: fullName,
                email: email,
                password: password,
            },
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    window.location = "/";
                } else {
                    $("#msg").html(
                        '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' +
                            data.error +
                            "</div>"
                    );
                }
                $("#registerSubmit").prop("disabled", false);
            },
            error: function (xhr, status, error) {
                $("#msg").html(
                    '<div class="alert alert-danger">Server error: ' +
                        error +
                        "</div>"
                );
                $("#registerSubmit").prop("disabled", false);
            },
        });
    });

    // Handle user login
    $("#login_btn").click(function () {
        var email = $("#login_email").val();
        var password = $("#login_pass").val();

        $("#login_btn").prop("disabled", true).append(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        );
        $("#login_email, #login_pass").prop("disabled", true);

        $.ajax({
            url: "/core/auth.php",
            type: "POST",
            data: {
                action: "login",
                email: email,
                password: password,
            },
            dataType: "json",
            success: function (data) {
                if (data.auth.success) {
                    window.location = data.auth.redirect;
                } else {
                    $("#msg").html(
                        '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' +
                            data.auth.msg +
                            "</div>"
                    );
                }
                $("#login_btn").prop("disabled", false).find("span").remove();
                $("#login_email, #login_pass").prop("disabled", false);
            },
            error: function (xhr, status, error) {
                $("#msg").html(
                    '<div class="alert alert-danger">Server error: ' +
                        error +
                        "</div>"
                );
                $("#login_btn").prop("disabled", false).find("span").remove();
                $("#login_email, #login_pass").prop("disabled", false);
            },
        });
    });

    // Handle SSO authentication if enabled
    $('#login_form #login_sso').click(function () {
        console.log('SSO AUTH');
        $('#login_form :input, #login_form button').prop('disabled', true);
        $('#login_sso').append('<span class="spinner-border spinner-border-sm mx-1" role="status" aria-hidden="true"></span>');
        $.ajax({
            url: '/core/auth.php',
            type: 'POST',
            data: {
                action: "auth_sso",
                provider: $(this).data('provider'),
            },
            dataType: 'json',
            success: function (data) {
                if (data.auth.success) {
                    window.location = data.auth.redirect;
                } else if (data.auth.error) {
                    $('#msg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>' + data.auth.msg + '</div>');
                    $("#login_form .spinner-border").remove();
                    $('#login_form :input, #login_form button').prop('disabled', false);
                }
            },
            error: function (request, status, error) {
                $('#msg').html('<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation mx-2"></i>Unable to handle request, server returned an error: ' + request.status + '</div>');
                $("#login_form .spinner-border").remove();
                $('#login_form :input, #login_form button').prop('disabled', false);
            },
        });
    });

    // Handle reset password logic
    $("#reset_pass_btn").click(function () {
        var password = $("#password").val();
        var confirmPassword = $("#confirm_password").val();

        if (password !== confirmPassword) {
            $("#msg").html(
                '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>Passwords do not match.</div>'
            );
            return;
        }

        $("#reset_pass_btn").prop("disabled", true);
        $("#msg").html(
            '<div class="alert alert-info"><img src="/img/loading.gif" alt="Loading"/> Please wait...</div>'
        );

        $.ajax({
            url: "/core/configureSystem.php",
            type: "POST",
            data: {
                action: "resetPassword",
                token: $("#reset_pass_btn").data("token"),
                newPassword: password,
            },
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    window.location = "/";
                } else {
                    $("#msg").html(
                        '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' +
                            data.error +
                            "</div>"
                    );
                }
                $("#reset_pass_btn").prop("disabled", false);
            },
            error: function (xhr, status, error) {
                $("#msg").html(
                    '<div class="alert alert-danger">Server error: ' +
                        error +
                        "</div>"
                );
                $("#reset_pass_btn").prop("disabled", false);
            },
        });
    });

    // Forgot password logic
    $("#forgot_submit").click(function () {
        var email = $("#forgot_email").val();

        $("#forgot_submit").prop("disabled", true);
        $("#forgot_msg").html(
            '<div class="alert alert-info"><img src="/img/loading.gif" alt="Loading"/> Please wait...</div>'
        );

        $.ajax({
            url: "/core/configureSystem.php",
            type: "POST",
            data: {
                action: "resetPassword",
                email: email,
            },
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    $("#forgot_msg").html(
                        '<div class="alert alert-success"><i class="fa-solid fa-circle-check mx-2"></i>' +
                            data.success +
                            "</div>"
                    );
                    $("#forgot_email_form").hide();
                    $("#forgot_submit").hide();
                } else {
                    $("#forgot_msg").html(
                        '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation mx-2"></i>' +
                            data.error +
                            "</div>"
                    );
                }
                $("#forgot_submit").prop("disabled", false);
            },
            error: function (xhr, status, error) {
                $("#forgot_msg").html(
                    '<div class="alert alert-danger">Server error: ' +
                        error +
                        "</div>"
                );
                $("#forgot_submit").prop("disabled", false);
            },
        });
    });
});