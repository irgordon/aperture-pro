/* global wp, ApertureProClient */

(function () {
    const api = wp.apiFetch;
    const rest = ApertureProClient.restUrl;

    // ------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------

    function endpoint(path) {
        return rest + path.replace(/^\//, '');
    }

    function qs(selector, root = document) {
        return root.querySelector(selector);
    }

    function qsa(selector, root = document) {
        return Array.from(root.querySelectorAll(selector));
    }

    function toast(message, type = 'success') {
        const div = document.createElement('div');
        div.className = `ap-toast ap-toast-${type}`;
        div.textContent = message;
        document.body.appendChild(div);
        setTimeout(() => div.remove(), 3000);
    }

    function setLoading(el, isLoading) {
        if (!el) return;
        if (isLoading) {
            el.dataset.loading = '1';
            el.disabled = true;
        } else {
            delete el.dataset.loading;
            el.disabled = false;
        }
    }

    function handleError(error) {
        console.error('Aperture Pro Client Error:', error);
        toast(error.message || 'Something went wrong.', 'error');
    }

    // ------------------------------------------------------------
    // REST wrappers
    // ------------------------------------------------------------

    function post(path, data = {}) {
        return api({
            path: path,
            method: 'POST',
            data,
        });
    }

    function get(path) {
        return api({
            path: path,
            method: 'GET',
        });
    }

    // ------------------------------------------------------------
    // Proofing Gallery Hydration
    // ------------------------------------------------------------

    async function loadClientGallery(container) {
        const projectId = container.dataset.projectId;
        if (!projectId) return;

        container.innerHTML = `<div class="ap-skeleton-grid"></div>`;

        try {
            const data = await get(`projects/${projectId}/status`);

            const images = data?.proofing?.images || [];
            if (!images.length) {
                container.innerHTML = `<p>No images found.</p>`;
                return;
            }

            container.innerHTML = `
                <div class="ap-grid">
                    ${images
                        .map(
                            (img) => `
                        <div class="ap-img-card" data-image-id="${img.id}">
                            <img src="${img.url}" alt="">
                            <div class="ap-img-actions">
                                <button data-status="approved">Approve</button>
                                <button data-status="rejected">Reject</button>
                                <button data-status="revision">Needs Revision</button>
                            </div>
                        </div>
                    `
                        )
                        .join('')}
                </div>
            `;
        } catch (e) {
            handleError(e);
            container.innerHTML = `<p class="ap-error">Failed to load gallery.</p>`;
        }
    }

    // ------------------------------------------------------------
    // Proofing Actions
    // ------------------------------------------------------------

    async function updateProofingImage(projectId, imageId, status) {
        try {
            await post(`projects/${projectId}/proofing/update`, {
                image_id: imageId,
                status,
            });
            toast('Updated');
        } catch (e) {
            handleError(e);
        }
    }

    async function submitProofing(projectId, btn) {
        setLoading(btn, true);
        try {
            await post(`projects/${projectId}/proofing/submit`);
            toast('Selections submitted');
            location.reload();
        } catch (e) {
            handleError(e);
        } finally {
            setLoading(btn, false);
        }
    }

    // ------------------------------------------------------------
    // Event Delegation
    // ------------------------------------------------------------

    // Image card actions
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.ap-img-actions button');
        if (!btn) return;

        const card = btn.closest('.ap-img-card');
        const container = btn.closest('#ap-client-proofing-gallery');
        if (!card || !container) return;

        const projectId = container.dataset.projectId;
        const imageId = card.dataset.imageId;
        const status = btn.dataset.status;

        updateProofingImage(projectId, imageId, status);
    });

    // Submit proofing
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-ap-client-action="submit-proofing"]');
        if (!btn) return;

        const projectId = btn.dataset.projectId;
        submitProofing(projectId, btn);
    });

    // ------------------------------------------------------------
    // Auto‑hydrate gallery
    // ------------------------------------------------------------

    document.addEventListener('DOMContentLoaded', () => {
        const gallery = qs('#ap-client-proofing-gallery');
        if (gallery) {
            loadClientGallery(gallery);
        }
    });
})();
