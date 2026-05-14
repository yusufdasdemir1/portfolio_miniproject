/* =============================================
   THEME — Dark / Light toggle
   ============================================= */

const html        = document.documentElement;
const themeToggle = document.getElementById('themeToggle');

function setTheme(theme) {
    html.setAttribute('data-theme', theme);
    localStorage.setItem('portfolio-theme', theme);
}

export function initTheme() {
    const saved = localStorage.getItem('portfolio-theme');
    setTheme(saved || 'dark');

    themeToggle.addEventListener('click', () => {
        const current = html.getAttribute('data-theme');
        setTheme(current === 'dark' ? 'light' : 'dark');
    });
}
