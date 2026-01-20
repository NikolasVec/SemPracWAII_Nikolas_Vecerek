<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Auth\AppUser $user */
?>
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <!-- Admin sections navigation -->
            <nav class="admin-nav mb-3">
              <div class="btn-group" role="group" aria-label="Admin sections">
                <button type="button" class="btn btn-outline-primary admin-nav-btn" data-section="bezci" onclick="showSection('bezci')">Bežci</button>
                <button type="button" class="btn btn-outline-primary admin-nav-btn" data-section="roky" onclick="showSection('roky')">Roky</button>
                <button type="button" class="btn btn-outline-primary admin-nav-btn" data-section="stanoviska" onclick="showSection('stanoviska')">Stanovištia</button>
                <button type="button" class="btn btn-outline-primary admin-nav-btn" data-section="gallery" onclick="showSection('gallery')">Galéria</button>
                <button type="button" class="btn btn-outline-primary admin-nav-btn" data-section="sponsors" onclick="showSection('sponsors')">Sponzori</button>
                <button type="button" class="btn btn-outline-primary admin-nav-btn" data-section="pouzivatelia" onclick="showSection('pouzivatelia')">Používatelia</button>
              </div>
            </nav>

            <div class="admin-section" data-section="bezci">
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
            </div> <!-- end section bezci -->

            <div class="admin-section" data-section="roky">
            <h3>Roky konania</h3>
            <div class="mb-4">
                <!-- manual crediting UI removed; crediting is automatic based on DB records -->
            </div>
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
            </div> <!-- end section roky -->

            <div class="admin-section" data-section="stanoviska">
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
            </div> <!-- end section stanoviska -->

            <div class="admin-section" data-section="gallery">
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
            </div> <!-- end section gallery -->
        </div>
    </div>
</div>

<!-- Sponsors management -->
<div class="admin-section" data-section="sponsors">
<div class="container-fluid mt-4">
    <h3>Sponzori (footer)</h3>
    <div class="table-scroll-wrapper">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Názov</th>
                    <th>Kontakt Email</th>
                    <th>Kontakt Telefón</th>
                    <th>Logo</th>
                    <th>URL</th>
                    <th>Vytvorené</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($sponsors)): ?>
                    <?php foreach ($sponsors as $sp): ?>
                        <tr>
                            <td><?= htmlspecialchars((string)($sp['ID_sponsor'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($sp['name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($sp['contact_email'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($sp['contact_phone'] ?? '')) ?></td>
                            <td>
                                <?php if (!empty($sp['logo'])): ?>
                                    <img src="<?= isset($link) ? $link->asset('images/sponsors/' . $sp['logo']) : '/images/sponsors/' . $sp['logo'] ?>" alt="logo" style="height:48px; object-fit:contain;" />
                                <?php else: ?>
                                    <span class="text-muted">Žiadne</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars((string)($sp['url'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($sp['created_at'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7">Žiadni sponzori</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mb-4">
        <button class="btn btn-primary" onclick="openAddModal('sponsors')">Pridať</button>
        <button class="btn btn-warning" data-section="sponsors">Upraviť</button>
        <button class="btn btn-danger" data-section="sponsors">Vymazať</button>
    </div>
</div>
</div> <!-- end section sponsors -->

<!-- Users management -->
<div class="admin-section" data-section="pouzivatelia">
<div class="container-fluid mt-4">
    <h3>Používatelia</h3>
    <div class="table-scroll-wrapper">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <?php
                    if (!empty($pouzivatelia)) {
                        $colsUsers = array_filter(array_keys($pouzivatelia[0]), 'is_string');
                        foreach($colsUsers as $col): ?>
                            <th><?= htmlspecialchars((string)$col) ?></th>
                        <?php endforeach;
                        // actions header
                        ?>
                        <th>Akcie</th>
                    <?php } else { ?>
                        <th>ID</th>
                        <th>Meno</th>
                        <th>Priezvisko</th>
                        <th>Email</th>
                        <th>Admin</th>
                        <th>Akcie</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pouzivatelia)): ?>
                    <?php foreach ($pouzivatelia as $usr): ?>
                        <tr>
                            <?php foreach ($colsUsers as $col): ?>
                                <td><?= htmlspecialchars((string)($usr[$col] ?? '')) ?></td>
                            <?php endforeach; ?>
                            <td>
                                <button class="btn btn-sm btn-warning me-1" onclick="window.currentEditSection='pouzivatelia'; document.getElementById('editId').value=<?= htmlspecialchars((string)($usr['ID_pouzivatela'] ?? '')) ?>; new bootstrap.Modal(document.getElementById('editIdModal')).show();">Upraviť</button>
                                <button class="btn btn-sm btn-danger" onclick="window.currentDeleteSection='pouzivatelia'; document.getElementById('deleteId').value=<?= htmlspecialchars((string)($usr['ID_pouzivatela'] ?? '')) ?>; new bootstrap.Modal(document.getElementById('deleteModal')).show();">Vymazať</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">Žiadni používatelia</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mb-4">
        <button class="btn btn-primary" onclick="openAddModal('pouzivatelia')">Pridať</button>
        <button class="btn btn-warning" data-section="pouzivatelia">Upraviť</button>
        <button class="btn btn-danger" data-section="pouzivatelia">Vymazať</button>
    </div>
</div>
</div> <!-- end section pouzivatelia -->

<!-- Modal pre pridanie záznamu -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addModalLabel">Pridať záznam</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addForm">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)($csrfToken ?? '')) ?>">
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
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)($csrfToken ?? '')) ?>">
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
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)($csrfToken ?? '')) ?>">
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
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)($csrfToken ?? '')) ?>">
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
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)($csrfToken ?? '')) ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="createAlbumName">Názov *</label>
            <input id="createAlbumName" class="form-control" name="name" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="createAlbumDescription">Popis</label>
            <textarea id="createAlbumDescription" class="form-control" name="description"></textarea>
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
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)($csrfToken ?? '')) ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="uploadAlbumSelect">Album</label>
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
            <label class="form-label" for="photosInput">Fotky (môžete vybrať viac súborov)</label>
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

