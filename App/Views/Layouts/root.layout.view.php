<?php

/** @var string $contentHTML */
/** @var \Framework\Auth\AppUser $user */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Support\LayoutPresenter $layoutPresenter */

// layoutPresenter je injektovaný frameworkom (ViewResponse) a je dostupný tu
// ako $layoutPresenter. Vyhni sa inštancovaniu presenterov vo view.
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <title><?= App\Configuration::APP_NAME ?></title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $layoutPresenter->asset('favicons/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $layoutPresenter->asset('favicons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $layoutPresenter->asset('favicons/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= $layoutPresenter->asset('favicons/site.webmanifest') ?>">
    <link rel="shortcut icon" href="<?= $layoutPresenter->asset('favicons/favicon.ico') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= $layoutPresenter->asset('css/styl.css') ?>">
    <script src="<?= $layoutPresenter->asset('js/script.js') ?>"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<!-- Nastavenie navbaru -->
<nav class="navbar navbar-expand-sm fixed-top">
    <!-- Hamburger (offcanvas) v ľavom rohu -->
    <button class="btn btn-outline-light me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu" aria-label="Otvoriť menu" style="margin-left:8px;">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="container-fluid d-flex justify-content-center align-items-center">
        <!-- Ľavé tlačidlá (skryteľné na úzkom displeji) -->
        <div class="d-flex align-items-center nav-left-group" style="gap: 2rem; margin-left: 7vw;">
            <a class="nav-link px-3" href="<?= $layoutPresenter->url('home.registrationPage') ?>"><strong>REGISTRÁCIA</strong></a>
            <a class="nav-link px-3" href="<?= $layoutPresenter->url('home.galleryPage') ?>"><strong>GALERIA</strong></a>
        </div>

        <!-- Logo v strede (vždy viditeľné) -->
        <a class="navbar-brand mx-4 logo-with-circle" href="<?= $layoutPresenter->url('home.index') ?>" style="position: relative; display: flex; align-items: center; justify-content: center;" title="Hlavná stránka">
            <span class="logo-circle"></span>
            <img src="<?= $layoutPresenter->asset('images/BehPoPivo_logo.png') ?>" title="Hlavná stránka" alt="Framework Logo">
        </a>

        <!-- Pravé tlačidlá (skryteľné na úzkom displeji) -->
        <div class="d-flex align-items-center nav-right-group" style="gap: 2rem; margin-right: 7vw;">
            <a class="nav-link px-3" href="<?= $layoutPresenter->url('home.mapa') ?>"><strong>MAPA</strong></a>
            <a class="nav-link px-3" href="<?= $layoutPresenter->url('home.resultsPage') ?>"><strong>VYHODNOTENIE</strong></a>
        </div>
    </div>

    <!-- Úplne pravý kraj: Log in/Log out (skryteľné na úzkom displeji) -->
    <div class="d-flex align-items-center ms-1 nav-auth-group" style="min-width: 90px;">
        <?php if ($layoutPresenter->isLoggedIn()) { ?>
            <?php if ($layoutPresenter->isAdmin()) { ?>
                <!-- Pre adminov: ikona do navbaru (odhlásenie je v menu) -->
                <a class="nav-link navbar-text me-2" href="<?= $layoutPresenter->adminUrl() ?>" title="Admin">
                    <i class="bi bi-person fs-3"></i>
                </a>
            <?php } else { ?>
                <!-- Pre bežných užívateľov: ikona odkazuje na profil -->
                <a class="nav-link navbar-text me-2" href="<?= $layoutPresenter->profileUrl() ?>" title="Môj profil">
                    <i class="bi bi-person fs-3"></i>
                </a>
            <?php } ?>
        <?php } else { ?>
            <a class="nav-link" href="<?= $layoutPresenter->loginUrl() ?>"><strong>Log in</strong></a>
        <?php } ?>
    </div>
</nav>

<!-- Offcanvas menu (hamburger) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
    <div class="offcanvas-header">
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Zatvoriť"></button>
    </div>
    <div class="offcanvas-body">
        <div class="list-group">
            <a class="list-group-item list-group-item-action" href="<?= $layoutPresenter->url('home.registrationPage') ?>"><strong>REGISTRÁCIA</strong></a>
            <a class="list-group-item list-group-item-action" href="<?= $layoutPresenter->url('home.galleryPage') ?>"><strong>GALERIA</strong></a>
            <a class="list-group-item list-group-item-action" href="<?= $layoutPresenter->url('home.mapa') ?>"><strong>MAPA</strong></a>
            <a class="list-group-item list-group-item-action" href="<?= $layoutPresenter->url('home.resultsPage') ?>"><strong>VYHODNOTENIE</strong></a>
            <?php if ($layoutPresenter->isLoggedIn()) { ?>
                <?php if ($layoutPresenter->isAdmin()) { ?>
                    <!-- Admin: link na administračné rozhranie a odhlásenie -->
                    <a class="list-group-item list-group-item-action" href="<?= $layoutPresenter->adminUrl() ?>"><strong>Administrácia</strong></a>
                    <a class="list-group-item list-group-item-action" href="<?= $layoutPresenter->url('auth.logout') ?>"><strong>Log out</strong></a>
                <?php } else { ?>
                    <!-- Pre bežných používateľov: odkaz na profil (odhlásenie na profile) -->
                    <a class="list-group-item list-group-item-action" href="<?= $layoutPresenter->profileUrl() ?>"><strong>Môj profil</strong></a>
                <?php } ?>
            <?php } else { ?>
                <a class="list-group-item list-group-item-action" href="<?= $layoutPresenter->loginUrl() ?>"><strong>Log in</strong></a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="container-fluid mt-3">
    <div class="web-content">
        <?= $contentHTML ?>
    </div>
</div>

<?php // vloženie pätičky ?>
<?php try { include __DIR__ . '/footer.layout.php'; } catch (\Throwable $e) { /* ignore if missing */ } ?>
</body>
</html>
