<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Auth\AppUser $user */
?>
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div>
                <br><br><br>
                Welcome, <strong><?= $user->getName() ?></strong>!<br><br>
                This part of the application is accessible only after logging in.
            </div>
            <hr>
            <h3>Bežci</h3>
            <div class="table-scroll-wrapper">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <?php
                    if (!empty($bezci)) {
                        $cols = array_filter(array_keys($bezci[0]), 'is_string');
                        foreach($cols as $col): ?>
                            <th><?= htmlspecialchars((string)$col) ?></th>
                        <?php endforeach;
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($bezci as $bezc): ?>
                    <tr>
                        <?php foreach ($cols as $col): ?>
                            <td><?= htmlspecialchars((string)($bezc[$col] ?? '')) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <div class="mb-4">
                <button class="btn btn-primary" onclick="openAddModal('bezci')">Pridať</button>
                <button class="btn btn-warning" data-section="bezci">Upraviť</button>
                <button class="btn btn-danger" data-section="bezci">Vymazať</button>
            </div>
            <h3>Roky konania</h3>
            <div class="table-scroll-wrapper">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <?php
                    if (!empty($roky)) {
                        $colsRoky = array_filter(array_keys($roky[0]), 'is_string');
                        foreach($colsRoky as $col): ?>
                            <th><?= htmlspecialchars((string)$col) ?></th>
                        <?php endforeach;
                        // add actions header
                        ?>
                        <th>Akcie</th>
                    <?php }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($roky as $rok): ?>
                    <tr>
                        <?php foreach ($colsRoky as $col): ?>
                            <td><?= htmlspecialchars((string)($rok[$col] ?? '')) ?></td>
                        <?php endforeach; ?>
                        <!-- actions: set/clear results year -->
                        <td>
                            <?php if (!empty($currentResultsYear) && (string)$currentResultsYear === (string)($rok['ID_roka'] ?? '')): ?>
                                <span class="badge bg-success">Aktuálne</span>
                                <button type="button" class="btn btn-sm btn-secondary ms-2" onclick="clearResultsYear()">Zrušiť</button>
                            <?php else: ?>
                                <button type="button" class="btn btn-sm btn-primary" onclick="setResultsYear(<?= htmlspecialchars((string)($rok['ID_roka'] ?? '')) ?>)">Nastaviť ako výsledkový rok</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <div class="mb-4">
                <button class="btn btn-primary" onclick="openAddModal('roky')">Pridať</button>
                <button class="btn btn-warning" data-section="roky">Upraviť</button>
                <button class="btn btn-danger" data-section="roky">Vymazať</button>
            </div>
            <h3>Stanoviská</h3>
            <div class="table-scroll-wrapper">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <?php
                    if (!empty($stanoviska)) {
                        $colsStan = array_filter(array_keys($stanoviska[0]), 'is_string');
                        foreach($colsStan as $col): ?>
                            <th><?= htmlspecialchars((string)$col) ?></th>
                        <?php endforeach;
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($stanoviska as $stan): ?>
                    <tr>
                        <?php foreach ($colsStan as $col): ?>
                            <td><?= htmlspecialchars((string)($stan[$col] ?? '')) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <div class="mb-4">
                <button class="btn btn-primary" onclick="openAddModal('stanoviska')">Pridať</button>
                <button class="btn btn-warning" data-section="stanoviska">Upraviť</button>
                <button class="btn btn-danger" data-section="stanoviska">Vymazať</button>
            </div>

            <h3>Galéria (albumy)</h3>
            <div class="mb-3">
                <button class="btn btn-success" onclick="openCreateAlbumModal()">Vytvoriť album</button>
            </div>
            <div class="table-scroll-wrapper">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Názov</th>
                            <th>Popis</th>
                            <th>Vytvorené</th>
                            <th>Akcie</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($albums)): ?>
                            <?php foreach ($albums as $alb): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string)($alb['ID_album'] ?? $alb['album']['ID_album'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($alb['name'] ?? $alb['album']['name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($alb['description'] ?? $alb['album']['description'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($alb['created_at'] ?? $alb['album']['created_at'] ?? '')) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary me-1" onclick="openPhotosModal(<?= htmlspecialchars((string)($alb['ID_album'] ?? $alb['album']['ID_album'] ?? '')) ?>)">Zobraziť fotky</button>
                                        <button class="btn btn-sm btn-primary me-1" onclick="openUploadModal(<?= htmlspecialchars((string)($alb['ID_album'] ?? $alb['album']['ID_album'] ?? '')) ?>)">Nahráť fotky</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteAlbumConfirm(<?= htmlspecialchars((string)($alb['ID_album'] ?? $alb['album']['ID_album'] ?? '')) ?>)">Vymazať album</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5">Žiadne albumy</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal pre pridanie záznamu -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addModalLabel">Pridať záznam</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addForm">
        <div class="modal-body" id="addFormBody">
          <!-- Dynamicky generované polia -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
          <button type="submit" class="btn btn-primary">Uložiť</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal pre vymazanie záznamu -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Vymazať záznam</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="deleteForm">
        <div class="modal-body">
          <label for="deleteId" class="form-label">Zadajte ID záznamu na vymazanie:</label>
          <input type="number" class="form-control" id="deleteId" name="deleteId" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
          <button type="submit" class="btn btn-danger">Vymazať</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal pre zadanie ID na úpravu -->
<div class="modal fade" id="editIdModal" tabindex="-1" aria-labelledby="editIdModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editIdModalLabel">Upraviť záznam</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editIdForm">
        <div class="modal-body">
          <label for="editId" class="form-label">Zadajte ID záznamu na úpravu:</label>
          <input type="number" class="form-control" id="editId" name="editId" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
          <button type="submit" class="btn btn-warning">Pokračovať</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal pre editáciu záznamu -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Upraviť záznam</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editForm">
        <div class="modal-body" id="editFormBody">
          <!-- Dynamicky generované polia -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
          <button type="submit" class="btn btn-warning">Uložiť zmeny</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Create album modal -->
<div class="modal fade" id="createAlbumModal" tabindex="-1" aria-labelledby="createAlbumLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createAlbumLabel">Vytvoriť album</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createAlbumForm">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Názov *</label>
            <input class="form-control" name="name" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Popis</label>
            <textarea class="form-control" name="description"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
          <button type="submit" class="btn btn-success">Vytvoriť</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Upload photos modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uploadLabel">Nahrať fotky</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="uploadForm" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Album</label>
            <select id="uploadAlbumSelect" name="album_id" class="form-select" required>
                <?php if (!empty($albums)): ?>
                    <?php foreach ($albums as $alb): ?>
                        <?php $aid = $alb['ID_album'] ?? $alb['album']['ID_album'] ?? null; $aname = $alb['name'] ?? $alb['album']['name'] ?? ''; ?>
                        <?php if ($aid): ?><option value="<?= htmlspecialchars((string)$aid) ?>"><?= htmlspecialchars((string)$aname) ?></option><?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Fotky (môžete vybrať viac súborov)</label>
            <input type="file" name="photos[]" id="photosInput" accept="image/*" multiple class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
          <button type="submit" class="btn btn-primary">Nahrať</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Photos modal (view + delete individual photos) -->
<div class="modal fade" id="photosModal" tabindex="-1" aria-labelledby="photosModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="photosModalLabel">Fotky v albume</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="photosModalBody">
        <!-- Filled dynamically -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
      </div>
    </div>
  </div>
</div>

<script>
// Admin UI script (cleaned, formatted)
const formFields = {
    bezci: [
        {name: 'meno', label: 'Meno', type: 'text', required: true},
        {name: 'priezvisko', label: 'Priezvisko', type: 'text', required: true},
        {name: 'email', label: 'Email', type: 'email', required: true},
        {name: 'pohlavie', label: 'Pohlavie', type: 'select', options: ['M', 'Ž'], required: true},
        {name: 'cas_dobehnutia', label: 'Čas dobehnutia', type: 'time', required: false, step: 1},
        {name: 'ID_roka', label: 'Rok konania', type: 'number', required: true}
    ],
    roky: [
        {name: 'rok', label: 'Rok', type: 'number', required: true},
        {name: 'datum_konania', label: 'Dátum konania', type: 'date', required: true}
    ],
    stanoviska: [
        {name: 'nazov', label: 'Názov', type: 'text', required: true},
        {name: 'poloha', label: 'Poloha', type: 'text', required: false},
        {name: 'popis', label: 'Popis', type: 'textarea', required: false},
        {name: 'mapa_odkaz', label: 'Odkaz na mapu', type: 'text', required: false},
        {name: 'obrazok_odkaz', label: 'Odkaz na obrázok', type: 'text', required: false},
        {name: 'x_pos', label: 'X (0..1) - pozícia na mape', type: 'number', required: false, step: '0.000001'},
        {name: 'y_pos', label: 'Y (0..1) - pozícia na mape', type: 'number', required: false, step: '0.000001'},
        {name: 'ID_roka', label: 'Rok konania', type: 'number', required: true}
    ]
};

let currentSection = null;
let currentEditId = null;

function openAddModal(section) {
    currentSection = section;
    const fields = formFields[section] || [];
    let html = '';
    fields.forEach(function(field) {
        html += '<div class="mb-3">';
        html += '<label class="form-label">' + field.label + (field.required ? ' *' : '') + '</label>';
        if (field.type === 'select') {
            html += '<select class="form-select" name="' + field.name + '"' + (field.required ? ' required' : '') + '>';
            field.options.forEach(function(opt) { html += '<option value="' + opt + '">' + opt + '</option>'; });
            html += '</select>';
        } else if (field.type === 'textarea') {
            html += '<textarea class="form-control" name="' + field.name + '"' + (field.required ? ' required' : '') + '></textarea>';
        } else {
            html += '<input class="form-control" type="' + field.type + '" name="' + field.name + '"' + (field.required ? ' required' : '') + (field.step ? ' step="' + field.step + '"' : '') + '>';
        }
        html += '</div>';
    });

    const addBody = document.getElementById('addFormBody');
    if (addBody) addBody.innerHTML = html;
    const modalEl = document.getElementById('addModal');
    if (modalEl) new bootstrap.Modal(modalEl).show();
}

// Add form submit
(function() {
    const addForm = document.getElementById('addForm');
    if (!addForm) return;
    addForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(addForm);
        fetch('/?c=Admin&a=add&section=' + encodeURIComponent(currentSection), { method: 'POST', body: fd })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success) {
                    location.reload();
                } else {
                    alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba.'));
                }
            })
            .catch(function() { alert('Chyba pri komunikácii so serverom.'); });
    });
})();

