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
                <button class="btn btn-warning">Upraviť</button>
                <button class="btn btn-danger">Vymazať</button>
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
                <button class="btn btn-warning">Upraviť</button>
                <button class="btn btn-danger">Vymazať</button>
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
                <button class="btn btn-warning">Upraviť</button>
                <button class="btn btn-danger">Vymazať</button>
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

<script>
// Definícia polí pre každý typ tabuľky
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
</script>
