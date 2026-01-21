<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var array $stanoviska */
?>

<?php if (!empty($mapa_error)): ?>
    <div class="container mt-2">
        <div class="alert alert-danger">Chyba pri načítaní stanovísk: <strong><?= htmlspecialchars($mapa_error) ?></strong></div>
    </div>
<?php endif; ?>

<div class="container">
    <div class="row">
        <!-- Ľavý stĺpec: zoznam stanovísk -->
        <div class="col-md-4">
            <br>
            <br>
            <br>
            <h1>Stanovištia</h1>
            <div class="list-group">
                <?php if (!empty($stanoviska) && is_array($stanoviska)): ?>
                    <?php foreach ($stanoviska as $s): ?>
                        <?php
                            $nazov = $s['nazov'] ?? 'Bez názvu';
                            $poloha = $s['poloha'] ?? '';
                            $popis = $s['popis'] ?? '';
                            $mapa_href = $s['mapa_href'] ?? null;
                        ?>
                        <div class="list-group-item list-group-item-action" role="button" tabindex="0"
                           data-id="<?= htmlspecialchars($s['ID_stanoviska'] ?? '', ENT_QUOTES) ?>"
                           data-nazov="<?= htmlspecialchars($nazov, ENT_QUOTES) ?>"
                           data-poloha="<?= htmlspecialchars($poloha, ENT_QUOTES) ?>"
                           data-popis="<?= htmlspecialchars($popis, ENT_QUOTES) ?>"
                           data-mapa="<?= htmlspecialchars($mapa_href ?? '', ENT_QUOTES) ?>"
                           data-image="<?= htmlspecialchars($s['obrazok_odkaz'] ?? '', ENT_QUOTES) ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <strong><?= htmlspecialchars($nazov) ?></strong>
                                <?php if ($mapa_href): ?>
                                    <small><a href="<?= htmlspecialchars($mapa_href) ?>" target="_blank" rel="noopener noreferrer">Mapa</a></small>
                                <?php endif; ?>
                            </div>
                            <?php if ($poloha): ?>
                                <div class="small text-muted"><?= htmlspecialchars($poloha) ?></div>
                            <?php endif; ?>
                            <?php if ($popis): ?>
                                <div class="mt-1"><?= nl2br(htmlspecialchars($popis)) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="list-group-item">Žiadne stanoviská na zobrazenie.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pravý stĺpec: mapa -->
        <div class="col-md-8">
            <br>
            <br>
            <br>
            <div class="card">
                <div class="card-body p-0" style="background:#f8f9fa;">
                    <div id="mapImageContainer" style="position:relative; --marker-offset-x: -25%; --marker-offset-y: -90%;">
                        <img id="mapImg" src="<?= $link->asset('images/mapa_MartinNEW.png') ?>" alt="Mapa Martin" class="img-fluid w-100" style="max-height:600px; object-fit:contain; display:block;">
                        <?php if (!empty($stanoviska) && is_array($stanoviska)): ?>
                            <?php foreach ($stanoviska as $s): ?>
                                <?php
                                    $markerLeft = $s['markerLeft'] ?? null; // pripravené presenterom, napr. '12.3%'
                                    $markerTop = $s['markerTop'] ?? null;
                                    if ($markerLeft === null || $markerTop === null) continue;
                                    $nazov = htmlspecialchars($s['nazov'] ?? '', ENT_QUOTES);
                                    $poloha = htmlspecialchars($s['poloha'] ?? '', ENT_QUOTES);
                                    $popis = htmlspecialchars($s['popis'] ?? '', ENT_QUOTES);
                                    $mapa_href = htmlspecialchars($s['mapa_href'] ?? '', ENT_QUOTES);
                                    $img_link = htmlspecialchars($s['obrazok_odkaz'] ?? '', ENT_QUOTES);
                                ?>
                                <div class="overlay-marker" data-id="<?= htmlspecialchars($s['ID_stanoviska'] ?? '', ENT_QUOTES) ?>" data-nazov="<?= $nazov ?>" data-poloha="<?= $poloha ?>" data-popis="<?= $popis ?>" data-mapa="<?= $mapa_href ?>" data-image="<?= $img_link ?>" style="position:absolute; left:<?= $markerLeft ?>; top:<?= $markerTop ?>; z-index:50; cursor:pointer;">
                                     <div class="marker-shape" aria-hidden="true">
                                         <img src="<?= $link->asset('images/Beer_icon.png') ?>" alt="Pivo" />
                                     </div>
                                 </div>
                             <?php endforeach; ?>
                         <?php endif; ?>
                         <!-- Bublina mapy vložená v kontejnery; pozícia je relatívna k tomuto elementu -->
                        <div id="mapBubble" class="map-bubble" aria-hidden="true"></div>
                     </div>
                  </div>
               </div>
           </div>
       </div>
   </div>
 </div>

<link rel="stylesheet" href="<?= $link->asset('css/mapa.css') ?>">
<script src="<?= $link->asset('js/mapa-bubbles.js') ?>"></script>
