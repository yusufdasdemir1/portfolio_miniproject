/* =============================================
   CONTACT — Form AJAX submit
   ============================================= */

import { showToast } from './toast.js';

export function initContact() {
    const contactForm = document.getElementById('contactForm');
    const submitBtn   = document.getElementById('submitBtn');

    if (!contactForm) return;

    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const fields = ['name', 'email', 'subject', 'message'];
        let   valid  = true;

        fields.forEach(id => {
            const el = document.getElementById(id);
            el.classList.remove('invalid');
            if (!el.value.trim()) { el.classList.add('invalid'); valid = false; }
        });

        const emailEl = document.getElementById('email');
        if (emailEl.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailEl.value)) {
            emailEl.classList.add('invalid');
            valid = false;
        }

        if (!valid) {
            showToast('error', 'Validation Error', 'Please fill in all required fields correctly.');
            return;
        }

        const btnText        = submitBtn.querySelector('.btn-text');
        submitBtn.disabled   = true;
        btnText.textContent  = 'Sending...';

        try {
            const res  = await fetch('php/contact.php', { method: 'POST', body: new FormData(contactForm) });
            const data = await res.json();

            if (data.success) {
                showToast('success', 'Message Sent!', "Thanks! I'll get back to you soon.");
                contactForm.reset();
            } else {
                showToast('error', 'Oops!', data.message || 'Something went wrong. Please try again.');
            }
        } catch {
            showToast('error', 'Network Error', 'Could not connect. Please try again later.');
        } finally {
            submitBtn.disabled  = false;
            btnText.textContent = 'Send Message';
        }
    });

    contactForm.querySelectorAll('input, textarea').forEach(el => {
        el.addEventListener('input', () => el.classList.remove('invalid'));
    });
}
