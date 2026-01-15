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
                            $nazov = $s['nazov'] ?? ($s->nazov ?? 'Bez názvu');
                            $poloha = $s['poloha'] ?? ($s->poloha ?? '');
                            $popis = $s['popis'] ?? ($s->popis ?? '');
                            $mapa = $s['mapa_odkaz'] ?? ($s->mapa_odkaz ?? '');
                        ?>
                        <a href="#" class="list-group-item list-group-item-action"
                           data-nazov="<?= htmlspecialchars($nazov, ENT_QUOTES) ?>"
                           data-poloha="<?= htmlspecialchars($poloha, ENT_QUOTES) ?>"
                           data-popis="<?= htmlspecialchars($popis, ENT_QUOTES) ?>"
                           data-mapa="<?= htmlspecialchars($mapa, ENT_QUOTES) ?>">
                            <strong><?= htmlspecialchars($nazov) ?></strong>
                            <?php if ($poloha): ?>
                                <div class="small text-muted"><?= htmlspecialchars($poloha) ?></div>
                            <?php endif; ?>
                            <?php if ($popis): ?>
                                <div class="mt-1"><?= nl2br(htmlspecialchars($popis)) ?></div>
                            <?php endif; ?>
                            <?php if ($mapa): ?>
                                <div class="small text-break text-primary mt-1">Odkaz: <?= htmlspecialchars($mapa) ?></div>
                            <?php endif; ?>
                        </a>
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
                    <img src="/images/mapa_Martin.png" alt="Mapa Martin" class="img-fluid w-100" style="max-height:600px; object-fit:contain; display:block;">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DEBUG: show count of stanoviska and first item (remove this when fixed) -->
<?php $count = is_array($stanoviska) ? count($stanoviska) : 0; ?>
<div class="container mt-2">
    <div class="alert alert-info">Počet stanovísk: <strong><?= $count ?></strong></div>
    <?php if ($count > 0): ?>
        <pre style="max-height:200px; overflow:auto; background:#f7f7f7; padding:8px; border:1px solid #e1e1e1;"><?php echo htmlspecialchars(json_encode($stanoviska[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES); ?></pre>
    <?php endif; ?>
</div>

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

    document.querySelectorAll('.list-group-item-action').forEach(function(el){
        el.addEventListener('click', function(ev){
            ev.preventDefault();
            const na = el.dataset.nazov || '';
            const po = el.dataset.poloha || '';
            const pop = el.dataset.popis || '';
            const ma = el.dataset.mapa || '';

            document.getElementById('stanoviskoModalLabel').textContent = na || 'Detaily stanoviska';
            document.getElementById('modal-poloha').textContent = po;
            document.getElementById('modal-popis').innerHTML = pop ? pop.replace(/\n/g, '<br/>') : '';
            const mapaEl = document.getElementById('modal-mapa');
            if (ma) {
                mapaEl.innerHTML = '<a href="' + ma + '" target="_blank" rel="noopener noreferrer">Otvoriť v mape</a>';
            } else {
                mapaEl.innerHTML = '';
            }

            bsModal.show();
        });
    });
})();
</script>
