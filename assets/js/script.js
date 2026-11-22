function toggleForm(form) {
    document.querySelectorAll('.form').forEach(f => f.classList.remove('active'));
    document.getElementById(`${form}-form`).classList.add('active');

    document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[onclick="toggleForm('${form}')"]`).classList.add('active');
}

function togglePassword(inputId, iconId) {
    let passwordField = document.getElementById(inputId);
    let eyeIcon = document.getElementById(iconId);

    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        passwordField.type = "password";
        eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

$(document).ready(function () {
    $("#registerForm").submit(function (e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "register.php",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    alert(response.message); // Show success message
                    window.location.href = response.redirect; // Redirect to OTP verification page
                } else {
                    $("#errorMessage").text(response.message).css("color", "red");
                }
            }
        });
    });
});
