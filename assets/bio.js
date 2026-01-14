document.addEventListener('DOMContentLoaded', function () {

    // Donation Logic
    const donationForm = document.querySelector('.ap-donation-form');
    if (donationForm) {
        const presets = donationForm.querySelectorAll('.ap-donate-btn');
        const mainBtn = donationForm.querySelector('.ap-bio-main-btn');
        const baseText = mainBtn.tagName === 'A' ? mainBtn.textContent : mainBtn.innerText;
        let originalLink = mainBtn.tagName === 'A' ? mainBtn.href : '';

        presets.forEach(btn => {
            btn.addEventListener('click', () => {
                // Toggle active state
                presets.forEach(b => b.style.background = '#f0f2f5');
                presets.forEach(b => b.style.color = '#333');

                btn.style.background = '#333';
                btn.style.color = '#fff';

                const amount = btn.dataset.amount;
                mainBtn.textContent = `Donate $${amount}`;

                if (mainBtn.tagName === 'A' && originalLink) {
                    // Append amount param if it's a link
                    const separator = originalLink.includes('?') ? '&' : '?';
                    mainBtn.href = `${originalLink}${separator}amount=${amount}`;
                }
            });
        });
    }

});