<style>
/* simple admin section show/hide */
.admin-section{ display:none; }
.admin-section.active{ display:block; }
.admin-nav-btn.active{ box-shadow: inset 0 -3px 0 rgba(0,123,255,0.25); }
</style>

<!-- expose CSRF token for JS -->
<script>
    window.CSRF_TOKEN = '<?= htmlspecialchars((string)($csrfToken ?? '')) ?>';
</script>

<script>
// small navigation helper for admin sections
(function(){
    const sections = ['bezci','roky','stanoviska','gallery','sponsors','pouzivatelia'];
    function showSection(section){
        sections.forEach(function(s){
            const el = document.querySelector('.admin-section[data-section="'+s+'"]');
            if(el) el.classList.toggle('active', s === section);
            const btn = document.querySelector('.admin-nav-btn[data-section="'+s+'"]');
            if(btn) btn.classList.toggle('active', s === section);
        });
        try{ history.replaceState(null, '', '#'+section); }catch(e){}
        window.currentAdminSection = section;
    }
    window.showSection = showSection;
    window.adminNext = function(){
        let idx = sections.indexOf(window.currentAdminSection || sections[0]);
        idx = (idx + 1) % sections.length; showSection(sections[idx]);
    };
    window.adminPrev = function(){
        let idx = sections.indexOf(window.currentAdminSection || sections[0]);
        idx = (idx - 1 + sections.length) % sections.length; showSection(sections[idx]);
    };
    document.addEventListener('DOMContentLoaded', function(){
        const hash = (location.hash || '').replace('#','');
        const start = sections.includes(hash) ? hash : 'bezci';
        showSection(start);
        document.querySelectorAll('.admin-prev').forEach(function(b){ b.addEventListener('click', window.adminPrev); });
        document.querySelectorAll('.admin-next').forEach(function(b){ b.addEventListener('click', window.adminNext); });
    });
})();
</script>

