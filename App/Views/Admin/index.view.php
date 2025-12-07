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
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach;
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($bezci as $bezc): ?>
                    <tr>
                        <?php foreach ($cols as $col): ?>
                            <td><?= htmlspecialchars($bezc[$col]) ?></td>
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
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach;
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($roky as $rok): ?>
                    <tr>
                        <?php foreach ($colsRoky as $col): ?>
                            <td><?= htmlspecialchars($rok[$col]) ?></td>
                        <?php endforeach; ?>
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
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach;
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($stanoviska as $stan): ?>
                    <tr>
                        <?php foreach ($colsStan as $col): ?>
                            <td><?= htmlspecialchars($stan[$col]) ?></td>
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

<script>
// Definícia polí pre každý typ tabuľky (používa sa na generovanie formulárov)
const formFields = {
    bezci: [
        {name: 'meno', label: 'Meno', type: 'text', required: true},
        {name: 'priezvisko', label: 'Priezvisko', type: 'text', required: true},
        {name: 'email', label: 'Email', type: 'email', required: true},
        {name: 'pohlavie', label: 'Pohlavie', type: 'select', options: ['M', 'Ž'], required: true},
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
            html += `<input class="form-control" type="${field.type}" name="${field.name}" ${field.required ? 'required' : ''}>`;
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
        let section = btn.getAttribute('data-section');
        window.currentEditSection = section;
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
                        html += `<option value="${opt}"${opt==value?' selected':''}>${opt}</option>`;
                    });
                    html += `</select>`;
                } else if (field.type === 'textarea') {
                    html += `<textarea class="form-control" name="${field.name}" ${field.required ? 'required' : ''}>${value}</textarea>`;
                } else {
                    html += `<input class="form-control" type="${field.type}" name="${field.name}" value="${value}" ${field.required ? 'required' : ''}>`;
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
};
</script>
