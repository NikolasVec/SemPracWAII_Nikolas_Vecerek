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
        <!-- Left: list of locations -->
        <div class="col-md-4">
            <br>
            <br>
            <br>
            <h3>Stanoviská</h3>
            <div class="list-group">
                <?php if (!empty($stanoviska) && is_array($stanoviska)): ?>
                    <?php foreach ($stanoviska as $s): ?>
                        <?php
                            $nazov = $s['nazov'] ?? 'Bez názvu';
                            $poloha = $s['poloha'] ?? '';
                            $popis = $s['popis'] ?? '';
                            $mapa_href = $s['mapa_href'] ?? null; // prepared by presenter
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

        <!-- Right: big map image -->
        <div class="col-md-8">
            <br>
            <br>
            <br>
            <div class="card">
                <div class="card-body p-0" style="background:#f8f9fa;">
                    <div id="mapImageContainer" style="position:relative; --marker-offset-x: -25%; --marker-offset-y: -90%;">
                        <img id="mapImg" src="/images/mapa_MartinNEW.png" alt="Mapa Martin" class="img-fluid w-100" style="max-height:600px; object-fit:contain; display:block;">
                        <?php if (!empty($stanoviska) && is_array($stanoviska)): ?>
                            <?php foreach ($stanoviska as $s): ?>
                                <?php
                                    $markerLeft = $s['markerLeft'] ?? null; // prepared by presenter, e.g. '12.3%'
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
                                         <img src="/images/Beer_icon.png" alt="Pivo" />
                                     </div>
                                 </div>
                             <?php endforeach; ?>
                         <?php endif; ?>
                         <!-- map bubble inserted inside container so positioning is relative to this element -->
                        <div id="mapBubble" class="map-bubble" aria-hidden="true"></div>
                     </div>
                  </div>
               </div>
           </div>
       </div>
   </div>
 </div>

     <!-- Map bubble (small info popup anchored on the map) -->
     <style>
     .map-bubble { position:absolute; z-index:999; display:none; pointer-events:auto; max-width:320px; }
     .map-bubble .bubble-card { background: #fff; border:1px solid rgba(0,0,0,0.12); border-radius:6px; box-shadow:0 6px 18px rgba(0,0,0,0.12); padding:8px; position:relative; overflow:visible; }
     /* Tail (arrow) - direction and placement set dynamically from JS */
     .map-bubble .bubble-tail { position:absolute; width:0; height:0; pointer-events:none; }

    /* Marker design: rounded pill with beer icon and small diamond pointer */
    .overlay-marker .marker-shape {
        position:relative;
        width:45px;
        height:45px;
        border-radius:8px;
        background: linear-gradient(180deg, #ff5252 0%, #b71c1c 100%);
        display:flex;
        align-items:center;
        justify-content:center;
        border:2px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.35);
        overflow:visible; /* allow pointer to be visible outside the rounded box */
        z-index:1; /* keep marker above its pointer */
    }
    /* Shift markers slightly to the right and higher than the stored coordinates. Tunable via CSS variables on #mapImageContainer. */
    /* Adjusted defaults so markers appear more to the right (smaller negative X translate). */
    .overlay-marker { transform: translate(var(--marker-offset-x, -10%), var(--marker-offset-y, -90%)); }
    .overlay-marker .marker-shape img { width:60%; height:60%; object-fit:contain; display:block; }
    /* small pointer under the marker (diamond rotated) */
    .overlay-marker .marker-shape::after {
        content: "";
        position:absolute;
        left:50%;
        transform:translateX(-50%) rotate(225deg); /* rotate diamond 180deg to flip pointer */
        width:12px;
        height:12px;
        bottom:-6px;
        background: #b71c1c;
        border:2px solid #fff; /* full white outline so pointer is visible */
        box-shadow: 0 3px 8px rgba(0,0,0,0.2);
        z-index:0; /* place pointer visually slightly behind the marker body */
    }
     </style>

<script src="/js/mapa-bubbles.js"></script>
