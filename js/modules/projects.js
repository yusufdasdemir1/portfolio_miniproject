/* =============================================
   PROJECTS — AJAX load, filter, thumbnails
   ============================================= */

import { escapeHtml } from './utils.js';
import { showToast }  from './toast.js';

let allProjects = [];
const projectsGrid = document.getElementById('projectsGrid');

/* ── Thumbnails ── */
function getThumb(p) {
    if (p.image_path) {
        return `<img src="${p.image_path}" alt="${escapeHtml(p.title)}" style="width:100%;height:100%;object-fit:cover;display:block;transition:transform 0.3s ease">`;
    }

    const t = p.title.toLowerCase();

    if (t.includes('nakliyat')) return `
    <div class="pt pt-nakliyat">
        <div class="pt-bar pt-bar-light">
            <div class="pt-dots"><span class="dr"></span><span class="dy"></span><span class="dg"></span></div>
            <span class="pt-url" style="color:#aaa">nakliyat.com.tr</span>
        </div>
        <div class="pt-body" style="background:#f8f9fa">
            <div class="pt-nav" style="background:#fff;border-bottom:1px solid #eee">
                <div style="width:28px;height:8px;background:#093C5D;border-radius:3px"></div>
                <div style="display:flex;gap:8px"><div class="pt-navline"></div><div class="pt-navline"></div><div class="pt-navline"></div></div>
                <div style="width:40px;height:14px;background:#5DF8D8;border-radius:3px"></div>
            </div>
            <div style="padding:12px 14px;display:flex;align-items:center;gap:10px;flex:1">
                <div style="flex:1">
                    <div style="height:10px;background:#093C5D;border-radius:3px;width:80%;margin-bottom:6px"></div>
                    <div style="height:7px;background:#ccc;border-radius:3px;width:60%;margin-bottom:10px"></div>
                    <div style="height:20px;background:#5DF8D8;border-radius:4px;width:50%;display:flex;align-items:center;justify-content:center;font-size:0.55rem;font-weight:700;color:#093C5D">Teklif Al →</div>
                </div>
                <div style="font-size:2.4rem;line-height:1">🚛</div>
            </div>
        </div>
    </div>`;

    if (t.includes('frontend') || t === 'luckofwheel frontend') return `
    <div class="pt pt-wheel-fe">
        <div class="pt-bar">
            <div class="pt-dots"><span class="dr"></span><span class="dy"></span><span class="dg"></span></div>
            <span class="pt-url">luckofwheel.io</span>
        </div>
        <div class="pt-body" style="background:#12082a;align-items:center;justify-content:center;flex-direction:column;gap:10px">
            <div class="pt-wheel-circle"></div>
            <div style="padding:5px 16px;background:linear-gradient(135deg,#a855f7,#ec4899);border-radius:20px;font-size:0.65rem;font-weight:800;color:#fff;letter-spacing:0.08em">SPIN!</div>
        </div>
    </div>`;

    if (t.includes('luckofwheel') || t === 'luckofwheel') return `
    <div class="pt pt-wheel-be">
        <div class="pt-bar">
            <div class="pt-dots"><span class="dr"></span><span class="dy"></span><span class="dg"></span></div>
            <span class="pt-url">api.luckofwheel.io</span>
        </div>
        <div class="pt-body" style="background:#0d1117;padding:14px 16px;flex-direction:column;justify-content:center;gap:6px">
            <div class="pt-codeline"><span style="color:#3fb950">$</span> <span style="color:#e6edf3">python api.py</span></div>
            <div class="pt-codeline"><span style="color:#5DF8D8">✓</span> <span style="color:#8b949e">Server on :8000</span></div>
            <div class="pt-codeline"><span style="color:#5DF8D8">✓</span> <span style="color:#8b949e">Wheel configured</span></div>
            <div class="pt-codeline"><span style="color:#d29922">→</span> <span style="color:#e6edf3">GET /spin</span> <span style="color:#3fb950">[200]</span></div>
            <div class="pt-codeline"><span style="color:#3fb950">✓</span> <span style="color:#8b949e">Prize: Gift Card 🎁</span></div>
        </div>
    </div>`;

    if (t.includes('spor')) return `
    <div class="pt pt-spor">
        <div class="pt-bar">
            <div class="pt-dots"><span class="dr"></span><span class="dy"></span><span class="dg"></span></div>
            <span class="pt-url">sporsalonu.app</span>
        </div>
        <div class="pt-body" style="background:#0f1923;flex-direction:column;padding:12px 14px;gap:8px">
            <div style="font-size:0.58rem;font-weight:800;letter-spacing:0.12em;color:#5DF8D8;margin-bottom:2px">GYM DASHBOARD</div>
            <div class="pt-statrow"><span>Members</span><div class="pt-statbar" style="width:82%"></div><span>248</span></div>
            <div class="pt-statrow"><span>Classes</span><div class="pt-statbar" style="width:58%"></div><span>12</span></div>
            <div class="pt-statrow"><span>Revenue</span><div class="pt-statbar" style="width:91%"></div><span>↑</span></div>
            <div style="display:flex;gap:6px;margin-top:4px">
                <div style="flex:1;background:rgba(93,248,216,0.08);border:1px solid rgba(93,248,216,0.15);border-radius:4px;padding:5px 6px;font-size:0.52rem;color:#5DF8D8;text-align:center">Members</div>
                <div style="flex:1;background:rgba(93,248,216,0.08);border:1px solid rgba(93,248,216,0.15);border-radius:4px;padding:5px 6px;font-size:0.52rem;color:#5DF8D8;text-align:center">Schedule</div>
            </div>
        </div>
    </div>`;

    if (t.includes('portfolio')) return `
    <div class="pt pt-portf">
        <div class="pt-bar">
            <div class="pt-dots"><span class="dr"></span><span class="dy"></span><span class="dg"></span></div>
            <span class="pt-url">yusufdasdemir.dev</span>
        </div>
        <div class="pt-body" style="background:#04101c;flex-direction:column">
            <div style="padding:6px 12px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(93,248,216,0.08)">
                <span style="font-family:monospace;font-size:0.6rem;font-weight:700;color:#5DF8D8">YD.</span>
                <div style="display:flex;gap:6px"><div class="pt-navline"></div><div class="pt-navline"></div><div class="pt-navline"></div></div>
            </div>
            <div style="padding:14px 12px;flex:1;display:flex;flex-direction:column;justify-content:center;gap:6px">
                <div style="height:12px;background:linear-gradient(90deg,#5DF8D8,#6FD1D7);border-radius:3px;width:55%"></div>
                <div style="height:7px;background:rgba(255,255,255,0.1);border-radius:3px;width:75%"></div>
                <div style="height:7px;background:rgba(255,255,255,0.07);border-radius:3px;width:60%"></div>
                <div style="display:flex;gap:6px;margin-top:4px">
                    <div style="height:18px;background:#5DF8D8;border-radius:3px;width:70px"></div>
                    <div style="height:18px;background:rgba(93,248,216,0.1);border:1px solid rgba(93,248,216,0.2);border-radius:3px;width:60px"></div>
                </div>
            </div>
        </div>
    </div>`;

    return `<div class="pt"><div class="pt-bar"><div class="pt-dots"><span class="dr"></span><span class="dy"></span><span class="dg"></span></div><span class="pt-url">${escapeHtml(p.title)}</span></div><div class="pt-body" style="align-items:center;justify-content:center;font-size:2.5rem">🌐</div></div>`;
}

