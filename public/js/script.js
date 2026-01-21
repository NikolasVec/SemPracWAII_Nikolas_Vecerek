// Po načítaní DOM vykonaj inicializáciu udalostí
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-signin');
    if (form) {
        // Validácia hesiel pri odoslaní formulára
        form.addEventListener('submit', function(event) {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirmPassword');

            if (passwordField && confirmPasswordField) {
                const password = passwordField.value.trim();
                const confirmPassword = confirmPasswordField.value.trim();

                // Skontroluj, či heslá nie sú prázdne a sú zhodné
                if (!password || !confirmPassword || password !== confirmPassword) {
                    event.preventDefault();
                    alert('Heslá sa nezhodujú alebo sú prázdne!');
                    confirmPasswordField.focus();
                }
            }
        });
    }

    // Inicializácia Bootstrap popoverov pre prvky, ktoré to požadujú
    try {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.forEach(function (popoverTriggerEl) {
            // eslint-disable-next-line no-undef
            // Vytvorenie obsahu popoveru z data-atribútov
            var name = popoverTriggerEl.getAttribute('data-user-name') || '';
            var email = popoverTriggerEl.getAttribute('data-user-email') || '';
            var km = popoverTriggerEl.getAttribute('data-user-km') || '0';
            var beers = popoverTriggerEl.getAttribute('data-user-beers') || '0';
            var contentHtml = '<div><strong>Meno a priezvisko:</strong> ' + name + '</div>' +
                '<div><strong>Email:</strong> ' + email + '</div>' +
                '<div><strong>Zabehnuté km:</strong> ' + km + '</div>' +
                '<div><strong>Počet vypitých pív:</strong> ' + beers + '</div>';

            // Ladací výpis pri inicializácii
            try { console.debug('Initializing popover for element', popoverTriggerEl, {name: name, email: email, km: km, beers: beers}); } catch (e) {}

            // Voľba triggeru: pre užívateľské popovery použijeme kliknutie
            var triggerMode = 'click';
            // Ak to nie je user-info popover, použij atribút alebo predvolený hover focus
            if (!popoverTriggerEl.hasAttribute('data-user-name')) {
                triggerMode = popoverTriggerEl.getAttribute('data-bs-trigger') || 'hover focus';
            }
            // Vytvor popover s nastaveným triggerom a obsahom
            new bootstrap.Popover(popoverTriggerEl, {
                trigger: triggerMode,
                html: true,
                content: function () {
                    return contentHtml;
                },
                container: document.body
            });
            // Zabezpeč spoľahlivé prepínanie popoveru klikom pre user-info prvky
            try {
                if (popoverTriggerEl.hasAttribute('data-user-name')) {
                    popoverTriggerEl.addEventListener('click', function (ev) {
                        ev.preventDefault();
                        var instance = bootstrap.Popover.getInstance(popoverTriggerEl);
                        if (!instance) {
                            instance = new bootstrap.Popover(popoverTriggerEl, { html: true, container: document.body, trigger: 'click' });
                        }
                        try { instance.toggle(); } catch (e) { console.warn('Popover toggle failed', e); }
                    });
                }
            } catch (e) { console.warn('Adding click toggle failed', e); }
         });
     } catch (e) {
        // Ak Bootstrap nie je dostupný, vypíš varovanie do konzoly
        console.warn('Bootstrap popover init failed', e);
    }
});
