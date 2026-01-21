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

            <!-- Filters: year and gender -->
            <div class="mb-3 d-flex gap-2 align-items-center" id="bezciFilters">
                <div>
                    <label for="bezciYearFilter" class="form-label mb-0 small">Rok</label>
                    <select id="bezciYearFilter" class="form-select form-select-sm">
                        <option value="">Všetky roky</option>
                        <?php if (!empty($roky)): foreach ($roky as $r): ?>
                            <option value="<?= htmlspecialchars((string)($r['ID_roka'] ?? '')) ?>"><?= htmlspecialchars((string)($r['rok'] ?? ($r['ID_roka'] ?? ''))) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div>
                    <label for="bezciGenderFilter" class="form-label mb-0 small">Pohlavie</label>
                    <select id="bezciGenderFilter" class="form-select form-select-sm">
                        <option value="">Všetky</option>
                        <option value="M">M</option>
                        <option value="Z">Ž</option>
                    </select>
                </div>
                <div class="align-self-end">
                    <button id="bezciFilterReset" type="button" class="btn btn-sm btn-outline-secondary">Reset</button>
                </div>
                <div class="ms-auto small text-muted align-self-end" id="bezciFilterStatus" aria-live="polite"></div>
            </div>

            <!-- Replace server-rendered table with JS-driven container to avoid duplicates -->
            <div class="table-scroll-wrapper">
                <div id="bezciTableContainer"></div>
            </div>
            <div class="mb-4">
                <button class="btn btn-primary" onclick="openAddModal('bezci')">Pridať</button>
                <!-- global edit/delete removed in favor of per-row actions -->
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
                        <!-- actions: set/clear results year + edit/delete -->
                        <td>
                            <div class="d-flex flex-column">
                                <div class="mb-2 d-flex align-items-center">
                                    <?php if (!empty($currentResultsYear) && (string)$currentResultsYear === (string)($rok['ID_roka'] ?? '')): ?>
                                        <span class="badge bg-success me-2">Aktuálne</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearResultsYear()">Zrušiť</button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="setResultsYear(<?= htmlspecialchars((string)($rok['ID_roka'] ?? '')) ?>)">Nastaviť ako výsledkový rok</button>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="btn-group" role="group" aria-label="Row actions">
                                        <button class="btn btn-sm btn-warning" onclick="window.currentEditSection='roky'; document.getElementById('editId').value=<?= htmlspecialchars((string)($rok['ID_roka'] ?? '')) ?>; new bootstrap.Modal(document.getElementById('editIdModal')).show();">Upraviť</button>
                                        <button class="btn btn-sm btn-danger" onclick="window.currentDeleteSection='roky'; document.getElementById('deleteId').value=<?= htmlspecialchars((string)($rok['ID_roka'] ?? '')) ?>; new bootstrap.Modal(document.getElementById('deleteModal')).show();">Vymazať</button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <div class="mb-4">
                <button class="btn btn-primary" onclick="openAddModal('roky')">Pridať</button>
                <!-- global edit/delete removed in favor of per-row actions -->
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
                        // actions header
                        ?>
                        <th>Akcie</th>
                    <?php }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($stanoviska as $stan): ?>
                    <tr>
                        <?php foreach ($colsStan as $col): ?>
                            <td><?= htmlspecialchars((string)($stan[$col] ?? '')) ?></td>
                        <?php endforeach; ?>
                        <td>
                            <button class="btn btn-sm btn-warning me-1" onclick="window.currentEditSection='stanoviska'; document.getElementById('editId').value=<?= htmlspecialchars((string)($stan['ID_stanoviska'] ?? '')) ?>; new bootstrap.Modal(document.getElementById('editIdModal')).show();">Upraviť</button>
                            <button class="btn btn-sm btn-danger" onclick="window.currentDeleteSection='stanoviska'; document.getElementById('deleteId').value=<?= htmlspecialchars((string)($stan['ID_stanoviska'] ?? '')) ?>; new bootstrap.Modal(document.getElementById('deleteModal')).show();">Vymazať</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <div class="mb-4">
                <button class="btn btn-primary" onclick="openAddModal('stanoviska')">Pridať</button>
                <!-- global edit/delete removed in favor of per-row actions -->
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
                    <th>Akcie</th>
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
                            <td>
                                <button class="btn btn-sm btn-warning me-1" onclick="window.currentEditSection='sponsors'; document.getElementById('editId').value=<?= htmlspecialchars((string)($sp['ID_sponsor'] ?? '')) ?>; new bootstrap.Modal(document.getElementById('editIdModal')).show();">Upraviť</button>
                                <button class="btn btn-sm btn-danger" onclick="window.currentDeleteSection='sponsors'; document.getElementById('deleteId').value=<?= htmlspecialchars((string)($sp['ID_sponsor'] ?? '')) ?>; new bootstrap.Modal(document.getElementById('deleteModal')).show();">Vymazať</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8">Žiadni sponzori</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mb-4">
        <button class="btn btn-primary" onclick="openAddModal('sponsors')">Pridať</button>
        <!-- global edit/delete removed in favor of per-row actions -->
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
        <!-- global edit/delete removed in favor of per-row actions -->
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
      <form id="addForm" enctype="multipart/form-data">
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
      <form id="editForm" enctype="multipart/form-data">
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

<!-- Load external admin JS (moved from inline scripts) -->
<script src="<?= isset($link) ? $link->asset('js/admin.admin.js') : '/js/admin.admin.js' ?>"></script>