function openDeleteModal(section) {
    window.currentDeleteSection = section;
    const el = document.getElementById('deleteId'); if (el) el.value = '';
    const modalEl = document.getElementById('deleteModal'); if (modalEl) new bootstrap.Modal(modalEl).show();
}

// Attach delete buttons
(function() {
    const buttons = document.querySelectorAll('.btn-danger[data-section]');
    if (!buttons) return;
    buttons.forEach(function(btn) {
        btn.onclick = function() { openDeleteModal(btn.getAttribute('data-section')); };
    });
})();

// Delete form
(function() {
    const delForm = document.getElementById('deleteForm');
    if (!delForm) return;
    delForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('deleteId').value;
        fetch('/?c=Admin&a=get&section=' + encodeURIComponent(window.currentDeleteSection) + '&id=' + encodeURIComponent(id))
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.item) {
                    var info = '';
                    if (window.currentDeleteSection === 'bezci') info = data.item.meno + ' ' + data.item.priezvisko;
                    else if (window.currentDeleteSection === 'roky') info = 'rok ' + data.item.rok;
                    else if (window.currentDeleteSection === 'stanoviska') info = data.item.nazov;

                    if (confirm('Naozaj chcete vymazať záznam: ' + info + '?')) {
                        fetch('/?c=Admin&a=delete&section=' + encodeURIComponent(window.currentDeleteSection) + '&id=' + encodeURIComponent(id), { method: 'POST' })
                            .then(function(res) { return res.json(); })
                            .then(function(resp) {
                                if (resp && resp.success) location.reload();
                                else alert('Chyba: ' + (resp && resp.message ? resp.message : 'Neznáma chyba.'));
                            }).catch(function() { alert('Chyba pri komunikácii so serverom.'); });
                    }
                } else {
                    alert('Záznam s daným ID neexistuje.');
                }
            }).catch(function() { alert('Chyba pri načítaní údajov.'); });
    });
})();

