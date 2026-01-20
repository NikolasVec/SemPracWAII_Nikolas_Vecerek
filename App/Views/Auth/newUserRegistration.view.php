<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('auth');
?>

<div class="container">
    <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            <div class="card card-signin my-5">
                <div class="card-body">
                    <h5 class="card-title text-center">Registrácia</h5>

                    <?php if (isset($message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars((string)($message ?? '')) ?>
                    </div>
                    <?php endif; ?>

                    <form class="form-signin" method="post" action="<?= $link->url('auth.registerUser') ?>">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)($csrfToken ?? '')) ?>">

                        <div class="form-label-group mb-3">
                            <label for="firstName" class="form-label">Meno</label>
                            <input name="firstName" type="text" id="firstName" class="form-control" placeholder="Meno" required>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="lastName" class="form-label">Priezvisko</label>
                            <input name="lastName" type="text" id="lastName" class="form-control" placeholder="Priezvisko" required>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input name="email" type="email" id="email" class="form-control" placeholder="Email" required>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="password" class="form-label">Heslo</label>
                            <input name="password" type="password" id="password" class="form-control" placeholder="Heslo" required>
                            <small class="form-text text-muted">Heslo musí obsahovať minimálne 5 písmen a aspoň jedno číslo.</small>
                            <div id="passwordError" class="text-danger small mt-1" style="display:none"></div>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="confirmPassword" class="form-label">Potvrďte heslo</label>
                            <input name="confirmPassword" type="password" id="confirmPassword" class="form-control" placeholder="Potvrďte heslo" required>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="birthDate" class="form-label">Dátum narodenia</label>
                            <input name="birthDate" type="date" id="birthDate" class="form-control" required>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="gender" class="form-label">Pohlavie</label>
                            <select name="gender" id="gender" class="form-control" required>
                                <option value="M">Muž</option>
                                <option value="Z">Žena</option>
                            </select>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary" type="submit">Registrovať</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/script.js"></script>
<script>
    (function(){
        var form = document.querySelector('form.form-signin');
        if (!form) return;
        var pwInput = document.getElementById('password');
        var confirmInput = document.getElementById('confirmPassword');
        var pwError = document.getElementById('passwordError');

        form.addEventListener('submit', function(e){
            var pw = pwInput.value || '';
            var conf = confirmInput.value || '';

            if (pw !== conf) {
                pwError.textContent = 'Heslo sa nezhoduje. Skúste to znova.';
                pwError.style.display = 'block';
                e.preventDefault();
                return;
            }

            // Count letters using Unicode property if available, fallback to ASCII letters
            let lettersCount;
            let letters;
            try {
                letters = pw.match(/\p{L}/gu);
                lettersCount = letters ? letters.length : 0;
            } catch (err) {
                letters = pw.match(/[A-Za-z]/g);
                lettersCount = letters ? letters.length : 0;
            }
            var hasDigit = /\d/.test(pw);

            if (lettersCount < 5 || !hasDigit) {
                pwError.textContent = 'Heslo musí obsahovať minimálne 5 písmen a aspoň jedno číslo.';
                pwError.style.display = 'block';
                e.preventDefault();
                return;
            }

            // clear previous error
            pwError.style.display = 'none';
        });
    })();
</script>
