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
// Definícia polí pre každý typ tabuľky (používa sa na generovanie formulárov)
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
        {name: 'ID_roka', label: 'Rok konania', type: 'number', required: true}
    ]
};
let currentSection = null;

// Otvorí modal na pridanie záznamu a vygeneruje formulár podľa sekcie
function openAddModal(section) {
    currentSection = section;
    const fields = formFields[section];
    let html = '';
    fields.forEach(field => {
        html += `<div class="mb-3">`;
        html += `<label class="form-label">${field.label}${field.required ? ' *' : ''}</label>`;
        if (field.type === 'select') {
            html += `<select class="form-select" name="${field.name}" required>`;
            field.options.forEach(opt => {
                html += `<option value="${opt}">${opt}</option>`;
            });
            html += `</select>`;
        } else if (field.type === 'textarea') {
            html += `<textarea class="form-control" name="${field.name}" ${field.required ? 'required' : ''}></textarea>`;
        } else {
            // include step attribute when provided (used for time inputs to allow seconds)
            html += `<input class="form-control" type="${field.type}" name="${field.name}" ${field.required ? 'required' : ''}${field.step ? ' step="' + field.step + '"' : ''}>`;
        }
        html += `</div>`;
    });
    document.getElementById('addFormBody').innerHTML = html;
    var modal = new bootstrap.Modal(document.getElementById('addModal'));
    modal.show();
}

// Handler pre odoslanie formulára na pridanie záznamu (AJAX)
document.getElementById('addForm').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    fetch(`/?c=Admin&a=add&section=${currentSection}`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Chyba: ' + (data.message || 'Neznáma chyba.'));
        }
    })
    .catch(() => alert('Chyba pri komunikácii so serverom.'));
};

// Otvorí modal na vymazanie záznamu podľa sekcie
function openDeleteModal(section) {
    window.currentDeleteSection = section;
    document.getElementById('deleteId').value = '';
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Handler na tlačidlá Vymazať (otvára modal na zadanie ID)
const deleteButtons = document.querySelectorAll('.btn-danger[data-section]');
deleteButtons.forEach((btn) => {
    btn.onclick = function() {
        let section = btn.getAttribute('data-section');
        openDeleteModal(section);
    };
});

// Handler pre odoslanie formulára na vymazanie záznamu (najprv načíta údaje a zobrazí potvrdenie)
document.getElementById('deleteForm').onsubmit = function(e) {
    e.preventDefault();
    const id = document.getElementById('deleteId').value;
    // Najprv načítaj údaje záznamu
    fetch(`/?c=Admin&a=get&section=${window.currentDeleteSection}&id=${id}`)
    .then(res => res.json())
    .then(data => {
        if (data.success && data.item) {
            // Priprav text podľa sekcie
            let info = '';
            if (window.currentDeleteSection === 'bezci') {
                info = `${data.item.meno} ${data.item.priezvisko}`;
            } else if (window.currentDeleteSection === 'roky') {
                info = `rok ${data.item.rok}`;
            } else if (window.currentDeleteSection === 'stanoviska') {
                info = `${data.item.nazov}`;
            }
            if (confirm(`Naozaj chcete vymazať záznam: ${info}?`)) {
                // Skutočné vymazanie
                fetch(`/?c=Admin&a=delete&section=${window.currentDeleteSection}&id=${id}`, {
                    method: 'POST'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Chyba: ' + (data.message || 'Neznáma chyba.'));
                    }
                })
                .catch(() => alert('Chyba pri komunikácii so serverom.'));
            }
        } else {
            alert('Záznam s daným ID neexistuje.');
        }
    })
    .catch(() => alert('Chyba pri načítaní údajov.'));
};

// Handler na tlačidlá Upraviť (otvára modal na zadanie ID pre úpravu)
const editButtons = document.querySelectorAll('.btn-warning[data-section]');
editButtons.forEach((btn) => {
    btn.onclick = function() {
        window.currentEditSection = btn.getAttribute('data-section');
        document.getElementById('editId').value = '';
        var modal = new bootstrap.Modal(document.getElementById('editIdModal'));
        modal.show();
    };
});