// Edit buttons
(function() {
    const editButtons = document.querySelectorAll('.btn-warning[data-section]');
    if (!editButtons) return;
    editButtons.forEach(function(btn) {
        btn.onclick = function() {
            window.currentEditSection = btn.getAttribute('data-section');
            const el = document.getElementById('editId'); if (el) el.value = '';
            const modalEl = document.getElementById('editIdModal'); if (modalEl) new bootstrap.Modal(modalEl).show();
        };
    });
})();

// Edit ID form
(function() {
    const form = document.getElementById('editIdForm');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('editId').value;
        fetch('/?c=Admin&a=get&section=' + encodeURIComponent(window.currentEditSection) + '&id=' + encodeURIComponent(id))
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.item) {
                    const section = window.currentEditSection;
                    const fields = formFields[section] || [];
                    let html = '';
                    fields.forEach(function(field) {
                        const value = data.item[field.name] || '';
                        html += '<div class="mb-3">';
                        html += '<label class="form-label">' + field.label + (field.required ? ' *' : '') + '</label>';
                        if (field.type === 'select') {
                            html += '<select class="form-select" name="' + field.name + '"' + (field.required ? ' required' : '') + '>';
                            field.options.forEach(function(opt) { html += '<option value="' + opt + '"' + (opt == value ? ' selected' : '') + '>' + opt + '</option>'; });
                            html += '</select>';
                        } else if (field.type === 'textarea') {
                            html += '<textarea class="form-control" name="' + field.name + '"' + (field.required ? ' required' : '') + '>' + value + '</textarea>';
                        } else {
                            html += '<input class="form-control" type="' + field.type + '" name="' + field.name + '" value="' + value + '"' + (field.required ? ' required' : '') + (field.step ? ' step="' + field.step + '"' : '') + '>';
                        }
                        html += '</div>';
                    });
                    const editBody = document.getElementById('editFormBody'); if (editBody) editBody.innerHTML = html;
                    window.currentEditId = id;
                    const modalEl = document.getElementById('editModal'); if (modalEl) new bootstrap.Modal(modalEl).show();
                } else {
                    alert('Záznam s daným ID neexistuje.');
                }
            }).catch(function() { alert('Chyba pri načítaní údajov.'); });
    });
})();

