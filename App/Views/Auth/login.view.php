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
    <div class="row justify-content-center">
        <div class="col-sm-9 col-md-7 col-lg-5">
            <!-- moved card closer to top: replaced my-5 with mt-4 and added subtle shadow -->
            <div class="card card-signin mt-4 shadow-sm">
                <!-- yellow top stripe for visual accent -->
                <div style="height:8px; background: #ffd54f; border-top-left-radius: .25rem; border-top-right-radius: .25rem;"></div>
                <div class="card-body" style="background: linear-gradient(to bottom, #fffaf0, #ffffff);">
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
                            <!-- use a yellow (warning) button for emphasis -->
                            <button class="btn btn-warning text-dark" type="submit" name="submit" <?= $disabledAttr ?>>Prihlásiť sa
                            </button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <!-- registration link styled with yellow outline to match theme -->
                        <a href="<?= $link->url('auth.newUserRegistration') ?>" class="btn btn-outline-warning">Zaregistruj sa</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


