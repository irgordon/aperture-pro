/* global wp, ApertureProAdmin */

(function () {
    const api = wp.apiFetch;
    const rest = ApertureProAdmin.restUrl;
    const nonce = ApertureProAdmin.nonce;

    const form = document.getElementById('ap-bio-form');
    const loading = document.getElementById('ap-bio-loading');
    const linksContainer = document.getElementById('bio-links-container');
    const saveBtn = document.getElementById('bio-save-btn');
    const tmplLink = document.getElementById('tmpl-bio-link-row');

    if (!form) return;

    // ------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------

    function endpoint(path) {
        return rest + path.replace(/^\//, '');
    }

    function toast(message, type = 'success') {
        const div = document.createElement('div');
        div.className = `ap-toast ap-toast-${type}`;
        div.textContent = message;
        div.style.position = 'fixed';
        div.style.bottom = '20px';
        div.style.right = '20px';
        div.style.padding = '10px 20px';
        div.style.background = type === 'error' ? '#d63638' : '#00a32a';
        div.style.color = '#fff';
        div.style.borderRadius = '4px';
        div.style.zIndex = '9999';
        document.body.appendChild(div);
        setTimeout(() => div.remove(), 3000);
    }

    // ------------------------------------------------------------
    // Logic
    // ------------------------------------------------------------

    function addLinkRow(data = {}) {
        const clone = tmplLink.content.cloneNode(true);
        const row = clone.querySelector('.bio-link-row');

        row.querySelector('.bio-link-label').value = data.label || '';
        row.querySelector('.bio-link-url').value = data.url || '';
        row.querySelector('.bio-link-icon').value = data.icon || '';
        row.querySelector('.bio-link-thumb').value = data.thumbnail || '';

        // Add event listener for remove
        row.querySelector('.bio-remove-link').addEventListener('click', () => {
            row.remove();
        });

        linksContainer.appendChild(row);
    }

    async function loadSettings() {
        try {
            const settings = await api({
                path: 'aperture-pro/v1/bio/settings',
                method: 'GET',
                headers: { 'X-WP-Nonce': nonce },
            });

            // Populate simple fields
            if (settings.profileImage) document.getElementById('bio-profile-image').value = settings.profileImage;
            if (settings.name) document.getElementById('bio-name').value = settings.name;
            if (settings.description) document.getElementById('bio-description').value = settings.description;

            if (settings.donationEnabled) document.getElementById('bio-donation-enabled').checked = true;
            if (settings.donationLink) document.getElementById('bio-donation-link').value = settings.donationLink;

            if (settings.shopEnabled) document.getElementById('bio-shop-enabled').checked = true;
            if (settings.printfulKey) document.getElementById('bio-printful-key').value = settings.printfulKey;

            if (settings.socials) {
                if (settings.socials.facebook) document.getElementById('bio-social-facebook').value = settings.socials.facebook;
                if (settings.socials.instagram) document.getElementById('bio-social-instagram').value = settings.socials.instagram;
                if (settings.socials.youtube) document.getElementById('bio-social-youtube').value = settings.socials.youtube;
                if (settings.socials['500px']) document.getElementById('bio-social-500px').value = settings.socials['500px'];
            }

            // Populate links
            linksContainer.innerHTML = '';
            if (settings.links && Array.isArray(settings.links)) {
                settings.links.forEach(link => addLinkRow(link));
            }

            loading.style.display = 'none';
            form.style.display = 'block';

        } catch (e) {
            console.error(e);
            loading.textContent = 'Error loading settings.';
        }
    }

    async function saveSettings(e) {
        e.preventDefault();
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        // Gather Data
        const data = {
            profileImage: document.getElementById('bio-profile-image').value,
            name: document.getElementById('bio-name').value,
            description: document.getElementById('bio-description').value,
            donationEnabled: document.getElementById('bio-donation-enabled').checked,
            donationLink: document.getElementById('bio-donation-link').value,
            shopEnabled: document.getElementById('bio-shop-enabled').checked,
            printfulKey: document.getElementById('bio-printful-key').value,
            socials: {
                facebook: document.getElementById('bio-social-facebook').value,
                instagram: document.getElementById('bio-social-instagram').value,
                youtube: document.getElementById('bio-social-youtube').value,
                '500px': document.getElementById('bio-social-500px').value,
            },
            links: []
        };

        const linkRows = linksContainer.querySelectorAll('.bio-link-row');
        linkRows.forEach(row => {
            data.links.push({
                label: row.querySelector('.bio-link-label').value,
                url: row.querySelector('.bio-link-url').value,
                icon: row.querySelector('.bio-link-icon').value,
                thumbnail: row.querySelector('.bio-link-thumb').value,
            });
        });

        try {
            await api({
                path: 'aperture-pro/v1/bio/settings',
                method: 'POST',
                headers: { 'X-WP-Nonce': nonce },
                data: data
            });
            toast('Settings Saved');
        } catch (e) {
            console.error(e);
            toast('Error saving settings', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Changes';
        }
    }

    // ------------------------------------------------------------
    // Init
    // ------------------------------------------------------------

    document.getElementById('add-bio-link').addEventListener('click', () => addLinkRow());
    form.addEventListener('submit', saveSettings);

    loadSettings();

})();