// Edit submit
(function() {
    const editForm = document.getElementById('editForm');
    if (!editForm) return;
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(editForm);
        fd.append('id', window.currentEditId);
        fetch('/?c=Admin&a=update&section=' + encodeURIComponent(window.currentEditSection), { method: 'POST', body: fd })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success) location.reload();
                else alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba.'));
            }).catch(function() { alert('Chyba pri ukladaní zmien.'); });
    });
})();

// set/clear results year
function setResultsYear(id) {
    if (!confirm('Nastaviť rok s ID ' + id + ' ako výsledkový rok?')) return;
    fetch('/?c=Admin&a=setResultsYear', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + encodeURIComponent(id)
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data && data.success) location.reload();
        else alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba.'));
    })
    .catch(function() { alert('Chyba pri komunikácii so serverom.'); });
}

function clearResultsYear() {
    if (!confirm('Naozaj zrušiť vybraný výsledkový rok?')) return;
    fetch('/?c=Admin&a=setResultsYear', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id='
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data && data.success) location.reload();
        else alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba.'));
    })
    .catch(function() { alert('Chyba pri komunikácii so serverom.'); });
}

// Gallery helpers and uploads
function openCreateAlbumModal() { const m = document.getElementById('createAlbumModal'); if (m) new bootstrap.Modal(m).show(); }
function openUploadModal(albumId) { const sel = document.getElementById('uploadAlbumSelect'); if (sel && albumId) { for (let i=0;i<sel.options.length;i++){ if (sel.options[i].value===String(albumId)){ sel.selectedIndex=i; break; } } } const m = document.getElementById('uploadModal'); if (m) new bootstrap.Modal(m).show(); }

