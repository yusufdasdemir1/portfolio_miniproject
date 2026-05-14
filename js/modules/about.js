/* =============================================
   ABOUT — Dynamic content loading
   ============================================= */

import { escapeHtml } from './utils.js';

export async function loadAbout() {
    try {
        const res  = await fetch('php/get_about.php');
        const data = await res.json();
        if (!data.success) return;
        const a = data.about;

        const heading = document.getElementById('aboutHeading');
        const bios    = document.getElementById('aboutBios');
        const photo   = document.getElementById('aboutPhoto');
        const stats   = document.getElementById('aboutStats');

        if (heading) heading.textContent = a.heading || '';
        if (photo && a.photo_path) photo.src = a.photo_path;

        if (bios) {
            bios.innerHTML = [a.bio_1, a.bio_2, a.bio_3]
                .filter(Boolean)
                .map(t => `<p class="about-text">${escapeHtml(t)}</p>`)
                .join('');
        }

        if (stats) {
            stats.innerHTML = `
            <div class="stat-card">
                <span class="stat-number" data-target="${a.stat_projects}">${a.stat_projects}</span>+
                <span class="stat-label">Projects Done</span>
            </div>
            <div class="stat-card">
                <span class="stat-number" data-target="${a.stat_years}">${a.stat_years}</span>+
                <span class="stat-label">Years Exp.</span>
            </div>
            <div class="stat-card">
                <span class="stat-number" data-target="${a.stat_clients}">${a.stat_clients}</span>+
                <span class="stat-label">Happy Clients</span>
            </div>`;
        }
    } catch (e) {
        console.error('loadAbout error:', e);
    }
}
