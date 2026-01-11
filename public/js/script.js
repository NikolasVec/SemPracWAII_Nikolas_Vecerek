document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-signin');
    if (form) {
        form.addEventListener('submit', function(event) {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirmPassword');

            if (passwordField && confirmPasswordField) {
                const password = passwordField.value.trim();
                const confirmPassword = confirmPasswordField.value.trim();

                if (!password || !confirmPassword || password !== confirmPassword) {
                    event.preventDefault();
                    alert('Heslá sa nezhodujú alebo sú prázdne!');
                    confirmPasswordField.focus();
                }
            }
        });
    }
});
