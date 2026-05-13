/* =============================================
   AURORA PORTFOLIO — main.js
   ============================================= */

// ─── LOAD ABOUT ───────────────────────────────
async function loadAbout() {
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
    } catch (e) { console.error('loadAbout error:', e); }
}

// ─── LOAD SKILLS ──────────────────────────────
async function loadSkills() {
    try {
        const res  = await fetch('php/get_skills.php');
        const data = await res.json();
        if (!data.success) return;

        const catMeta = {
            frontend: { label: 'Frontend',   icon: 'fas fa-palette' },
            backend:  { label: 'Backend',    icon: 'fas fa-server'  },
            devops:   { label: 'DevOps',     icon: 'fas fa-infinity'},
        };

        const grouped = {};
        data.skills.forEach(s => {
            if (!grouped[s.category]) grouped[s.category] = [];
            grouped[s.category].push(s);
        });

        const grid = document.getElementById('skillsGrid');
        if (!grid) return;

        let html = '';
        for (const [cat, skills] of Object.entries(grouped)) {
            const meta = catMeta[cat] || { label: cat, icon: 'fas fa-code' };
            html += `
            <div class="skill-category reveal">
                <div class="category-header">
                    <div class="category-icon"><i class="${meta.icon}"></i></div>
                    <h3>${meta.label}</h3>
                </div>
                <div class="skill-bars">
                    ${skills.map(s => `
                    <div class="skill-bar-item">
                        <div class="skill-info">
                            <span>${s.icon ? `<i class="${escapeHtml(s.icon)}"></i> ` : ''}${escapeHtml(s.name)}</span>
                            <span>${s.percent}%</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" data-width="${s.percent}"></div>
                        </div>
                    </div>`).join('')}
                </div>
            </div>`;
        }

        /* Tools card */
        if (data.tools && data.tools.length) {
            html += `
            <div class="skill-category reveal">
                <div class="category-header">
                    <div class="category-icon"><i class="fas fa-wrench"></i></div>
                    <h3>Tools</h3>
                </div>
                <div class="tool-cards">
                    ${data.tools.map(t => `
                    <div class="tool-card">
                        <i class="${escapeHtml(t.icon)}"></i>
                        <span>${escapeHtml(t.name)}</span>
                    </div>`).join('')}
                </div>
            </div>`;
        }

        grid.innerHTML = html;

        /* Immediately make visible if already in viewport, else observe */
        grid.querySelectorAll('.reveal').forEach(el => {
            const rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight) {
                el.classList.add('visible');
            } else {
                revealObserver.observe(el);
            }
        });
        grid.querySelectorAll('.progress-fill').forEach(f => skillObserver.observe(f));
    } catch (e) { console.error('loadSkills error:', e); }
}

// ─── THEME ───────────────────────────────────
const html        = document.documentElement;
const themeToggle = document.getElementById('themeToggle');

function setTheme(theme) {
    html.setAttribute('data-theme', theme);
    localStorage.setItem('portfolio-theme', theme);
}

(function initTheme() {
    const saved = localStorage.getItem('portfolio-theme');
    setTheme(saved || 'dark');
})();

themeToggle.addEventListener('click', () => {
    const current = html.getAttribute('data-theme');
    setTheme(current === 'dark' ? 'light' : 'dark');
});


// ─── MOBILE NAV ───────────────────────────────
const hamburger = document.getElementById('hamburger');
const navLinks  = document.getElementById('navLinks');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('open');
    navLinks.classList.toggle('open');
});

navLinks.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
        hamburger.classList.remove('open');
        navLinks.classList.remove('open');
    });
});

// ─── NAVBAR ON SCROLL ─────────────────────────
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 20);
}, { passive: true });

// ─── ACTIVE NAV LINK ─────────────────────────
const sections  = document.querySelectorAll('section[id]');
const navItems  = document.querySelectorAll('.nav-link');

function updateActiveLink() {
    const scrollY = window.scrollY + 120;
    sections.forEach(section => {
        const top    = section.offsetTop;
        const height = section.offsetHeight;
        const id     = section.getAttribute('id');
        if (scrollY >= top && scrollY < top + height) {
            navItems.forEach(link => link.classList.remove('active'));
            const active = document.querySelector(`.nav-link[href="#${id}"]`);
            if (active) active.classList.add('active');
        }
    });
}

window.addEventListener('scroll', updateActiveLink, { passive: true });

// ─── REVEAL ON SCROLL ─────────────────────────
const revealEls = document.querySelectorAll('.reveal');

const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
            setTimeout(() => entry.target.classList.add('visible'), i * 80);
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

revealEls.forEach(el => revealObserver.observe(el));

// ─── SKILL BARS ───────────────────────────────
const fills = document.querySelectorAll('.progress-fill');

const skillObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const fill  = entry.target;
            const width = fill.getAttribute('data-width');
            fill.style.width = width + '%';
            fill.classList.add('animated');
            skillObserver.unobserve(fill);
        }
    });
}, { threshold: 0.3 });

fills.forEach(f => skillObserver.observe(f));

