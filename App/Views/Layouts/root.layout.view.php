<?php

/** @var string $contentHTML */
/** @var \Framework\Auth\AppUser $user */
/** @var \Framework\Support\LinkGenerator $link */
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <title><?= App\Configuration::APP_NAME ?></title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $link->asset('favicons/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $link->asset('favicons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $link->asset('favicons/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= $link->asset('favicons/site.webmanifest') ?>">
    <link rel="shortcut icon" href="<?= $link->asset('favicons/favicon.ico') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= $link->asset('css/styl.css') ?>">
    <script src="<?= $link->asset('js/script.js') ?>"></script>
</head>
<body>

<!-- Nastavenie navbaru -->
<nav class="navbar navbar-expand-sm fixed-top">
    <!-- Hamburger (offcanvas) v ľavom rohu -->
    <button class="btn btn-outline-light me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu" aria-label="Otvoriť menu" style="margin-left:8px;">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="container-fluid d-flex justify-content-center align-items-center">
        <div class="d-flex align-items-center" style="gap: 2rem; margin-left: 7vw;">
            <!-- Ľavé tlačidlá -->
            <a class="nav-link px-3" href="<?= $link->url('home.registrationPage') ?>"><strong>REGISTRÁCIA</strong></a>
            <a class="nav-link px-3" href="<?= $link->url('home.galleryPage') ?>"><strong>GALERIA</strong></a>
            <!-- Logo v strede -->
            <a class="navbar-brand mx-4 logo-with-circle" href="<?= $link->url('home.index') ?>" style="position: relative; display: flex; align-items: center; justify-content: center;" title="Hlavná stránka">
                <span class="logo-circle"></span>
                <img src="<?= $link->asset('images/BehPoPivo_logo.png') ?>" title="Hlavná stránka" alt="Framework Logo">
            </a>
            <!-- Pravé tlačidlá -->
            <a class="nav-link px-3" href="<?= $link->url('home.contact') ?>"><strong>MAPA</strong></a>
            <a class="nav-link px-3" href="<?= $link->url('home.resultsPage') ?>"><strong>VÝHODNOTENIE</strong></a>
        </div>
    </div>

    <!-- Úplne pravý kraj: Log in/Log out -->
    <div class="d-flex align-items-center ms-3" style="min-width: 90px;">
        <?php if ($user->isLoggedIn()) { ?>
            <span class="navbar-text me-2"><?= $user->getName() ?></span>
            <a class="nav-link" href="<?= $link->url('auth.logout') ?>"><strong>Log out</strong></a>
        <?php } else { ?>
            <a class="nav-link" href="<?= App\Configuration::LOGIN_URL ?>"><strong>Log in</strong></a>
        <?php } ?>
    </div>
</nav>

<!-- Offcanvas menu chứa položky hamburgeru -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasMenuLabel"><?= App\Configuration::APP_NAME ?></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Zatvoriť"></button>
    </div>
    <div class="offcanvas-body">
        <div class="list-group">
            <a class="list-group-item list-group-item-action" href="<?= $link->url('home.registrationPage') ?>"><strong>REGISTRÁCIA</strong></a>
            <a class="list-group-item list-group-item-action" href="<?= $link->url('home.galleryPage') ?>"><strong>GALERIA</strong></a>
            <a class="list-group-item list-group-item-action" href="<?= $link->url('home.contact') ?>"><strong>MAPA</strong></a>
            <a class="list-group-item list-group-item-action" href="<?= $link->url('home.resultsPage') ?>"><strong>VÝHODNOTENIE</strong></a>
            <?php if ($user->isLoggedIn()) { ?>
                <a class="list-group-item list-group-item-action" href="<?= $link->url('auth.logout') ?>"><strong>Log out</strong></a>
            <?php } else { ?>
                <a class="list-group-item list-group-item-action" href="<?= App\Configuration::LOGIN_URL ?>"><strong>Log in</strong></a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="container-fluid mt-3">
    <div class="web-content">
        <?= $contentHTML ?>
    </div>
</div>
</body>
</html>
