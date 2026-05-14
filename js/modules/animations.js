/* =============================================
   ANIMATIONS — Reveal, Skill bars, Counters
   ============================================= */

/* ── Reveal on scroll ── */
export const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
            setTimeout(() => entry.target.classList.add('visible'), i * 80);
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

/* ── Skill bar fill ── */
export const skillObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const fill  = entry.target;
            fill.style.width = fill.getAttribute('data-width') + '%';
            fill.classList.add('animated');
            skillObserver.unobserve(fill);
        }
    });
}, { threshold: 0.3 });

/* ── Counter animation ── */
function animateCounter(el, target) {
    let current = 0;
    const step  = target / 40;
    const timer = setInterval(() => {
        current += step;
        if (current >= target) { el.textContent = target; clearInterval(timer); return; }
        el.textContent = Math.floor(current);
    }, 40);
}

const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const el     = entry.target;
            const target = parseInt(el.getAttribute('data-target'), 10);
            animateCounter(el, target);
            counterObserver.unobserve(el);
        }
    });
}, { threshold: 0.5 });

export function initAnimations() {
    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
    document.querySelectorAll('.progress-fill').forEach(f => skillObserver.observe(f));
    document.querySelectorAll('.stat-number').forEach(el => counterObserver.observe(el));
}