(function attachCreateAlbum(){ const f = document.getElementById('createAlbumForm'); if (!f) return; f.addEventListener('submit', function(e){ e.preventDefault(); const fd = new FormData(f); fetch('/?c=Admin&a=createAlbum', { method: 'POST', body: fd }).then(function(res){ return res.json(); }).then(function(data){ if (data && data.success) location.reload(); else alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba.')); }).catch(function(){ alert('Chyba pri komunikácii so serverom.'); }); }); })();

(function attachUploadForm(){ const f = document.getElementById('uploadForm'); if (!f) return; f.addEventListener('submit', function(e){ e.preventDefault(); const UPLOAD_MAX_BYTES = <?= (int)($upload_max_bytes ?? 0) ?>; const POST_MAX_BYTES = <?= (int)($post_max_bytes ?? 0) ?>; const input = document.getElementById('photosInput'); const files = input ? input.files : null; if (!files || files.length === 0) { alert('Vyberte aspoň jeden súbor.'); return; } let total = 0; for (let i=0;i<files.length;i++){ total += files[i].size; if (UPLOAD_MAX_BYTES > 0 && files[i].size > UPLOAD_MAX_BYTES){ alert('Súbor "' + files[i].name + '" je príliš veľký.'); return; } } if (POST_MAX_BYTES > 0 && total > POST_MAX_BYTES) { alert('Súhrnná veľkosť súborov je príliš veľká.'); return; } const fd = new FormData(f); fetch('/?c=Admin&a=uploadPhoto', { method: 'POST', body: fd }).then(function(res){ return res.json(); }).then(function(data){ if (data && data.success){ alert('Nahrané ' + (data.files ? data.files.length : 0) + ' súborov.'); location.reload(); } else alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba.')); }).catch(function(){ alert('Chyba pri komunikácii so serverom.'); }); }); })();

const ASSET_GALLERY = '<?= isset($link) ? $link->asset('images/gallery') : '/images/gallery' ?>';
function openPhotosModal(albumId){ const body = document.getElementById('photosModalBody'); if (!body) return; body.innerHTML = '<p>Načítavam...</p>'; const modalEl = document.getElementById('photosModal'); if (modalEl) new bootstrap.Modal(modalEl).show(); fetch('/?c=Admin&a=listPhotos&album_id=' + encodeURIComponent(albumId)).then(function(res){ return res.json(); }).then(function(data){ if (!data || !data.success){ body.innerHTML = '<div class="alert alert-danger">Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba') + '</div>'; return; } const photos = data.photos || []; if (photos.length === 0){ body.innerHTML = '<p>Žiadne fotky v albume.</p>'; return; } let html = '<div class="d-flex flex-wrap gap-2">'; photos.forEach(function(p){ const src = ASSET_GALLERY + '/' + (p.album_id || albumId) + '/' + p.filename; html += '<div class="card" style="width:140px;"><img src="' + src + '" class="card-img-top" alt="' + (p.original_name || p.filename) + '" style="height:100px; object-fit:cover;" /><div class="card-body p-2"><div class="small text-truncate">' + (p.original_name || p.filename) + '</div><div class="d-flex mt-2"><a href="' + src + '" target="_blank" class="btn btn-sm btn-outline-primary me-1">Otvoriť</a><button class="btn btn-sm btn-danger ms-auto" onclick="deletePhotoConfirm(' + parseInt(p.ID_photo) + ')">Vymazať</button></div></div></div>'; }); html += '</div>'; body.innerHTML = html; }).catch(function(){ body.innerHTML = '<div class="alert alert-danger">Chyba pri načítaní fotiek.</div>'; }); }

function deletePhotoConfirm(photoId){ if (!confirm('Naozaj vymazať túto fotku?')) return; fetch('/?c=Admin&a=delete&section=photos&id=' + encodeURIComponent(photoId), { method: 'POST' }).then(function(res){ return res.json(); }).then(function(data){ if (data && data.success){ const body = document.getElementById('photosModalBody'); const imgs = body ? body.querySelectorAll('img') : []; let albumId = null; if (imgs && imgs.length){ const parts = imgs[0].src.split('/'); albumId = parts[parts.length-2]; } if (albumId) openPhotosModal(albumId); else location.reload(); } else alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba.')); }).catch(function(){ alert('Chyba pri komunikácii so serverom.'); }); }

function deleteAlbumConfirm(albumId){ if (!confirm('Naozaj vymazať celý album a všetky jeho fotky?')) return; fetch('/?c=Admin&a=delete&section=albums&id=' + encodeURIComponent(albumId), { method: 'POST' }).then(function(res){ return res.json(); }).then(function(data){ if (data && data.success) location.reload(); else alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba.')); }).catch(function(){ alert('Chyba pri komunikácii so serverom.'); }); }

// --- Admin map picker: when add/edit modal contains x_pos/y_pos fields, show a small map picker image that
// lets admin click to select relative coordinates (0..1). The picker will fill inputs and show a small marker.
(function(){
    // create picker HTML
    function createPickerHtml() {
        var html = '';
        html += '<div class="admin-map-picker mt-3">';
        html += '<div class="mb-2"><small class="text-muted">Vyberte pozíciu na mape (kliknutím) alebo zadajte čísla (0..1).</small></div>';
        html += '<div style="position:relative; display:inline-block; max-width:100%;">';
        html += '<img id="adminMapImg" src="/images/mapa_MartinNEW.png" alt="mapa" style="max-width:100%; height:auto; display:block; border:1px solid #ddd;" />';
        html += '<div id="adminMapMarker" style="position:absolute;width:14px;height:14px;border-radius:7px;background:rgba(220,53,69,0.9);border:2px solid white;transform:translate(-50%,-50%);display:none;pointer-events:none;"></div>';
        html += '</div>';
        html += '<div class="mt-2"><button type="button" id="clearMapPos" class="btn btn-sm btn-outline-secondary">Vymazať pozíciu</button></div>';
        html += '</div>';
        return html;
     }

    // Install picker into modal when it opens
    document.addEventListener('shown.bs.modal', function(ev){
        try {
            // target addModal or editModal
            var modal = ev.target;
            if (!modal) return;
            // add modal body where dynamic fields are rendered
            var body = modal.querySelector('#addFormBody') || modal.querySelector('#editFormBody');
            if (!body) return;
            // only for stanoviska
            var xInput = body.querySelector('input[name="x_pos"]');
            var yInput = body.querySelector('input[name="y_pos"]');
            if (!xInput || !yInput) return;

            // if picker already exists, do nothing
            if (body.querySelector('.admin-map-picker')) return;

            // inject picker
            var wrapper = document.createElement('div');
            wrapper.innerHTML = createPickerHtml();
            body.appendChild(wrapper);

            var img = body.querySelector('#adminMapImg');
            var marker = body.querySelector('#adminMapMarker');
            var clearBtn = body.querySelector('#clearMapPos');

            function setMarkerRel(relX, relY) {
                if (!img) return;
                var rect = img.getBoundingClientRect();
                // compute pixel position
                var px = rect.left + relX * rect.width;
                var py = rect.top + relY * rect.height;
                // position marker relative to image container (which is positioned)
                var containerRect = img.parentElement.getBoundingClientRect();
                var left = relX * img.parentElement.offsetWidth;
                var top = relY * img.parentElement.offsetHeight;
                marker.style.left = (relX * 100) + '%';
                marker.style.top = (relY * 100) + '%';
                marker.style.display = 'block';
            }

            img.addEventListener('click', function(e){
                var rect = img.getBoundingClientRect();
                var relX = (e.clientX - rect.left) / rect.width;
                var relY = (e.clientY - rect.top) / rect.height;
                relX = Math.min(Math.max(relX,0),1);
                relY = Math.min(Math.max(relY,0),1);
                // set inputs with 6 decimal places
                xInput.value = relX.toFixed(6);
                yInput.value = relY.toFixed(6);
                setMarkerRel(relX, relY);
            });

            // if inputs already have values, show marker
            if (xInput.value !== '' && yInput.value !== '') {
                var vx = parseFloat(xInput.value);
                var vy = parseFloat(yInput.value);
                if (isFinite(vx) && isFinite(vy)) setMarkerRel(vx, vy);
            }

            clearBtn.addEventListener('click', function(){
                xInput.value = '';
                yInput.value = '';
                marker.style.display = 'none';
            });

            // when inputs change manually, update marker
            xInput.addEventListener('input', function(){
                var vx = parseFloat(xInput.value);
                var vy = parseFloat(yInput.value);
                if (isFinite(vx) && isFinite(vy)) setMarkerRel(vx, vy);
                else marker.style.display = 'none';
            });
            yInput.addEventListener('input', function(){
                var vx = parseFloat(xInput.value);
                var vy = parseFloat(yInput.value);
                if (isFinite(vx) && isFinite(vy)) setMarkerRel(vx, vy);
                else marker.style.display = 'none';
            });

            // handle modal hide: remove picker to avoid duplicates next time
            modal.addEventListener('hidden.bs.modal', function(){
                var p = body.querySelector('.admin-map-picker'); if (p) p.remove();
            }, { once: true });

        } catch (err) {
            console.error('Map picker init error', err);
        }
    });
})();
</script>