// Handler pre odoslanie formulára na zadanie ID pre úpravu (načíta dáta cez AJAX a otvorí editovací modal)
document.getElementById('editIdForm').onsubmit = function(e) {
    e.preventDefault();
    const id = document.getElementById('editId').value;
    fetch(`/?c=Admin&a=get&section=${window.currentEditSection}&id=${id}`)
    .then(res => res.json())
    .then(data => {
        if (data.success && data.item) {
            // Vygeneruj editovací formulár podľa sekcie a predvyplň hodnoty
            const section = window.currentEditSection;
            const fields = formFields[section];
            let html = '';
            fields.forEach(field => {
                html += `<div class="mb-3">`;
                html += `<label class="form-label">${field.label}${field.required ? ' *' : ''}</label>`;
                let value = data.item[field.name] ?? '';
                if (field.type === 'select') {
                    html += `<select class="form-select" name="${field.name}" required>`;
                    field.options.forEach(opt => {
                        html += `<option value="${opt}"${opt===value? ' selected' : ''}>${opt}</option>`;
                    });
                    html += `</select>`;
                } else if (field.type === 'textarea') {
                    html += `<textarea class="form-control" name="${field.name}" ${field.required ? 'required' : ''}>${value}</textarea>`;
                } else {
                    // include step attribute when provided (used for time inputs to allow seconds)
                    html += `<input class="form-control" type="${field.type}" name="${field.name}" value="${value}" ${field.required ? 'required' : ''}${field.step ? ' step="' + field.step + '"' : ''}>`;
                }
                html += `</div>`;
            });
            document.getElementById('editFormBody').innerHTML = html;
            window.currentEditId = id;
            var modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        } else {
            alert('Záznam s daným ID neexistuje.');
        }
    })
    .catch(() => alert('Chyba pri načítaní údajov.'));
};

// Handler pre odoslanie editovacieho formulára (AJAX, uloží zmeny do DB)
document.getElementById('editForm').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('id', window.currentEditId);
    fetch(`/?c=Admin&a=update&section=${window.currentEditSection}`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Chyba: ' + (data.message || 'Neznáma chyba.'));
        }
    })
    .catch(() => alert('Chyba pri ukladaní zmien.'));
}

// add setResultsYear JS functions near the end of the script

function setResultsYear(id) {
    if (!confirm('Nastaviť rok s ID ' + id + ' ako výsledkový rok?')) return;
    fetch(`/?c=Admin&a=setResultsYear`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + encodeURIComponent(id)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Chyba: ' + (data.message || 'Neznáma chyba.'));
        }
    })
    .catch(() => alert('Chyba pri komunikácii so serverom.'));
}

function clearResultsYear() {
    if (!confirm('Naozaj zrušiť vybraný výsledkový rok?')) return;
    fetch(`/?c=Admin&a=setResultsYear`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' // empty to clear
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Chyba: ' + (data.message || 'Neznáma chyba.'));
        }
    })
    .catch(() => alert('Chyba pri komunikácii so serverom.'));
}

// Gallery: open modals and handle create/upload
function openCreateAlbumModal() {
    var modal = new bootstrap.Modal(document.getElementById('createAlbumModal'));
    modal.show();
}

function openUploadModal(albumId) {
    if (albumId) {
        const sel = document.getElementById('uploadAlbumSelect');
        for (let i = 0; i < sel.options.length; i++) {
            if (sel.options[i].value === String(albumId)) {
                sel.selectedIndex = i; break;
            }
        }
    }
    var modal = new bootstrap.Modal(document.getElementById('uploadModal'));
    modal.show();
}

// create album submit
document.getElementById('createAlbumForm').onsubmit = function(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    fetch('/?c=Admin&a=createAlbum', { method: 'POST', body: fd })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Chyba: ' + (data.message || 'Neznáma chyba.'));
        }
    })
    .catch(() => alert('Chyba pri komunikácii so serverom.'));
};

