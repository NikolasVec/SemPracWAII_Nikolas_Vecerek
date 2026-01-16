<?php

/** @var \Framework\Auth\DbUser|null $identity */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('root');
?>
<br>
<br>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Môj profil</h3>

                    <?php if ($identity === null): ?>
                        <div class="alert alert-warning">Používateľ nie je prihlásený.</div>
                    <?php else: ?>
                        <dl class="row">
                            <dt class="col-sm-4">Meno a priezvisko</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($identity->getFirstName() . ' ' . $identity->getLastName()) ?></dd>

                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($identity->getEmail()) ?></dd>

                            <dt class="col-sm-4">Zabehnuté kilometre</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars(number_format($identity->getKilometres(), 2, '.', '')) ?></dd>

                            <dt class="col-sm-4">Počet vypitých pív</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars((string)$identity->getBeers()) ?></dd>
                        </dl>

                        <div class="mt-3">
                            <?php if (!$identity->isAdmin()): ?>
                                <a class="btn btn-danger ms-2" href="<?= $link->url('auth.logout') ?>">Odhlásiť sa</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
