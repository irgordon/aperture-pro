/* global wp, ApertureProAdmin */

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('ap-wizard-content');
    const stepperContainer = document.getElementById('ap-wizard-stepper');
    if (!container) return;

    const restUrl = ApertureProAdmin.restUrl;
    const nonce = ApertureProAdmin.nonce;
    const initialSettings = ApertureProAdmin.settings || {};

    const state = {
        step: 0,
        data: {
            brandName: initialSettings.brandName || 'Aperture Pro',
            brandLogo: initialSettings.brandLogo || '',
            storageAdapter: initialSettings.storageAdapter || 'local',
            ikPublicKey: initialSettings.ikPublicKey || '',
            ikPrivateKey: initialSettings.ikPrivateKey || '',
            ikUrlEndpoint: initialSettings.ikUrlEndpoint || '',
            seoTitle: initialSettings.seoTitle || '%project% | %brand%',
            seoDesc: initialSettings.seoDesc || 'View the proofing gallery for %project%.',
            imgQuality: initialSettings.imgQuality || 85,
            imgMaxWidth: initialSettings.imgMaxWidth || 1920
        }
    };

    const steps = [
        { title: 'Branding', render: renderBranding },
        { title: 'Storage', render: renderStorage },
        { title: 'SEO', render: renderSEO },
        { title: 'Optimization', render: renderOptimization },
        { title: 'Finish', render: renderFinish }
    ];

    function init() {
        renderStepper();
        renderStep();
    }

    function renderStepper() {
        let html = '<div class="ap-stepper">';
        steps.forEach((s, idx) => {
            let cls = 'ap-step-item';
            if (idx === state.step) cls += ' active';
            if (idx < state.step) cls += ' completed';
            html += `<div class="${cls}" title="${s.title}">${idx + 1}</div>`;
        });
        html += '</div>';
        stepperContainer.innerHTML = html;
    }

    function renderStep() {
        renderStepper();
        const step = steps[state.step];
        container.innerHTML = step.render();
        bindEvents();
    }

    // --- Render Functions ---

    function renderBranding() {
        return `
            <h2>Branding</h2>
            <div class="ap-form-group">
                <label>Brand Name</label>
                <input type="text" id="brandName" value="${esc(state.data.brandName)}">
                <p class="ap-form-text">The name displayed on client galleries.</p>
            </div>
            <div class="ap-form-group">
                <label>Logo URL</label>
                <input type="url" id="brandLogo" value="${esc(state.data.brandLogo)}">
                <p class="ap-form-text">Full URL to your logo image.</p>
            </div>
            ${renderActions()}
        `;
    }

    function renderStorage() {
        const isIk = state.data.storageAdapter === 'imagekit';
        return `
            <h2>Storage Adapter</h2>
            <div class="ap-form-group">
                <label>Select Storage</label>
                <select id="storageAdapter">
                    <option value="local" ${state.data.storageAdapter === 'local' ? 'selected' : ''}>Local Storage</option>
                    <option value="imagekit" ${isIk ? 'selected' : ''}>ImageKit.io</option>
                </select>
            </div>

            <div id="imagekit-fields" style="display: ${isIk ? 'block' : 'none'}; border-left: 3px solid #2271b1; padding-left: 15px; margin-top: 20px;">
                <h3>ImageKit Configuration</h3>
                <div class="ap-form-group">
                    <label>Public Key</label>
                    <input type="text" id="ikPublicKey" value="${esc(state.data.ikPublicKey)}">
                </div>
                <div class="ap-form-group">
                    <label>Private Key</label>
                    <input type="text" id="ikPrivateKey" value="${esc(state.data.ikPrivateKey)}">
                </div>
                <div class="ap-form-group">
                    <label>URL Endpoint</label>
                    <input type="url" id="ikUrlEndpoint" value="${esc(state.data.ikUrlEndpoint)}">
                </div>
            </div>
            ${renderActions()}
        `;
    }

    function renderSEO() {
        return `
            <h2>SEO Settings</h2>
            <div class="ap-form-group">
                <label>Page Title Template</label>
                <input type="text" id="seoTitle" value="${esc(state.data.seoTitle)}">
                <p class="ap-form-text">Variables: %project%, %brand%</p>
            </div>
            <div class="ap-form-group">
                <label>Meta Description Template</label>
                <input type="text" id="seoDesc" value="${esc(state.data.seoDesc)}">
            </div>
            ${renderActions()}
        `;
    }

    function renderOptimization() {
        return `
            <h2>Image Optimization</h2>
            <div class="ap-form-group">
                <label>Image Quality (1-100)</label>
                <input type="number" id="imgQuality" value="${esc(state.data.imgQuality)}" min="1" max="100">
            </div>
            <div class="ap-form-group">
                <label>Max Width (px)</label>
                <input type="number" id="imgMaxWidth" value="${esc(state.data.imgMaxWidth)}">
            </div>
            ${renderActions(true)}
        `;
    }

    function renderFinish() {
        return `
            <div class="ap-congrats">
                <div class="ap-success-icon">✓</div>
                <h2>Setup Complete!</h2>
                <p>Your studio is ready to go. You can now start creating projects and delivering proofs to your clients.</p>
                <br>
                <a href="admin.php?page=aperture-pro-projects" class="ap-btn ap-btn-primary">Go to Dashboard</a>
            </div>
        `;
    }

    function renderActions(isLast = false) {
        return `
            <div class="ap-wizard-actions">
                ${state.step > 0 ? '<button class="ap-btn ap-btn-secondary" id="btn-prev">Back</button>' : '<div></div>'}
                <button class="ap-btn ap-btn-primary" id="btn-next">${isLast ? 'Save & Finish' : 'Next'}</button>
            </div>
        `;
    }

    // --- Logic ---

    function esc(str) {
        if (typeof str !== 'string') return str;
        return str.replace(/"/g, '&quot;');
    }

    function updateStateFromDOM() {
        if (state.step === 0) {
            state.data.brandName = document.getElementById('brandName').value;
            state.data.brandLogo = document.getElementById('brandLogo').value;
        } else if (state.step === 1) {
            state.data.storageAdapter = document.getElementById('storageAdapter').value;
            if (document.getElementById('ikPublicKey')) state.data.ikPublicKey = document.getElementById('ikPublicKey').value;
            if (document.getElementById('ikPrivateKey')) state.data.ikPrivateKey = document.getElementById('ikPrivateKey').value;
            if (document.getElementById('ikUrlEndpoint')) state.data.ikUrlEndpoint = document.getElementById('ikUrlEndpoint').value;
        } else if (state.step === 2) {
            state.data.seoTitle = document.getElementById('seoTitle').value;
            state.data.seoDesc = document.getElementById('seoDesc').value;
        } else if (state.step === 3) {
            state.data.imgQuality = document.getElementById('imgQuality').value;
            state.data.imgMaxWidth = document.getElementById('imgMaxWidth').value;
        }
    }

    function bindEvents() {
        const btnNext = document.getElementById('btn-next');
        const btnPrev = document.getElementById('btn-prev');
        const storageSelect = document.getElementById('storageAdapter');

        if (btnNext) {
            btnNext.addEventListener('click', () => {
                updateStateFromDOM();
                if (state.step === steps.length - 2) {
                    saveAndFinish();
                } else {
                    state.step++;
                    renderStep();
                }
            });
        }

        if (btnPrev) {
            btnPrev.addEventListener('click', () => {
                updateStateFromDOM(); // Save current inputs even if going back
                state.step--;
                renderStep();
            });
        }

        if (storageSelect) {
            storageSelect.addEventListener('change', (e) => {
                const isIk = e.target.value === 'imagekit';
                const fields = document.getElementById('imagekit-fields');
                fields.style.display = isIk ? 'block' : 'none';
            });
        }
    }

    async function saveAndFinish() {
        const btn = document.getElementById('btn-next');
        btn.disabled = true;
        btn.textContent = 'Saving...';

        try {
            const res = await fetch(restUrl + 'wizard/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce
                },
                body: JSON.stringify(state.data)
            });

            const json = await res.json();

            if (json.success) {
                state.step++;
                renderStep();
            } else {
                alert('Error saving settings');
                btn.disabled = false;
            }
        } catch (e) {
            console.error(e);
            alert('Error saving settings');
            btn.disabled = false;
        }
    }

    init();
});
