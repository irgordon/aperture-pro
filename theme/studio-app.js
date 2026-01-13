(function () {

    /* ===========================
       Helpers
    ============================ */
    const qs = (sel, root = document) => root.querySelector(sel);
    const qsa = (sel, root = document) => [...root.querySelectorAll(sel)];

    const portfolioModal = qs('#ap-portfolio-modal');
    const termsModal = qs('#ap-terms-modal');
    const header = qs('.ap-header');


    /* ===========================
       Sticky Header Shadow
    ============================ */
    let lastScroll = 0;
    window.addEventListener('scroll', () => {
        const current = window.scrollY;
        if (current > 10) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        lastScroll = current;
    });


    /* ===========================
       Hamburger Menu
       (You can expand this later)
    ============================ */
    const hamburger = qs('.ap-hamburger');
    hamburger.addEventListener('click', () => {
        document.body.classList.toggle('menu-open');
    });


    /* ===========================
       Portfolio Modal
    ============================ */
    qsa('[data-open-portfolio]').forEach(btn => {
        btn.addEventListener('click', () => {
            portfolioModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    qs('#ap-portfolio-modal .ap-modal-close').addEventListener('click', () => {
        portfolioModal.classList.remove('active');
        document.body.style.overflow = '';
    });


    /* ===========================
       Terms Modal
    ============================ */
    qsa('[data-open-terms]').forEach(btn => {
        btn.addEventListener('click', () => {
            termsModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    qs('#ap-terms-modal .ap-modal-close').addEventListener('click', () => {
        termsModal.classList.remove('active');
        document.body.style.overflow = '';
    });


    /* ===========================
       Close modals on background click
    ============================ */
    [portfolioModal, termsModal].forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

})();
