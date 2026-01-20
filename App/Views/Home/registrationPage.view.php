<?php

/** @var \Framework\Support\LinkGenerator $link */

?>

<h1>
</h1>
<br><br>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card registration-card shadow">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Registrácia na beh</h2>
                    <?php
                    if (!empty($success)) {
                        echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>';
                    }
                    if (!empty($error)) {
                        echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
                    }

                    // Show a special, actionable notice when the provided email is not a registered user
                    if (!empty($userNotRegistered)) {
                        ?>
                        <div class="alert alert-warning">
                            <p class="mb-2">Prihláste sa alebo si vytvorte účet.</p>
                            <div class="d-flex gap-2">
                                <a href="<?= $link->url('auth.login') ?>" class="btn btn-primary">Prihlásiť sa</a>
                                <a href="<?= $link->url('auth.newUserRegistration') ?>" class="btn btn-secondary">Zaregistrujte sa</a>
                            </div>
                        </div>
                        <?php
                    }

                    // form values come from controller in $form array; provide safe defaults
                    $f = $form ?? ['meno' => '', 'priezvisko' => '', 'email' => '', 'pohlavie' => 'M'];
                    ?>
                    <form method="post" action="">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)($csrfToken ?? '')) ?>">
                        <div class="mb-3">
                            <label for="meno" class="form-label">Meno:</label>
                            <input type="text" id="meno" name="meno" class="form-control" required value="<?= htmlspecialchars($f['meno'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="priezvisko" class="form-label">Priezvisko:</label>
                            <input type="text" id="priezvisko" name="priezvisko" class="form-control" required value="<?= htmlspecialchars($f['priezvisko'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($f['email'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="pohlavie" class="form-label">Pohlavie:</label>
                            <select id="pohlavie" name="pohlavie" class="form-select" required>
                                <option value="M" <?= (isset($f['pohlavie']) && ($f['pohlavie'] === 'M')) ? 'selected' : '' ?>>Muž</option>
                                <option value="Ž" <?= (isset($f['pohlavie']) && ($f['pohlavie'] === 'Ž')) ? 'selected' : '' ?>>Žena</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-block">Registrovať</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
