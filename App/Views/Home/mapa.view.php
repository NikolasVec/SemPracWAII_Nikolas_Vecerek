<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var array $stanoviska */

// Helper: normalize map link or coordinates into a proper absolute URL
function normalize_map_link($s) {
    if ($s === null) return null;
    $s = trim($s);
    if ($s === '') return null;

    // If it's plain coordinates like "lat,lng" (allow spaces)
    if (preg_match('/^\s*([+-]?\d+(?:\.\d+)?)\s*,\s*([+-]?\d+(?:\.\d+)?)\s*$/', $s, $m)) {
        $lat = $m[1]; $lng = $m[2];
        return 'https://www.google.com/maps/search/?api=1&query=' . $lat . ',' . $lng;
    }

    // If it looks like a google maps share with !3d...!4d..., convert to a google maps link (just prefix https if needed)
    if (strpos($s, '!3d') !== false && strpos($s, '!4d') !== false) {
        if (!preg_match('#^https?://#i', $s)) {
            return 'https://www.google.com/maps' . $s;
        }
        return $s;
    }

    // If it contains an @lat,lng sequence, assume it's part of a google maps URL — ensure scheme
    if (preg_match('/@([+-]?\d+(?:\.\d+)?),([+-]?\d+(?:\.\d+)?)/', $s)) {
        if (!preg_match('#^https?://#i', $s)) {
            return 'https://' . ltrim($s, '/');
        }
        return $s;
    }

    // If it contains ?q=lat,lng, ensure scheme
    if (preg_match('/[?&]q=([+-]?\d+(?:\.\d+)?),([+-]?\d+(?:\.\d+)?)/', $s)) {
        if (!preg_match('#^https?://#i', $s)) {
            return 'https://' . ltrim($s, '/');
        }
        return $s;
    }

    // If it's a plain host/path without scheme, add https://
    if (!preg_match('#^https?://#i', $s)) {
        // but if it's clearly not a URL (no dots and no slashes) just return null
        if (strpos($s, '.') === false && strpos($s, '/') === false) {
            return null;
        }
        return 'https://' . ltrim($s, '/');
    }

    // otherwise return as-is
    return $s;
}
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
                            $nazov = $s['nazov'] ?? ($s->nazov ?? 'Bez názvu');
                            $poloha = $s['poloha'] ?? ($s->poloha ?? '');
                            $popis = $s['popis'] ?? ($s->popis ?? '');
                            $mapa = $s['mapa_odkaz'] ?? ($s->mapa_odkaz ?? '');
                            $mapa_href = normalize_map_link($mapa);
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
                                    $x = isset($s['x_pos']) ? $s['x_pos'] : null;
                                    $y = isset($s['y_pos']) ? $s['y_pos'] : null;
                                    if ($x === null || $y === null || $x === '' || $y === '') continue;
                                    // ensure numeric and between 0 and 1
                                    $xn = is_numeric($x) ? floatval($x) : null;
                                    $yn = is_numeric($y) ? floatval($y) : null;
                                    if ($xn === null || $yn === null) continue;
                                    if ($xn < 0 || $xn > 1 || $yn < 0 || $yn > 1) continue;
                                    $markerLeft = ($xn * 100) . '%';
                                    $markerTop = ($yn * 100) . '%';
                                    $nazov = htmlspecialchars($s['nazov'] ?? '', ENT_QUOTES);
                                    $poloha = htmlspecialchars($s['poloha'] ?? '', ENT_QUOTES);
                                    $popis = htmlspecialchars($s['popis'] ?? '', ENT_QUOTES);
                                    $mapa_href = htmlspecialchars(normalize_map_link($s['mapa_odkaz'] ?? '' ) ?? '', ENT_QUOTES);
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