<script>
// Admin UI script (cleaned, formatted)
// NOTE: this block was updated to include csrfFetch() wrapper which adds X-CSRF-Token header for POST requests
(function(){
    // helper that injects CSRF header for POST requests
    function csrfFetch(url, options) {
        options = options || {};
        var method = (options.method || 'GET').toUpperCase();
        options.headers = options.headers || {};
        if (method === 'POST') {
            // do not overwrite existing header if set
            if (!options.headers['X-CSRF-Token'] && !options.headers['x-csrf-token']) {
                options.headers['X-CSRF-Token'] = window.CSRF_TOKEN || '';
            }
        }
        return fetch(url, options);
    }

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
            {name: 'datum_konania', label: 'Dátum konania', type: 'date', required: true},
            {name: 'dlzka_behu', label: 'Dĺžka behu (km)', type: 'number', required: false, step: '0.01'},
            {name: 'pocet_stanovisk', label: 'Počet stanovísk', type: 'number', required: false, step: '1'}
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
        ],
        sponsors: [
            {name: 'name', label: 'Názov', type: 'text', required: true},
            {name: 'contact_email', label: 'Kontakt email', type: 'email', required: false},
            {name: 'contact_phone', label: 'Kontakt telefón', type: 'text', required: false},
            {name: 'url', label: 'URL (web)', type: 'text', required: false},
            {name: 'logo', label: 'Logo (obrázok)', type: 'file', required: false}
        ],
        pouzivatelia: [
            {name: 'meno', label: 'Meno', type: 'text', required: true},
            {name: 'priezvisko', label: 'Priezvisko', type: 'text', required: true},
            {name: 'email', label: 'Email', type: 'email', required: true},
            {name: 'heslo', label: 'Heslo (zanechajte prázdne ak nemeníte)', type: 'password', required: false},
            {name: 'admin', label: 'Admin', type: 'select', options: ['0','1'], required: false}
        ]
    };

    var currentSection = null;
    var currentEditId = null;

    function openAddModal(section) {
        currentSection = section;
        const fields = formFields[section] || [];
        var html = '';
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

        var addBody = document.getElementById('addFormBody');
        if (addBody) addBody.innerHTML = html;
        var modalEl = document.getElementById('addModal');
        if (modalEl) new bootstrap.Modal(modalEl).show();
    }

    // Add form submit
    (function() {
        const addForm = document.getElementById('addForm');
        if (!addForm) return;
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(addForm);
            csrfFetch('/?c=Admin&a=add&section=' + encodeURIComponent(currentSection), { method: 'POST', body: fd })
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
                        else if (window.currentDeleteSection === 'sponsors') info = data.item.name;
                        else if (window.currentDeleteSection === 'pouzivatelia') info = (data.item.meno || '') + ' ' + (data.item.priezvisko || '');

                        if (confirm('Naozaj chcete vymazať záznam: ' + info + '?')) {
                            csrfFetch('/?c=Admin&a=delete&section=' + encodeURIComponent(window.currentDeleteSection) + '&id=' + encodeURIComponent(id), { method: 'POST' })
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
                                field.options.forEach(function(opt) { html += '<option value="' + opt + '"' + (opt === value ? ' selected' : '') + '>' + opt + '</option>'; });
                                html += '</select>';
                            } else if (field.type === 'textarea') {
                                html += '<textarea class="form-control" name="' + field.name + '"' + (field.required ? ' required' : '') + '>' + value + '</textarea>';
                            } else if (field.type === 'file') {
                                html += '<input class="form-control" type="file" name="' + field.name + '"' + (field.required ? ' required' : '') + '>';
                                if (data.item[field.name]) {
                                    html += '<div class="mt-1 small text-muted">Aktuálny súbor: ' + (data.item[field.name] || data.item['logo'] || '') + '</div>';
                                } else if (data.item.logo && field.name === 'logo') {
                                    html += '<div class="mt-1 small text-muted">Aktuálny súbor: ' + (data.item.logo) + '</div>';
                                }
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
            csrfFetch('/?c=Admin&a=update&section=' + encodeURIComponent(window.currentEditSection), { method: 'POST', body: fd })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data && data.success) location.reload();
                    else alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba.'));
                }).catch(function() { alert('Chyba pri ukladaní zmien.'); });