/* ── Card builder ── */
function buildCard(p) {
    const techs      = p.technologies
        ? p.technologies.split(',').map(t => `<span class="project-tech">${t.trim()}</span>`).join('')
        : '';
    const badgeClass = `badge-${p.category}`;
    const githubLink = p.github_url
        ? `<a href="${p.github_url}" class="project-link github" target="_blank" rel="noopener"><i class="fab fa-github"></i> GitHub</a>`
        : '';
    const liveLink   = p.live_url
        ? `<a href="${p.live_url}" class="project-link live" target="_blank" rel="noopener"><i class="fas fa-external-link-alt"></i> Live</a>`
        : '';

    return `
    <div class="project-card" data-category="${p.category}">
        <div class="project-thumb">${getThumb(p)}<span class="project-badge ${badgeClass}">${p.category}</span></div>
        <div class="project-body">
            <h3 class="project-title">${escapeHtml(p.title)}</h3>
            <p class="project-desc">${escapeHtml(p.description)}</p>
            <div class="project-techs">${techs}</div>
            <div class="project-links">${githubLink}${liveLink}</div>
        </div>
    </div>`;
}

/* ── Render ── */
function renderProjects(filter) {
    const list = filter === 'all' ? allProjects : allProjects.filter(p => p.category === filter);
    projectsGrid.innerHTML = list.length
        ? list.map(buildCard).join('')
        : '<div class="projects-loading"><p style="color:var(--text-2)">No projects in this category yet.</p></div>';
}

/* ── Fallback data ── */
function showFallbackProjects() {
    allProjects = [
        { id:1, title:'E-Commerce Platform',  description:'Full-featured online store with cart, checkout, and admin panel.', technologies:'HTML5,CSS3,JavaScript,PHP,MySQL', category:'fullstack', github_url:'#', live_url:'#' },
        { id:2, title:'Portfolio Website',     description:'Responsive personal portfolio with dark/light theme and AJAX contact form.', technologies:'HTML5,CSS3,JavaScript,PHP,MySQL', category:'fullstack', github_url:'#', live_url:'#' },
        { id:3, title:'Task Manager App',      description:'Drag-and-drop task board with real-time updates.', technologies:'JavaScript,PHP,MySQL,AJAX', category:'fullstack', github_url:'#', live_url:'#' },
    ];
    renderProjects('all');
}

/* ── Load ── */
export async function loadProjects() {
    try {
        const res  = await fetch('php/get_projects.php');
        const data = await res.json();
        if (data.success) {
            allProjects = data.projects;
            renderProjects('all');
        } else {
            showFallbackProjects();
        }
    } catch {
        showFallbackProjects();
    }
}

/* ── Filter ── */
export function initProjectFilter() {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            renderProjects(btn.getAttribute('data-filter'));
        });
    });
}
