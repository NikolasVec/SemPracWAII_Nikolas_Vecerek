(function() {
    'use strict';


    // Spočíta počet písmen v reťazci
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

    // Zobrazí chybovú správu v danom elemente
    function showError(el, msg) {
        if (!el) return;
        el.textContent = msg;
        el.style.display = 'block';
    }

    // Skryje chybovú správu z elementu
    function hideError(el) {
        if (!el) return;
        el.textContent = '';
        el.style.display = 'none';
    }

    // Po načítaní DOM: nájde formulár a polia
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.querySelector('form.form-signin');
        if (!form) return;

        var pwInput = document.getElementById('password');
        var confirmInput = document.getElementById('confirmPassword');
        var pwError = document.getElementById('passwordError');

        // Zabráni odoslaniu formulára pri neúspešnej klient-side validácii
        form.addEventListener('submit', function (e) {
            if (!pwInput || !confirmInput) return;

            var pw = pwInput.value || '';
            var conf = confirmInput.value || '';

            // Kontrola: zhodné heslá
            if (pw !== conf) {
                showError(pwError, 'Heslo sa nezhoduje. Skúste to znova.');
                e.preventDefault();
                return;
            }

            var lettersCount = countLetters(pw);
            var hasDigit = /\d/.test(pw);

            // Kontrola: aspoň 5 písmen a jedno číslo
            if (lettersCount < 5 || !hasDigit) {
                showError(pwError, 'Heslo musí obsahovať minimálne 5 písmen a aspoň jedno číslo.');
                e.preventDefault();
                return;
            }

            // Ak všetko OK, odstráň predchádzajúcu chybu
            hideError(pwError);
        });

        // Živá spätná väzba pri písaní hesla
        if (pwInput && pwError) {
            pwInput.addEventListener('input', function () {
                var pw = pwInput.value || '';
                var lettersCount = countLetters(pw);
                var hasDigit = /\d/.test(pw);

                // Ak heslo spĺňa pravidlá, skry chybu
                if (lettersCount >= 5 && hasDigit) {
                    hideError(pwError);
                }
            });
        }
    });
})();
