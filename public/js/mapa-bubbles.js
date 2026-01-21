(function(){
    'use strict';

    // helper to normalize simple filenames to an image src (same logic as previous modal helper)
    function normalizeImgSrc(im) {
        if (!im) return '';
        // If the image is already an absolute URL (http(s)://) or root-relative (/...), return as-is
        if (/^\s*(https?:\/\/|\/)\s*/i.test(im) || /^\s*\//.test(im)) return im.trim();
        return '/images/' + im.replace(/^\/+/, '').trim();
    }

    // escape text for safe insertion inside small bubble
    function escHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // Ensure container and bubble exist; be defensive in case DOM structure changes
    function ensureMapElements() {
        var mapContainer = document.getElementById('mapImageContainer') || document.body;
        var bubble = document.getElementById('mapBubble');
        if (!bubble) {
            bubble = document.createElement('div');
            bubble.id = 'mapBubble';
            bubble.className = 'map-bubble';
            mapContainer.appendChild(bubble);
        }
        return { mapContainer: mapContainer, bubble: bubble };
    }

    function hideBubble(bubble) {
        if (bubble) {
            bubble.style.display = 'none';
            bubble.setAttribute('aria-hidden','true');
            bubble.innerHTML = '';
        }
    }

    function showBubbleFor(markerEl) {
        var els = ensureMapElements();
        var mapContainer = els.mapContainer;
        var bubble = els.bubble;
        if (!bubble || !markerEl) return;

        // read data
        var na = markerEl.dataset.nazov || '';
        var po = markerEl.dataset.poloha || '';
        var pop = markerEl.dataset.popis || '';
        var ma = markerEl.dataset.mapa || '';
        var im = markerEl.dataset.image || '';

        // build content
        var imgHtml = im ? '<img src="' + escHtml(normalizeImgSrc(im)) + '" alt="' + escHtml(na) + '" style="width:80px;height:80px;object-fit:cover;margin-right:8px;border-radius:4px;"/>' : '';
        var popHtml = pop ? (escHtml(pop).replace(/\n/g,'<br/>')) : '';
        var mapLinkHtml = ma ? '<div class="mt-2"><a href="' + escHtml(ma) + '" target="_blank" rel="noopener noreferrer">Otvoriť v GoogleMaps</a></div>' : '';

        var els2 = ensureMapElements();
        var bubble2 = els2.bubble;

        bubble2.innerHTML = '\n            <div class="bubble-card p-2">\n                <div class="d-flex align-items-start">\n                    ' + imgHtml + '\n                    <div><strong>' + escHtml(na) + '</strong>' + (po ? ('<div class="small text-muted">' + escHtml(po) + '</div>') : '') + '</div>\n                </div>' + (pop ? ('<div class="mt-2 small">' + popHtml + '</div>') : '') + mapLinkHtml + '\n                <div class="bubble-tail" aria-hidden="true"></div>\n            </div>';

        bubble2.style.display = 'block';
        bubble2.setAttribute('aria-hidden','false');

        // hide images that error (attach handler instead of inline onerror)
        var bubbleImgs = bubble2.querySelectorAll('img');
        bubbleImgs.forEach(function(bi){ bi.addEventListener('error', function(){ this.style.display = 'none'; }); });

        // attach handlers: click-to-open external link on bubble card
        var bubbleCard = bubble2.querySelector('.bubble-card');
        if (bubbleCard) {
            bubbleCard.style.cursor = 'pointer';
            bubbleCard.addEventListener('click', function(ev){
                ev.stopPropagation();
                if (ma) {
                    window.open(ma, '_blank', 'noopener');
                } else if (im) {
                    window.open(normalizeImgSrc(im), '_blank', 'noopener');
                }
                hideBubble(bubble2);
            });
        }

        // Positioning needs to happen after images inside the bubble load (they change height)
        function positionBubble() {
            var contRect = mapContainer.getBoundingClientRect ? mapContainer.getBoundingClientRect() : { left: 0, top: 0, width: window.innerWidth, height: window.innerHeight };
            var mRect = markerEl.getBoundingClientRect();

            bubble2.style.left = '0px';
            bubble2.style.top = '0px';
            var preRect = bubble2.getBoundingClientRect();
            var bubW = preRect.width;
            var bubH = preRect.height;

            var relMarkerLeft = mRect.left - contRect.left;
            var relMarkerTop = mRect.top - contRect.top;
            var relMarkerCenterX = relMarkerLeft + (mRect.width / 2);
            var relMarkerCenterY = relMarkerTop + (mRect.height / 2);

            var contWidth = contRect.width || window.innerWidth;
            var contHeight = contRect.height || window.innerHeight;

            var markerHalfH = mRect.height / 2;
            var offset = 8;
            var left = relMarkerCenterX - (bubW / 2);
            var top = relMarkerCenterY - bubH - markerHalfH - offset;
            var placeAbove = true;

            if (top < 8) {
                top = relMarkerCenterY + markerHalfH + offset;
                placeAbove = false;
            }

            if (left < 8) left = 8;
            if (left + bubW > contWidth - 8) left = Math.max(8, contWidth - bubW - 8);
            if (top < 8) top = 8;
            if (top + bubH > contHeight - 8) top = Math.max(8, contHeight - bubH - 8);

            bubble2.style.left = Math.round(left) + 'px';
            bubble2.style.top = Math.round(top) + 'px';

            var tail = bubble2.querySelector('.bubble-tail');
            if (tail) {
                tail.style.left = '';
                tail.style.top = '';
                tail.style.right = '';
                tail.style.bottom = '';

                var markerCenterX_relBubble = relMarkerCenterX - left;
                var tailLeft = Math.round(markerCenterX_relBubble - 8);
                var maxLeft = Math.max(8, bubW - 24);
                var finalTailLeft = Math.max(8, Math.min(tailLeft, maxLeft));

                if (placeAbove) {
                    tail.style.top = Math.round(bubH) + 'px';
                    tail.style.left = finalTailLeft + 'px';
                    tail.style.borderTop = '8px solid transparent';
                    tail.style.borderBottom = '8px solid #fff';
                    tail.style.borderLeft = '8px solid transparent';
                    tail.style.borderRight = '8px solid transparent';
                    tail.style.transform = 'translateY(0)';
                } else {
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

        var imgs = bubble2.querySelectorAll('img');
        if (imgs && imgs.length > 0) {
            var remaining = 0;
            imgs.forEach(function(img){ if (!img.complete) remaining++; });
            if (remaining === 0) {
                positionBubble();
            } else {
                var oneDone = function(){ remaining--; if (remaining === 0) { setTimeout(positionBubble, 20); } };
                imgs.forEach(function(img){ if (!img.complete) { img.addEventListener('load', oneDone); img.addEventListener('error', oneDone); } });
                setTimeout(positionBubble, 600);
            }
        } else {
            positionBubble();
        }
    }

    // delegated click handler for list items and markers
    function delegatedClickHandler(ev) {
        var listItem = ev.target && ev.target.closest ? ev.target.closest('.list-group-item-action') : null;
        if (listItem) {
            // If clicked a real link inside, allow default
            if (ev.target && ev.target.closest && ev.target.closest('a')) return;
            ev.preventDefault();
            var id = listItem.dataset.id || null;
            if (id) {
                var marker = document.querySelector('.overlay-marker[data-id="' + id + '"]');
                if (marker) { showBubbleFor(marker); return; }
            }
            var ma = listItem.dataset.mapa || '';
            var im = listItem.dataset.image || '';
            if (ma) { window.open(ma, '_blank', 'noopener'); }
            else if (im) { window.open(normalizeImgSrc(im), '_blank', 'noopener'); }
            return;
        }

        var markerEl = ev.target && ev.target.closest ? ev.target.closest('.overlay-marker') : null;
        if (markerEl) {
            ev.preventDefault(); ev.stopPropagation();
            showBubbleFor(markerEl);
            return;
        }
    }

    function init() {
        if (window && window.console) console.log('mapa-bubbles: init');
        document.addEventListener('click', delegatedClickHandler);

        // clicking outside hides the bubble
        document.addEventListener('click', function(ev){
            var els = ensureMapElements();
            var bubble = els.bubble;
            var isInside = bubble && (bubble.contains(ev.target) || ev.target.closest && ev.target.closest('.overlay-marker'));
            if (!isInside) hideBubble(bubble);
        });

        // hide on resize/scroll
        window.addEventListener('resize', function(){ var els = ensureMapElements(); hideBubble(els.bubble); });
        var els = ensureMapElements();
        if (els.mapContainer && els.mapContainer.addEventListener) {
            els.mapContainer.addEventListener('scroll', function(){ hideBubble(els.bubble); });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
