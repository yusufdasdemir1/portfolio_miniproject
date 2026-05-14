/* =============================================
   TOAST — Notification system
   ============================================= */

let toastTimer;
const toast      = document.getElementById('toast');
const toastIcon  = document.getElementById('toastIcon');
const toastTitle = document.getElementById('toastTitle');
const toastMsg   = document.getElementById('toastMessage');
const toastClose = document.getElementById('toastClose');

export function showToast(type, title, message) {
    toastIcon.className  = `toast-icon ${type}`;
    toastIcon.innerHTML  = type === 'success'
        ? '<i class="fas fa-check"></i>'
        : '<i class="fas fa-exclamation-triangle"></i>';
    toastTitle.textContent = title;
    toastMsg.textContent   = message;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(closeToast, 5000);
}

export function closeToast() {
    toast.classList.remove('show');
}

toastClose.addEventListener('click', closeToast);
