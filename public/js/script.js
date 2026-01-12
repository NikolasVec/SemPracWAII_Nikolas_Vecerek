document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-signin');
    if (form) {
        form.addEventListener('submit', function(event) {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirmPassword');

            if (passwordField && confirmPasswordField) {
                const password = passwordField.value.trim();
                const confirmPassword = confirmPasswordField.value.trim();

                if (!password || !confirmPassword || password !== confirmPassword) {
                    event.preventDefault();
                    alert('Heslá sa nezhodujú alebo sú prázdne!');
                    confirmPasswordField.focus();
                }
            }
        });
    }

    // Initialize Bootstrap popovers for elements that request it (person icon for non-admins)
    try {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.forEach(function (popoverTriggerEl) {
            // eslint-disable-next-line no-undef
            // Build content from data attributes (so we don't rely on data-bs-content attribute)
            var name = popoverTriggerEl.getAttribute('data-user-name') || '';
            var email = popoverTriggerEl.getAttribute('data-user-email') || '';
            var km = popoverTriggerEl.getAttribute('data-user-km') || '0';
            var beers = popoverTriggerEl.getAttribute('data-user-beers') || '0';
            var contentHtml = '<div><strong>Meno a priezvisko:</strong> ' + name + '</div>' +
                '<div><strong>Email:</strong> ' + email + '</div>' +
                '<div><strong>Zabehnuté km:</strong> ' + km + '</div>' +
                '<div><strong>Počet vypitých pív:</strong> ' + beers + '</div>';

            // Debug log for initialization
            try { console.debug('Initializing popover for element', popoverTriggerEl, {name: name, email: email, km: km, beers: beers}); } catch (e) {}

            // Quick fix: use click trigger for user-info popovers to ensure visibility on hover issues
            var triggerMode = 'click';
            // If not a user-info popover, fall back to element's attribute
            if (!popoverTriggerEl.hasAttribute('data-user-name')) {
                triggerMode = popoverTriggerEl.getAttribute('data-bs-trigger') || 'hover focus';
            }
            new bootstrap.Popover(popoverTriggerEl, {
                trigger: triggerMode,
                html: true,
                content: function () {
                    return contentHtml;
                },
                container: document.body
            });
            // Ensure click toggles the popover reliably for user-info elements
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
        // If Bootstrap is not available, warn in console so we can debug why popovers don't show
        console.warn('Bootstrap popover init failed', e);
    }
});
