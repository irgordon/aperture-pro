(function () {
    // --- 1. SPA MODAL LOGIC (With Deep Linking) ---

    window.openModal = function(id) {
        document.body.style.overflow = 'hidden';
        const modal = document.getElementById(id + '-modal');
        if (modal) {
            modal.classList.add('active');

            // DEEP LINKING: Update URL to #gallery without reloading
            history.pushState({modal: id}, "", "#" + id);

            // A11Y: Move focus to close button
            const closeBtn = modal.querySelector('.close-btn');
            if(closeBtn) closeBtn.focus();
        }
    };

    window.closeModal = function() {
        document.body.style.overflow = 'auto';
        document.querySelectorAll('.spa-modal').forEach(m => m.classList.remove('active'));

        // Clean up URL hash
        if(window.location.hash) {
            history.pushState(null, "", " ");
        }
    };

    // Handle Browser Back Button
    window.onpopstate = function(event) {
        if(event.state && event.state.modal) {
            // If history state exists (forward button), open modal
            document.body.style.overflow = 'hidden';
            const modal = document.getElementById(event.state.modal + '-modal');
            if(modal) modal.classList.add('active');
        } else {
            // If back button pressed (no state), close all
            document.body.style.overflow = 'auto';
            document.querySelectorAll('.spa-modal').forEach(m => m.classList.remove('active'));
        }
    };

    // Handle Deep Link on Page Load (e.g. user visits iangordon.pro/#gallery)
    document.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash.substring(1); // remove #
        if(hash === 'gallery' || hash === 'terms') {
            openModal(hash);
        }
    });

    // --- 4. HAMBURGER MENU ---
    document.addEventListener('DOMContentLoaded', () => {
        const hamburger = document.querySelector('.ap-hamburger');
        const navActions = document.querySelector('.nav-actions');

        if (hamburger && navActions) {
            hamburger.addEventListener('click', (e) => {
                e.stopPropagation();
                hamburger.classList.toggle('active');
                navActions.classList.toggle('active');
            });

            // Close menu when a link inside it is clicked
            navActions.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    hamburger.classList.remove('active');
                    navActions.classList.remove('active');
                });
            });

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!hamburger.contains(e.target) && !navActions.contains(e.target) && navActions.classList.contains('active')) {
                    hamburger.classList.remove('active');
                    navActions.classList.remove('active');
                }
            });
        }
    });

    // --- 2. GALLERY FILTER LOGIC ---
    window.filterGallery = function(category) {
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        if (event && event.target) {
            event.target.classList.add('active');
        }

        document.querySelectorAll('.masonry-item').forEach(item => {
            if (category === 'all' || item.getAttribute('data-category') === category) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    };

    // --- 3. GOOGLE SHEET FORM LOGIC ---
    // IMPORTANT: Paste your Web App URL here
    const scriptURL = 'https://script.google.com/macros/s/AKfycbxn-dTN2mflSSpNfw0yGMSnZB4w-CSiStvlnMBlvKOEbMlD5ZLx0_3JsriZVd3e6FX_/exec';

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.forms['submit-to-google-sheet'];
        const btn = document.getElementById('submitBtn');
        const successMsg = document.getElementById('form-success');
        const errorMsg = document.getElementById('form-error');

        if(form) {
            form.addEventListener('submit', e => {
                e.preventDefault();
                btn.disabled = true;
                btn.innerText = "Sending...";

                fetch(scriptURL, { method: 'POST', body: new FormData(form)})
                    .then(response => {
                        form.style.display = "none";
                        if(successMsg) {
                            successMsg.style.display = "block";
                            successMsg.scrollIntoView({behavior: "smooth"});
                        }
                    })
                    .catch(error => {
                        console.error('Error!', error.message);
                        if(errorMsg) errorMsg.style.display = "block";
                        btn.disabled = false;
                        btn.innerText = "Send Request";
                    });
            });
        }
    });

})();
