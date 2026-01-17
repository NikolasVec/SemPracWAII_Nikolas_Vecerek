<?php

/** @var \Framework\Support\LinkGenerator $link */
?>

<div class="container-fluid p-0 m-0 home-top-lift">
    <div class="row">
            <div class="text-center p-0 m-0 position-relative">

                <img src="<?= $link->asset('images/BEHPOPIVO_OBR_BEZTEXTU.png') ?>" alt="Banner" class="w-100 home-hero-img"
                     style="display:block;margin:0;padding:0;border:0;">
            <div class="text-center p-0 m-0 home-image-container">
                <div class="behpopivo-nadpis">BEH PO PIVO</div>
            </div>
        </div>
    </div>

    <!-- New three full-width cells under the main heading -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="row">
                <div class="col-md-4 p-2">
                    <a href="<?= $link->url('home.informacie') ?>" class="text-decoration-none text-dark">
                        <div class="p-4 border h-100 d-flex flex-column justify-content-center align-items-center text-center text-dark" style="background-color:#ffce0a;">
                            <img src="<?= $link->asset('images/Bezec.jpg') ?>" alt="Bežec" class="img-fluid mb-2" style="max-height:160px;object-fit:contain;">
                            <h5>Informácie a pravidlá</h5>
                            <p class="mb-0">Všetky informácie a pravidlá behu nájdete tu.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 p-2">
                    <a href="https://pivovarmartins.sk/charakternost-nasich-piv/" target="_blank" rel="noopener" class="text-decoration-none text-dark">
                        <div class="p-4 border h-100 d-flex flex-column justify-content-center align-items-center text-center text-dark" style="background-color:#ffce0a;">
                            <img src="<?= $link->asset('images/Pivovar-Martins.jpg') ?>" alt="Pivovar Martins" class="img-fluid mb-2" style="max-height:160px;object-fit:contain;">
                            <h5>Čo sa bude piť</h5>
                            <p class="mb-0">Počas behu budeme podávať pivo z Pivovaru Martins.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 p-2">
                    <div class="p-4 border h-100 d-flex flex-column justify-content-center align-items-center text-center text-dark" style="background-color:#ffce0a;">
                        <img src="<?= $link->asset('images/Obrazok_tricko.png') ?>" alt="Tričko podujatia" class="img-fluid mb-2" style="max-height:160px;object-fit:contain;">
                        <h5>Oficiálne tričko</h5>
                        <p class="mb-0">Pohodlné tričko s motívom podujatia sa da zakúpiť na štarte a v cieli behu.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
