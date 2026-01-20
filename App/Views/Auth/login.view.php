<?php

/** @var string|null $message */
/** @var int|null $attemptsLeft */
/** @var int|null $lockoutExpiresAt */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('auth');

// Prepare lockout state for the form
$attemptsLeft = $attemptsLeft ?? null;
$lockoutExpiresAt = $lockoutExpiresAt ?? null;
$isLocked = $lockoutExpiresAt && ((int)$lockoutExpiresAt > time());
$disabledAttr = $isLocked ? 'disabled' : '';
$lockMinutes = $isLocked ? (int)ceil(((int)$lockoutExpiresAt - time()) / 60) : 0;
?>

<div class="container">
    <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            <div class="card card-signin my-5">
                <div class="card-body">
                    <h5 class="card-title text-center">Prihlásenie</h5>
                    <div class="text-center text-danger mb-3">
                        <?= @$message ?>
                    </div>

                    <?php // Show a specific notice only when the user has exactly one attempt left ?>
                    <?php if ($attemptsLeft !== null && !$isLocked && (int)$attemptsLeft === 1): ?>
                        <div class="text-center text-danger mb-2"><strong>Zostáva posledný pokus.</strong></div>
                    <?php endif; ?>

                    <?php if ($isLocked): ?>
                        <div class="text-center text-danger mb-3">Prekročili ste počet pokusov. Skúste znova o <?= date('H:i', (int)$lockoutExpiresAt) ?> (približne o <?= $lockMinutes ?> minút).</div>
                    <?php endif; ?>

                    <form class="form-signin" method="post" action="<?= $link->url("login") ?>">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)($csrfToken ?? '')) ?>">
                        <div class="form-label-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input name="email" type="email" id="email" class="form-control" placeholder="Email"
                                   required autofocus <?= $disabledAttr ?> >
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input name="password" type="password" id="password" class="form-control"
                                   placeholder="Password" required <?= $disabledAttr ?> >
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary" type="submit" name="submit" <?= $disabledAttr ?>>Log in
                            </button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <a href="<?= $link->url('auth.newUserRegistration') ?>" class="btn btn-secondary">Zaregistruj sa</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
