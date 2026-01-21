// Combined admin scripts moved from App/Views/Admin/index.view.php
// Contains: admin section navigation + admin UI (AJAX forms, modals, bezci filtering)
(function(){
    // --- Navigation helper for admin sections ---
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

    // --- Admin UI script ---
    // helper that injects CSRF header for POST requests
    function csrfFetch(url, options) {
        options = options || {};
        var method = (options.method || 'GET').toUpperCase();
        options.headers = options.headers || {};
        // ensure we send credentials (session cookie) by default
        if (!options.credentials) options.credentials = 'same-origin';
        if (method === 'POST') {
            if (!options.headers['X-CSRF-Token'] && !options.headers['x-csrf-token']) {
                options.headers['X-CSRF-Token'] = window.CSRF_TOKEN || '';
            }
        }
        return fetch(url, options);
    }

    // Add missing handler for "Nastaviť ako výsledkový rok" button.
    // Called from inline onclick in App/Views/Admin/index.view.php
    window.setResultsYear = function(id) {
        // confirm action with the admin
        if (!confirm('Naozaj chcete nastaviť tento rok ako výsledkový?')) return;
        var fd = new FormData();
        // ensure id is string (empty string clears the setting)
        fd.append('id', typeof id === 'undefined' || id === null ? '' : id);
        csrfFetch('/?c=Admin&a=setResultsYear', { method: 'POST', body: fd })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success) {
                    // reload to reflect change in UI
                    location.reload();
                } else {
                    alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba.'));
                }
            }).catch(function() {
                alert('Chyba pri komunikácii so serverom.');
            });
    };

    // Handler for clearing the results year (called by "Zrušiť" button)
    window.clearResultsYear = function() {
        if (!confirm('Naozaj chcete zrušiť nastavenie výsledkového roku?')) return;
        var fd = new FormData(); fd.append('id', '');
        csrfFetch('/?c=Admin&a=setResultsYear', { method: 'POST', body: fd })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success) {
                    location.reload();
                } else {
                    alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba.'));
                }
            }).catch(function() { alert('Chyba pri komunikácii so serverom.'); });
    };

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
            {name: 'admin', label: 'Admin', type: 'select', options: ['0','1'], required: false},
            {name: 'pohlavie', label: 'Pohlavie', type: 'select', options: ['M','Z'], required: false},
            {name: 'datum_narodenia', label: 'Dátum narodenia', type: 'date', required: false},
            {name: 'zabehnute_kilometre', label: 'Zabehnuté kilometre', type: 'number', required: false, step: '0.01'},
            {name: 'vypite_piva', label: 'Vypité piva', type: 'number', required: false, step: '1'}
        ]
    };

    var currentSection = null;

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
    window.openAddModal = openAddModal;

    // Add form submit
    (function() {
        const addForm = document.getElementById('addForm');
        if (!addForm) return;
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(addForm);
            let noticeEl = document.getElementById('addModalNotice');
            if (!noticeEl) {
                noticeEl = document.createElement('div');
                noticeEl.id = 'addModalNotice';
                const body = document.querySelector('#addModal .modal-body');
                if (body) body.parentNode.insertBefore(noticeEl, body);
            }
            noticeEl.innerHTML = '';

            csrfFetch('/?c=Admin&a=add&section=' + encodeURIComponent(currentSection), { method: 'POST', body: fd })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data && data.success) {
                        location.reload();
                        return;
                    }

                    if (data && data.userNotRegistered) {
                        noticeEl.innerHTML = '<div class="alert alert-warning">Zadaný email nie je zaregistrovaný ako používateľ.<div class="mt-2"><button type="button" class="btn btn-sm btn-primary" id="createUserBtn">Vytvoriť používateľa</button> <button type="button" class="btn btn-sm btn-secondary" id="changeEmailBtn">Zmeniť email</button></div></div>';
                        const createBtn = document.getElementById('createUserBtn');
                        const changeBtn = document.getElementById('changeEmailBtn');
                        if (createBtn) {
                            createBtn.onclick = function() {
                                openAddModal('pouzivatelia');
                                setTimeout(function() {
                                    const emailInput = document.querySelector('#addForm [name="email"]');
                                    if (emailInput && fd.get('email')) emailInput.value = fd.get('email');
                                }, 100);
                            };
                        }
                        if (changeBtn) {
                            changeBtn.onclick = function() {
                                const emailInput = document.querySelector('#addForm [name="email"]');
                                if (emailInput) { emailInput.value = ''; emailInput.focus(); }
                                noticeEl.innerHTML = '';
                            };
                        }
                        return;
                    }

                    const msg = data && data.message ? data.message : 'Neznáma chyba.';
                    if (noticeEl) {
                        noticeEl.innerHTML = '<div class="alert alert-danger">Chyba: ' + msg + '</div>';
                    } else {
                        alert('Chyba: ' + msg);
                    }
                })
                .catch(function() {
                    alert('Chyba pri komunikácii so serverom.');
                });
        });
    })();

    function openDeleteModal(section) {
        window.currentDeleteSection = section;
        const el = document.getElementById('deleteId'); if (el) el.value = '';
        const modalEl = document.getElementById('deleteModal'); if (modalEl) new bootstrap.Modal(modalEl).show();
    }

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
        });
    })();

    // --- Bezci AJAX filtering (fetch listBezci and re-render table) ---
    (function(){
        const yearSel = document.getElementById('bezciYearFilter');
        const genderSel = document.getElementById('bezciGenderFilter');
        const resetBtn = document.getElementById('bezciFilterReset');
        const statusEl = document.getElementById('bezciFilterStatus');
        const container = document.getElementById('bezciTableContainer');

        function getOrCreateTable() {
            if (!container) return null;
            document.querySelectorAll('#bezciTable').forEach(function(el){
                if (!container.contains(el)) {
                    el.parentNode && el.parentNode.removeChild(el);
                }
            });

            let table = container.querySelector('table#bezciTable');
            if (!table) {
                table = document.createElement('table');
                table.className = 'table table-bordered table-striped';
                table.id = 'bezciTable';
                const thead = document.createElement('thead');
                const tbody = document.createElement('tbody');
                table.appendChild(thead);
                table.appendChild(tbody);
                container.innerHTML = '';
                container.appendChild(table);
            }
            return table;
        }

        function buildHeaderFromItem(item) {
            const table = getOrCreateTable(); if (!table) return [];
            const thead = table.querySelector('thead');
            const tr = document.createElement('tr');
            const keys = Object.keys(item || {}).filter(function(k){ return !/^\\d+$/.test(k); });
            keys.forEach(function(k){
                const th = document.createElement('th'); th.textContent = k; tr.appendChild(th);
            });
            const thActions = document.createElement('th'); thActions.textContent = 'Akcie'; tr.appendChild(thActions);
            thead.innerHTML = ''; thead.appendChild(tr);
            return keys;
        }

        function renderRows(items, keys){
            const table = getOrCreateTable(); if (!table) return;
            const tbody = table.querySelector('tbody');
            tbody.innerHTML = '';
            if (!items || items.length === 0) {
                const tr = document.createElement('tr');
                const td = document.createElement('td'); td.colSpan = (keys ? keys.length + 1 : 1); td.textContent = 'Žiadni bežci'; tr.appendChild(td); tbody.appendChild(tr); return;
            }
            items.forEach(function(it){
                const tr = document.createElement('tr');
                (keys || Object.keys(it)).forEach(function(k){
                    const td = document.createElement('td'); td.textContent = typeof it[k] !== 'undefined' && it[k] !== null ? it[k] : ''; tr.appendChild(td);
                });
                const tdAct = document.createElement('td');
                const idVal = (typeof it.ID_bezca !== 'undefined' ? it.ID_bezca : (it.id || ''));
                tdAct.innerHTML = '<button class="btn btn-sm btn-warning me-1" onclick="window.currentEditSection=\'bezci\'; document.getElementById(\'editId\').value='+ (idVal)+'; new bootstrap.Modal(document.getElementById(\'editIdModal\')).show();">Upraviť</button>' +
                                 '<button class="btn btn-sm btn-danger" onclick="window.currentDeleteSection=\'bezci\'; document.getElementById(\'deleteId\').value='+ (idVal)+'; new bootstrap.Modal(document.getElementById(\'deleteModal\')).show();">Vymazať</button>';
                tr.appendChild(tdAct);
                tbody.appendChild(tr);
            });
        }

        function fetchAndRender() {
            const year = yearSel ? yearSel.value : '';
            const gender = genderSel ? genderSel.value : '';
            statusEl && (statusEl.textContent = 'Načítavam...');
            const params = new URLSearchParams();
            if (year) params.set('ID_roka', year);
            if (gender) params.set('pohlavie', gender);
            const url = '/?c=Admin&a=listBezci' + (params.toString() ? '&' + params.toString() : '');
            fetch(url, { credentials: 'same-origin' })
                .then(function(res){ return res.json(); })
                .then(function(data){
                    statusEl && (statusEl.textContent = 'Zobrazených: ' + (data.items ? data.items.length : 0));
                    if (!data || !data.success) { renderRows([], null); return; }
                    const items = data.items || [];
                    let keys = null;
                    if (items.length > 0) {
                        const first = items[0];
                        keys = Object.keys(first).filter(function(k){ return typeof k === 'string' && !/^\\d+$/.test(k); });
                        buildHeaderFromItem(first);
                    } else {
                        const table = getOrCreateTable();
                        const existingKeys = table ? Array.from(table.querySelectorAll('thead th')).map(function(th){ return th.textContent; }).filter(function(t){ return t !== 'Akcie'; }) : [];
                        keys = existingKeys;
                    }
                    renderRows(items, keys);
                }).catch(function(){ statusEl && (statusEl.textContent = 'Chyba pri načítaní'); renderRows([], null); });
        }

        if (yearSel) yearSel.addEventListener('change', fetchAndRender);
        if (genderSel) genderSel.addEventListener('change', fetchAndRender);
        if (resetBtn) resetBtn.addEventListener('click', function(){ if (yearSel) yearSel.value=''; if (genderSel) genderSel.value=''; fetchAndRender(); });

        document.addEventListener('DOMContentLoaded', function(){ fetchAndRender(); });
    })();

    // Gallery / Albums helpers (create, upload, list photos, delete)
    (function(){
        // Open create album modal
        window.openCreateAlbumModal = function() {
            const form = document.getElementById('createAlbumForm');
            if (!form) return;
            // reset form fields
            form.reset();
            const modalEl = document.getElementById('createAlbumModal');
            if (modalEl) new bootstrap.Modal(modalEl).show();
        };

        // Handle create album submit
        (function(){
            const form = document.getElementById('createAlbumForm');
            if (!form) return;
            form.addEventListener('submit', function(e){
                e.preventDefault();
                const fd = new FormData(form);
                csrfFetch('/?c=Admin&a=createAlbum', { method: 'POST', body: fd })
                    .then(function(res){ return res.json(); })
                    .then(function(data){
                        if (data && data.success) {
                            // reload to show newly created album in table
                            location.reload();
                        } else {
                            alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba při vytváraní albumu.'));
                        }
                    }).catch(function(){ alert('Chyba pri komunikácii so serverom.'); });
            });
        })();

        // Open upload modal and preselect album
        window.openUploadModal = function(albumId) {
            const sel = document.getElementById('uploadAlbumSelect');
            if (sel) {
                // try to select option if present
                const opt = sel.querySelector('option[value="'+albumId+'"]');
                if (opt) sel.value = albumId;
            }
            const form = document.getElementById('uploadForm'); if (form) form.reset();
            const modalEl = document.getElementById('uploadModal'); if (modalEl) new bootstrap.Modal(modalEl).show();
        };

        // Handle upload form submit
        (function(){
            const form = document.getElementById('uploadForm');
            if (!form) return;
            form.addEventListener('submit', function(e){
                e.preventDefault();
                const fd = new FormData(form);
                // ensure album_id present
                const aid = fd.get('album_id');
                if (!aid) { alert('Vyberte album.'); return; }
                // Use fetch with credentials/same-origin; csrfFetch will add header for POST
                csrfFetch('/?c=Admin&a=uploadPhoto', { method: 'POST', body: fd })
                    .then(function(res){ return res.json(); })
                    .then(function(data){
                        if (data && data.success) {
                            // close modal and optionally refresh photos modal if open
                            const modal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
                            if (modal) modal.hide();
                            // show photos modal for the same album to reflect new uploads
                            if (aid) window.openPhotosModal(aid);
                        } else {
                            alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba pri nahrávaní.'));
                        }
                    }).catch(function(){ alert('Chyba pri komunikácii so serverom.'); });
            });
        })();

        // Open photos modal for album and list photos
        window.openPhotosModal = function(albumId) {
            if (!albumId) return;
            const body = document.getElementById('photosModalBody');
            if (body) body.innerHTML = '<div class="text-muted">Načítavam...</div>';
            fetch('/?c=Admin&a=listPhotos&album_id=' + encodeURIComponent(albumId), { credentials: 'same-origin' })
                .then(function(res){ return res.json(); })
                .then(function(data){
                    if (!data || !data.success) {
                        if (body) body.innerHTML = '<div class="text-danger">Chyba pri načítaní fotiek.</div>';
                        return;
                    }
                    const photos = data.photos || [];
                    let html = '<div class="row">';
                    if (photos.length === 0) {
                        html += '<div class="col-12">Žiadne fotky</div>';
                    } else {
                        photos.forEach(function(p){
                            const thumbUrl = (typeof p.filename !== 'undefined') ? ('/images/gallery/' + encodeURIComponent(p.album_id) + '/' + encodeURIComponent(p.filename)) : '';
                            html += '<div class="col-6 col-md-3 mb-3">';
                            html += '<div class="card">';
                            if (thumbUrl) html += '<img src="'+thumbUrl+'" class="card-img-top" style="object-fit:cover; height:150px;" alt="'+(p.original_name||'')+'">';
                            html += '<div class="card-body p-2 small text-truncate">' + (p.original_name || '') + '</div>';
                            html += '<div class="card-footer p-2 text-center">';
                            html += '<button class="btn btn-sm btn-danger me-1" data-photo-id="'+(p.ID_photo||'')+'" data-album-id="'+(p.album_id||'')+'">Vymazať</button>';
                            html += '</div></div></div>';
                        });
                    }
                    html += '</div>';
                    if (body) body.innerHTML = html;
                    // attach delete handlers
                    const modalEl = document.getElementById('photosModal');
                    if (modalEl) new bootstrap.Modal(modalEl).show();
                    body.querySelectorAll('button[data-photo-id]').forEach(function(btn){
                        btn.addEventListener('click', function(){
                            const pid = btn.getAttribute('data-photo-id');
                            const aid = btn.getAttribute('data-album-id');
                            if (!pid) return;
                            if (!confirm('Naozaj chcete vymazať túto fotku?')) return;
                            csrfFetch('/?c=Admin&a=delete&section=photos&id=' + encodeURIComponent(pid), { method: 'POST' })
                                .then(function(res){ return res.json(); })
                                .then(function(resp){
                                    if (resp && resp.success) {
                                        // refresh photos list
                                        window.openPhotosModal(aid);
                                    } else {
                                        alert('Chyba: ' + (resp && resp.message ? resp.message : 'Neznáma chyba pri mazaní.'));
                                    }
                                }).catch(function(){ alert('Chyba pri komunikácii so serverom.'); });
                        });
                    });
                }).catch(function(){ if (body) body.innerHTML = '<div class="text-danger">Chyba pri načítaní fotiek.</div>'; });
        };

        // Confirm and delete album
        window.deleteAlbumConfirm = function(albumId) {
            if (!albumId) return;
            if (!confirm('Naozaj chcete vymazať album a všetky jeho fotky?')) return;
            csrfFetch('/?c=Admin&a=delete&section=albums&id=' + encodeURIComponent(albumId), { method: 'POST' })
                .then(function(res){ return res.json(); })
                .then(function(data){
                    if (data && data.success) location.reload();
                    else alert('Chyba: ' + (data && data.message ? data.message : 'Neznáma chyba pri mazaní albumu.'));
                }).catch(function(){ alert('Chyba pri komunikácii so serverom.'); });
        };

    })();

    // Initialize navigation when DOM is ready
    document.addEventListener('DOMContentLoaded', function(){
        const hash = (location.hash || '').replace('#','');
        const start = sections.includes(hash) ? hash : 'bezci';
        showSection(start);
        document.querySelectorAll('.admin-prev').forEach(function(b){ b.addEventListener('click', window.adminPrev); });
        document.querySelectorAll('.admin-next').forEach(function(b){ b.addEventListener('click', window.adminNext); });
    });

})();
