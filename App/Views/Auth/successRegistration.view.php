<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('auth');
?>


<a href="<?= $link->url('home.index') ?>" class="home-button btn btn-outline-secondary" aria-label="Domov">Domov</a>

<div class="container">
    <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            <div class="card card-signin my-5">
                <div class="card-body">
                    <h5 class="card-title text-center">Registrácia úspešná</h5>
                    <p class="text-center">Vaša registrácia prebehla úspešne. Teraz sa môžete prihlásiť.</p>

                    <div class="text-center">
                        <a href="<?= $link->url('auth.login') ?>" class="btn btn-primary">Prihlásiť sa</a>
                    </div>

                    <div class="text-center mt-3">
                        <a href="<?= $link->url('home.index') ?>" class="btn btn-secondary">Späť na domovskú stránku</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= $link->asset('js/script.js') ?>"></script>
