(function() {
    'use strict';

    function countLetters(str) {
        if (!str) return 0;
        try {
            var letters = str.match(/\p{L}/gu);
            return letters ? letters.length : 0;
        } catch (err) {
            var lettersAscii = str.match(/[A-Za-z]/g);
            return lettersAscii ? lettersAscii.length : 0;
        }
    }

    function showError(el, msg) {
        if (!el) return;
        el.textContent = msg;
        el.style.display = 'block';
    }

    function hideError(el) {
        if (!el) return;
        el.textContent = '';
        el.style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.querySelector('form.form-signin');
        if (!form) return;

        var pwInput = document.getElementById('password');
        var confirmInput = document.getElementById('confirmPassword');
        var pwError = document.getElementById('passwordError');

        // Prevent submitting if client-side validation fails
        form.addEventListener('submit', function (e) {
            if (!pwInput || !confirmInput) return;

            var pw = pwInput.value || '';
            var conf = confirmInput.value || '';

            if (pw !== conf) {
                showError(pwError, 'Heslo sa nezhoduje. Skúste to znova.');
                e.preventDefault();
                return;
            }

            var lettersCount = countLetters(pw);
            var hasDigit = /\d/.test(pw);

            if (lettersCount < 5 || !hasDigit) {
                showError(pwError, 'Heslo musí obsahovať minimálne 5 písmen a aspoň jedno číslo.');
                e.preventDefault();
                return;
            }

            // all good: clear any previous error
            hideError(pwError);
        });

        // Optional: live feedback while typing
        if (pwInput && pwError) {
            pwInput.addEventListener('input', function () {
                var pw = pwInput.value || '';
                var lettersCount = countLetters(pw);
                var hasDigit = /\d/.test(pw);

                if (lettersCount >= 5 && hasDigit) {
                    hideError(pwError);
                }
            });
        }
    });
})();

