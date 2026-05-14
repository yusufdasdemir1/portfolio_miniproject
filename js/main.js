/* =============================================
   MAIN — Entry point
   Portfolio: Yusuf Daşdemir
   ============================================= */

import { initTheme }                    from './modules/theme.js';
import { initNavbar }                   from './modules/navbar.js';
import { initAnimations }               from './modules/animations.js';
import { loadAbout }                    from './modules/about.js';
import { loadSkills }                   from './modules/skills.js';
import { loadProjects, initProjectFilter } from './modules/projects.js';
import { initContact }                  from './modules/contact.js';

initTheme();
initNavbar();
initAnimations();
initContact();
initProjectFilter();

loadProjects();
loadAbout();
loadSkills();
