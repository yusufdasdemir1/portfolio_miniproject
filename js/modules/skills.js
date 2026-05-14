/* =============================================
   SKILLS — Dynamic skill/tool loading
   ============================================= */

import { escapeHtml }                    from './utils.js';
import { revealObserver, skillObserver } from './animations.js';

const CAT_META = {
    frontend: { label: 'Frontend', icon: 'fas fa-palette'  },
    backend:  { label: 'Backend',  icon: 'fas fa-server'   },
    devops:   { label: 'DevOps',   icon: 'fas fa-infinity' },
};

function buildSkillBar(s) {
    return `
    <div class="skill-bar-item">
        <div class="skill-info">
            <span>${s.icon ? `<i class="${escapeHtml(s.icon)}"></i> ` : ''}${escapeHtml(s.name)}</span>
            <span>${s.percent}%</span>
        </div>
        <div class="progress-track">
            <div class="progress-fill" data-width="${s.percent}"></div>
        </div>
    </div>`;
}

function buildToolCard(t) {
    return `
    <div class="tool-card">
        <i class="${escapeHtml(t.icon)}"></i>
        <span>${escapeHtml(t.name)}</span>
    </div>`;
}

function buildCategory(cat, skills) {
    const meta = CAT_META[cat] || { label: cat, icon: 'fas fa-code' };
    return `
    <div class="skill-category reveal">
        <div class="category-header">
            <div class="category-icon"><i class="${meta.icon}"></i></div>
            <h3>${meta.label}</h3>
        </div>
        <div class="skill-bars">
            ${skills.map(buildSkillBar).join('')}
        </div>
    </div>`;
}

function buildToolsCard(tools) {
    return `
    <div class="skill-category reveal">
        <div class="category-header">
            <div class="category-icon"><i class="fas fa-wrench"></i></div>
            <h3>Tools</h3>
        </div>
        <div class="tool-cards">
            ${tools.map(buildToolCard).join('')}
        </div>
    </div>`;
}

export async function loadSkills() {
    try {
        const res  = await fetch('php/get_skills.php');
        const data = await res.json();
        if (!data.success) return;

        const grid = document.getElementById('skillsGrid');
        if (!grid) return;

        const grouped = {};
        data.skills.forEach(s => {
            if (!grouped[s.category]) grouped[s.category] = [];
            grouped[s.category].push(s);
        });

        let html = Object.entries(grouped).map(([cat, skills]) => buildCategory(cat, skills)).join('');

        if (data.tools?.length) {
            html += buildToolsCard(data.tools);
        }

        grid.innerHTML = html;

        grid.querySelectorAll('.reveal').forEach(el => {
            el.getBoundingClientRect().top < window.innerHeight
                ? el.classList.add('visible')
                : revealObserver.observe(el);
        });
        grid.querySelectorAll('.progress-fill').forEach(f => skillObserver.observe(f));

    } catch (e) {
        console.error('loadSkills error:', e);
    }
}