// ─── COUNTERS ─────────────────────────────────
function animateCounter(el, target) {
    let current   = 0;
    const step    = target / 40;
    const timer   = setInterval(() => {
        current  += step;
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

document.querySelectorAll('.stat-number').forEach(el => counterObserver.observe(el));

// ─── PROJECTS — AJAX ─────────────────────────
const projectsGrid = document.getElementById('projectsGrid');
let   allProjects  = [];

const gradients = [
    'linear-gradient(135deg,#093C5D44,#3B759722)',
    'linear-gradient(135deg,#3B759722,#5DF8D811)',
    'linear-gradient(135deg,#5DF8D811,#6FD1D722)',
    'linear-gradient(135deg,#093C5D33,#6FD1D722)',
    'linear-gradient(135deg,#6FD1D722,#3B759733)',
    'linear-gradient(135deg,#3B759733,#5DF8D811)',
];

const emojis = ['🌐', '⚡', '🎨', '🛠️', '📊', '🔧', '💻', '🚀', '🗄️', '🎯'];

function getThumb(p) {
    /* Eğer admin panelinden fotoğraf yüklendiyse onu göster */
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

function buildCard(p, idx) {
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

function renderProjects(filter) {
    const list = filter === 'all' ? allProjects : allProjects.filter(p => p.category === filter);
    if (list.length === 0) {
        projectsGrid.innerHTML = '<div class="projects-loading"><p style="color:var(--text-2)">No projects in this category yet.</p></div>';
        return;
    }
    projectsGrid.innerHTML = list.map((p, i) => buildCard(p, i)).join('');
}

async function loadProjects() {
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

function showFallbackProjects() {
    allProjects = [
        { id:1, title:'E-Commerce Platform',    description:'Full-featured online store with cart, checkout, and admin panel built with PHP and MySQL.',            technologies:'HTML5,CSS3,JavaScript,PHP,MySQL', category:'fullstack', github_url:'#', live_url:'#' },
        { id:2, title:'Portfolio Website',       description:'Responsive personal portfolio with dark/light theme, animations, and AJAX contact form.',               technologies:'HTML5,CSS3,JavaScript,PHP,MySQL', category:'fullstack', github_url:'#', live_url:'#' },
        { id:3, title:'Task Manager App',        description:'Drag-and-drop task board with real-time updates using Fetch API and a RESTful PHP backend.',            technologies:'JavaScript,PHP,MySQL,AJAX',       category:'fullstack', github_url:'#', live_url:'#' },
        { id:4, title:'Weather Dashboard',       description:'Beautiful weather app fetching live data from an external API with animated weather icons.',            technologies:'HTML5,CSS3,JavaScript,Fetch API', category:'frontend',  github_url:'#', live_url:'#' },
        { id:5, title:'REST API — Blog',         description:'RESTful JSON API for a blogging platform with JWT auth, CRUD operations, and full-text search.',        technologies:'PHP,MySQL,REST API,JSON',         category:'backend',   github_url:'#', live_url:'#' },
        { id:6, title:'Interactive Quiz App',    description:'Dynamic quiz application with timer, score tracking, leaderboard, and category filtering.',             technologies:'HTML5,CSS3,JavaScript,PHP,MySQL', category:'fullstack', github_url:'#', live_url:'#' },
    ];
    renderProjects('all');
}

loadProjects();

// ─── PROJECT FILTER ───────────────────────────
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderProjects(btn.getAttribute('data-filter'));
    });
});

// ─── CONTACT FORM ─────────────────────────────
const contactForm = document.getElementById('contactForm');
const submitBtn   = document.getElementById('submitBtn');

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

    if (!valid) { showToast('error', 'Validation Error', 'Please fill in all required fields correctly.'); return; }

    const btnText = submitBtn.querySelector('.btn-text');
    submitBtn.disabled   = true;
    btnText.textContent  = 'Sending...';

    const formData = new FormData(contactForm);

    try {
        const res  = await fetch('php/contact.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
            showToast('success', 'Message Sent!', 'Thanks! I\'ll get back to you soon.');
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

// ─── TOAST ────────────────────────────────────
let toastTimer;
const toast      = document.getElementById('toast');
const toastIcon  = document.getElementById('toastIcon');
const toastTitle = document.getElementById('toastTitle');
const toastMsg   = document.getElementById('toastMessage');
const toastClose = document.getElementById('toastClose');

function showToast(type, title, message) {
    toastIcon.className   = `toast-icon ${type}`;
    toastIcon.innerHTML   = type === 'success'
        ? '<i class="fas fa-check"></i>'
        : '<i class="fas fa-exclamation-triangle"></i>';
    toastTitle.textContent = title;
    toastMsg.textContent   = message;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(closeToast, 5000);
}

function closeToast() {
    toast.classList.remove('show');
}

toastClose.addEventListener('click', closeToast);

// ─── UTILS ────────────────────────────────────
function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

// ─── SMOOTH SCROLL ────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', (e) => {
        const target = document.querySelector(anchor.getAttribute('href'));
        if (target) {
            e.preventDefault();
            const top = target.getBoundingClientRect().top + window.scrollY - (parseInt(getComputedStyle(document.documentElement).getPropertyValue('--nav-h')) || 72);
            window.scrollTo({ top, behavior: 'smooth' });
        }
    });
});

// ─── INIT DYNAMIC SECTIONS ────────────────────
// Called last so revealObserver & skillObserver are already defined
loadAbout();
loadSkills();