<!-- Modal for showing stanovisko details -->
<div class="modal fade" id="stanoviskoModal" tabindex="-1" aria-labelledby="stanoviskoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="stanoviskoModalLabel">Detaily stanoviska</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zavrieť"></button>
      </div>
      <div class="modal-body">
        <p id="modal-poloha" class="text-muted"></p>
        <div id="modal-popis"></div>
        <p id="modal-mapa" class="mt-2"></p>
        <div class="mt-3 text-center">
          <img id="modal-image" src="" alt="" class="img-fluid" style="max-height:300px; display:none;" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
    const modalEl = document.getElementById('stanoviskoModal');
    if (!modalEl) return;
    const bsModal = new bootstrap.Modal(modalEl);

    // Ensure anchor clicks inside list items don't trigger the parent item click handler
    document.querySelectorAll('.list-group-item-action a').forEach(function(a){
        a.addEventListener('click', function(ev){
            // allow default navigation but prevent the click from bubbling to the list item
            ev.stopPropagation();
        });
    });

    // helper to set modal image: normalize simple filenames to /images/{name}, keep absolute URLs, hide on error
    function setModalImage(im, alt) {
        const imgEl = document.getElementById('modal-image');
        if (!imgEl) return;
        if (!im) {
            imgEl.src = '';
            imgEl.style.display = 'none';
            imgEl.onerror = null;
            return;
        }
        // normalize: if doesn't start with http(s) or slash, assume it's a filename in /images/
        var src = im;
        if (!/^\s*(https?:\/\/|\/)/i.test(im)) {
            src = '/images/' + im.replace(/^\/+/, '');
        }
        imgEl.onerror = function() { imgEl.style.display = 'none'; imgEl.src = ''; imgEl.onerror = null; };
        imgEl.src = src;
        imgEl.alt = alt || 'Obrázok stanoviska';
        imgEl.style.display = 'block';
    }

     // Clicking a list item will try to find its marker on the map and show the bubble there
    document.querySelectorAll('.list-group-item-action').forEach(function(el){
        el.addEventListener('click', function(ev){
            // If the user clicked a real link inside the item, let it behave normally
            if (ev.target && ev.target.closest && ev.target.closest('a')) {
                return; // allow anchor default navigation (e.g. open map link)
            }
            ev.preventDefault();
            const id = el.dataset.id || null;
            if (id) {
                // find marker with same data-id
                const marker = document.querySelector('.overlay-marker[data-id="' + id + '"]');
                if (marker) {
                    showBubbleFor(marker);
                    return;
                }
            }
            // fallback: open modal
            const na = el.dataset.nazov || '';
            const po = el.dataset.poloha || '';
            const pop = el.dataset.popis || '';
            const ma = el.dataset.mapa || '';
            const im = el.dataset.image || '';
            document.getElementById('stanoviskoModalLabel').textContent = na || 'Detaily stanoviska';
            document.getElementById('modal-poloha').textContent = po;
            document.getElementById('modal-popis').innerHTML = pop ? pop.replace(/\n/g, '<br/>') : '';
            const mapaEl = document.getElementById('modal-mapa');
            if (ma) mapaEl.innerHTML = '<a href="' + ma + '" target="_blank" rel="noopener noreferrer">Otvoriť v mape</a>'; else mapaEl.innerHTML = '';
            setModalImage(im, na);
            bsModal.show();
        });
    });

    // function to normalize simple filenames to an image src (same logic as modal helper)
    function normalizeImgSrc(im) {
        if (!im) return '';
        if (/^\s*(https?:\/\/|\/)/i.test(im)) return im.trim();
        return '/images/' + im.replace(/^\/+/, '').trim();
    }

    // escape text for safe insertion inside small bubble
    function escHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    const mapContainer = document.getElementById('mapImageContainer');
    const bubble = document.getElementById('mapBubble');

    function hideBubble() {
        if (bubble) {
            bubble.style.display = 'none';
            bubble.setAttribute('aria-hidden','true');
            bubble.innerHTML = '';
        }
    }

    function showBubbleFor(markerEl) {
        if (!mapContainer || !bubble || !markerEl) return;

        // read data
        const na = markerEl.dataset.nazov || '';
        const po = markerEl.dataset.poloha || '';
        const pop = markerEl.dataset.popis || '';
        const ma = markerEl.dataset.mapa || '';
        const im = markerEl.dataset.image || '';

        // build content
        // Use a template literal to avoid nested-quote escaping issues
        const imgHtml = im ? `<img src="${escHtml(normalizeImgSrc(im))}" alt="${escHtml(na)}" style="width:80px;height:80px;object-fit:cover;margin-right:8px;border-radius:4px;" onerror="this.style.display='none'"/>` : '';
        const popHtml = pop ? `${escHtml(pop).replace(/\n/g,'<br/>')}` : '';
        const mapLinkHtml = ma ? `<div class="mt-2"><a href="${escHtml(ma)}" target="_blank" rel="noopener noreferrer">Otvoriť v mape</a></div>` : '';

        let html = `
            <div class="bubble-card p-2">
                <div class="d-flex align-items-start">
                    ${imgHtml}
                    <div><strong>${escHtml(na)}</strong>
                        ${po ? `<div class="small text-muted">${escHtml(po)}</div>` : ''}
                    </div>
                </div>
                ${pop ? `<div class="mt-2 small">${popHtml}</div>` : ''}
                ${mapLinkHtml}
                <div class="bubble-tail" aria-hidden="true"></div>
            </div>`;

        bubble.innerHTML = html;
        bubble.style.display = 'block';
        bubble.setAttribute('aria-hidden','false');

        // attach handlers: click-to-open-modal on bubble card
        const bubbleCard = bubble.querySelector('.bubble-card');
        if (bubbleCard) {
            bubbleCard.style.cursor = 'pointer';
            bubbleCard.addEventListener('click', function(ev){
                ev.stopPropagation();
                // open full modal with same data
                document.getElementById('stanoviskoModalLabel').textContent = na || 'Detaily stanoviska';
                document.getElementById('modal-poloha').textContent = po;
                document.getElementById('modal-popis').innerHTML = pop ? pop.replace(/\n/g, '<br/>') : '';
                const mapaEl = document.getElementById('modal-mapa');
                if (ma) mapaEl.innerHTML = '<a href="' + ma + '" target="_blank" rel="noopener noreferrer">Otvoriť v mape</a>'; else mapaEl.innerHTML = '';
                setModalImage(im, na);
                hideBubble();
                bsModal.show();
            });
        }

        // Positioning needs to happen after images inside the bubble load (they change height)
        function positionBubble() {
            // read fresh rects here (marker might have moved or DOM reflow changed sizes)
            const contRect = mapContainer.getBoundingClientRect();
            const mRect = markerEl.getBoundingClientRect();

            // reset position so bounding rect is accurate
            bubble.style.left = '0px';
            bubble.style.top = '0px';
            const preRect = bubble.getBoundingClientRect();
            const bubW = preRect.width;
            const bubH = preRect.height;

            const relMarkerLeft = mRect.left - contRect.left; // marker left relative to container
            const relMarkerTop = mRect.top - contRect.top;   // marker top relative to container
            const relMarkerCenterX = relMarkerLeft + (mRect.width / 2);
            const relMarkerCenterY = relMarkerTop + (mRect.height / 2);

            const contWidth = contRect.width;
            const contHeight = contRect.height;

            // Try to place bubble above the marker, centered horizontally on the marker
            const markerHalfH = mRect.height / 2;
            const offset = 8; // gap between bubble and marker
            let left = relMarkerCenterX - (bubW / 2);
            let top = relMarkerCenterY - bubH - markerHalfH - offset;
            let placeAbove = true;

            // If there's not enough space above, place below the marker
            if (top < 8) {
                top = relMarkerCenterY + markerHalfH + offset;
                placeAbove = false;
            }

            // Clamp horizontally inside container with some margin
            if (left < 8) left = 8;
            if (left + bubW > contWidth - 8) left = Math.max(8, contWidth - bubW - 8);

            // Clamp vertically inside container
            if (top < 8) top = 8;
            if (top + bubH > contHeight - 8) top = Math.max(8, contHeight - bubH - 8);

            // apply position
            bubble.style.left = Math.round(left) + 'px';
            bubble.style.top = Math.round(top) + 'px';

            // position tail to point from bubble toward marker center
            const tail = bubble.querySelector('.bubble-tail');
            if (tail) {
                // Reset tail styles
                tail.style.left = '';
                tail.style.top = '';
                tail.style.right = '';
                tail.style.bottom = '';
                tail.style.borderLeft = '8px solid transparent';
                tail.style.borderRight = '8px solid transparent';
                tail.style.borderTop = 'none';
                tail.style.borderBottom = 'none';

                // Marker center X relative to bubble left
                const markerCenterX_relBubble = relMarkerCenterX - left;
                const tailLeft = Math.round(markerCenterX_relBubble - 8); // center tail (tail width 16)
                // keep tail within bubble bounds
                const maxLeft = Math.max(8, bubW - 24);
                const finalTailLeft = Math.max(8, Math.min(tailLeft, maxLeft));

                if (placeAbove) {
                    // Bubble is above marker -> tail at bottom of bubble pointing down
                    tail.style.top = Math.round(bubH) + 'px';
                    tail.style.left = finalTailLeft + 'px';
                    tail.style.borderTop = '8px solid transparent';
                    tail.style.borderBottom = '8px solid #fff';
                    tail.style.borderLeft = '8px solid transparent';
                    tail.style.borderRight = '8px solid transparent';
                    // ensure tail sits visually at bottom edge
                    tail.style.transform = 'translateY(0)';
                } else {
                    // Bubble is below marker -> tail at top of bubble pointing up
                    tail.style.top = '-8px';
                    tail.style.left = finalTailLeft + 'px';
                    tail.style.borderBottom = '8px solid transparent';
                    tail.style.borderTop = '8px solid #fff';
                    tail.style.borderLeft = '8px solid transparent';
                    tail.style.borderRight = '8px solid transparent';
                    tail.style.transform = 'translateY(0)';
                }
            }
        }

        // If there are images inside the bubble, wait for them to load before positioning
        const imgs = bubble.querySelectorAll('img');
        if (imgs && imgs.length > 0) {
            let remaining = 0;
            imgs.forEach(function(img){ if (!img.complete) remaining++; });
            if (remaining === 0) {
                // already loaded
                positionBubble();
            } else {
                const oneDone = function(){ remaining--; if (remaining === 0) { setTimeout(positionBubble, 20); } };
                imgs.forEach(function(img){
                    if (!img.complete) {
                        img.addEventListener('load', oneDone);
                        img.addEventListener('error', oneDone);
                    }
                });
                // safety fallback in case load events don't fire
                setTimeout(positionBubble, 600);
            }
        } else {
            positionBubble();
        }
    }

    // clicks on markers: show bubble
    document.querySelectorAll('.overlay-marker').forEach(function(m){
        m.addEventListener('click', function(ev){
            ev.preventDefault(); ev.stopPropagation();
            showBubbleFor(m);
        });
    });

    // clicking outside hides the bubble
    document.addEventListener('click', function(ev){
        const isInside = bubble && (bubble.contains(ev.target) || ev.target.closest('.overlay-marker'));
        if (!isInside) hideBubble();
    });

    // hide on resize/scroll
    window.addEventListener('resize', hideBubble);
    mapContainer.addEventListener('scroll', hideBubble);
  })();
  </script>