// upload photos submit
document.getElementById('uploadForm').onsubmit = function(e) {
    e.preventDefault();
    // client-side size checks using server-provided limits
    const UPLOAD_MAX_BYTES = <?= (int)($upload_max_bytes ?? 0) ?>;
    const POST_MAX_BYTES = <?= (int)($post_max_bytes ?? 0) ?>;
    const files = document.getElementById('photosInput').files;
    if (!files || files.length === 0) {
        alert('Vyberte aspoň jeden súbor.');
        return;
    }
    let total = 0;
    for (let i = 0; i < files.length; i++) {
        total += files[i].size;
        if (UPLOAD_MAX_BYTES > 0 && files[i].size > UPLOAD_MAX_BYTES) {
            alert('Súbor "' + files[i].name + '" je príliš veľký. Maximálna veľkosť jedného súboru: ' + Math.round(UPLOAD_MAX_BYTES/1024/1024) + ' MB');
            return;
        }
    }
    if (POST_MAX_BYTES > 0 && total > POST_MAX_BYTES) {
        alert('Súhrnná veľkosť súborov je príliš veľká. Maximálny súčet: ' + Math.round(POST_MAX_BYTES/1024/1024) + ' MB');
        return;
    }

    const fd = new FormData(e.target);
    fetch('/?c=Admin&a=uploadPhoto', { method: 'POST', body: fd })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Nahrané ' + (data.files ? data.files.length : 0) + ' súborov.');
            location.reload();
        } else {
            alert('Chyba: ' + (data.message || 'Neznáma chyba.'));
        }
    })
    .catch(() => alert('Chyba pri komunikácii so serverom.'));
};

// base path for gallery assets
const ASSET_GALLERY = '<?= isset($link) ? $link->asset('images/gallery') : '/images/gallery' ?>';

function openPhotosModal(albumId) {
    const body = document.getElementById('photosModalBody');
    body.innerHTML = '<p>Načítavam...</p>';
    var modal = new bootstrap.Modal(document.getElementById('photosModal'));
    modal.show();
    fetch(`/?c=Admin&a=listPhotos&album_id=${encodeURIComponent(albumId)}`)
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            body.innerHTML = '<div class="alert alert-danger">Chyba: ' + (data.message || 'Neznáma chyba') + '</div>';
            return;
        }
        const photos = data.photos || [];
        if (photos.length === 0) {
            body.innerHTML = '<p>Žiadne fotky v albume.</p>';
            return;
        }
        let html = '<div class="d-flex flex-wrap gap-2">';
        photos.forEach(p => {
            const src = ASSET_GALLERY + '/' + (p.album_id || albumId) + '/' + p.filename;
            html += `<div class="card" style="width:140px;">
                        <img src="${src}" class="card-img-top" style="height:100px; object-fit:cover;" />
                        <div class="card-body p-2">
                            <div class="small text-truncate">${(p.original_name || p.filename)}</div>
                            <div class="d-flex mt-2">
                                <a href="${src}" target="_blank" class="btn btn-sm btn-outline-primary me-1">Otvoriť</a>
                                <button class="btn btn-sm btn-danger ms-auto" onclick="deletePhotoConfirm(${parseInt(p.ID_photo)})">Vymazať</button>
                            </div>
                        </div>
                    </div>`;
        });
        html += '</div>';
        body.innerHTML = html;
    })
    .catch(() => {
        body.innerHTML = '<div class="alert alert-danger">Chyba pri načítaní fotiek.</div>';
    });
}

function deletePhotoConfirm(photoId) {
    if (!confirm('Naozaj vymazať túto fotku?')) return;
    fetch(`/?c=Admin&a=delete&section=photos&id=${encodeURIComponent(photoId)}`, { method: 'POST' })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // refresh modal content
            // try to find currently open album id from modal images' src
            const body = document.getElementById('photosModalBody');
            const imgs = body.querySelectorAll('img');
            let albumId = null;
            if (imgs.length) {
                const parts = imgs[0].src.split('/');
                albumId = parts[parts.length-2];
            }
            if (albumId) openPhotosModal(albumId);
            else location.reload();
        } else {
            alert('Chyba: ' + (data.message || 'Neznáma chyba.'));
        }
    })
    .catch(() => alert('Chyba pri komunikácii so serverom.'));
}

function deleteAlbumConfirm(albumId) {
    if (!confirm('Naozaj vymazať celý album a všetky jeho fotky?')) return;
    fetch(`/?c=Admin&a=delete&section=albums&id=${encodeURIComponent(albumId)}`, { method: 'POST' })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Chyba: ' + (data.message || 'Neznáma chyba.'));
        }
    })
    .catch(() => alert('Chyba pri komunikácii so serverom.'));
}
</script>
