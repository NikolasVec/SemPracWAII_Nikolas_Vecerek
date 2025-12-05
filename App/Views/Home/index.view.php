<?php

/** @var \Framework\Support\LinkGenerator $link */
?>
<div class="container-fluid p-0 m-0">
    <div class="row">
            <div class="text-center p-0 m-0 position-relative">
                <img src="<?= $link->asset('images/BEHPOPIVO_OBR_BEZTEXTU.png') ?>" class="w-100"
                     style="display:block;margin:0;padding:0;border:0;">
            <div class="text-center p-0 m-0 home-image-container">
                <div class="behpopivo-nadpis">BEH PO PIVO</div>
                <p>
                    Congratulations, you have successfully installed and run the framework
                    <strong>Vaííčko</strong> <?= App\Configuration::FW_VERSION ?>!<br>
                    We hope that you will create a great application using this framework.<br>
                </p>
                <p>
                    This simple framework was created for teaching purposes and to better understand how the MVC
                    architecture works.<br>
                    It is intended for students of the subject <em>web application development</em>, but not only
                    for them.
                </p>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col text-center">
            <h4>Authors</h4>
            <div>
                <a href="mailto:Patrik.Hrkut@fri.uniza.sk">doc. Ing. Patrik Hrkút, PhD.</a><br>
                <a href="mailto:Michal.Duracik@fri.uniza.sk">Ing. Michal Ďuračík, PhD.</a><br>
                <a href="mailto:Matej.Mesko@fri.uniza.sk">Ing. Matej Meško, PhD.</a><br><br>
                &copy; 2020-<?= date('Y') ?> University of Žilina, Faculty of Management Science and Informatics,
                Department of Software Technologies
            </div>
        </div>
    </div>
