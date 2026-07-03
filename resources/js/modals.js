/**
 * Modales et lightbox – ouvert/fermeture fluide, Escape, backdrop
 */
function openModal(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('hidden');
  requestAnimationFrame(() => {
    el.classList.remove('opacity-0');
    const content = el.querySelector('.modal-content');
    if (content) {
      // Side-panel : glisse depuis la droite
      if (content.classList.contains('translate-x-full')) {
        content.classList.remove('translate-x-full');
      } else {
        // Modal centré : zoom-in
        content.classList.remove('scale-95');
      }
    }
  });
  document.body.style.overflow = 'hidden';
}

function closeModal(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.add('opacity-0');
  const content = el.querySelector('.modal-content');
  if (content) {
    // Détecter le type par la présence de la classe de position
    if (content.classList.contains('top-0') && content.classList.contains('right-0')) {
      content.classList.add('translate-x-full');
    } else {
      content.classList.add('scale-95');
    }
  }
  setTimeout(() => {
    el.classList.add('hidden');
    document.body.style.overflow = '';
  }, 200);
}

window.openModal  = openModal;
window.closeModal = closeModal;

function initModals() {

  document.querySelectorAll('[data-modal-close]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      const id = e.currentTarget.getAttribute('data-modal-close');
      if (id) closeModal(id);
    });
  });

  document.querySelectorAll('[data-modal-open]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      const id = e.currentTarget.getAttribute('data-modal-open');
      if (id) openModal(id);
    });
  });

  document.querySelectorAll('.modal-lightbox-trigger').forEach((el) => {
    el.addEventListener('click', () => {
      const src = el.getAttribute('data-src');
      const alt = el.getAttribute('data-alt') || '';
      const img = document.getElementById('lightbox-img');
      const modal = document.getElementById('lightbox-modal');
      if (img && modal && src) {
        img.src = src;
        img.alt = alt;
        openModal('lightbox-modal');
      }
    });
  });

  document.querySelectorAll('.modal-overlay').forEach((overlay) => {
    overlay.addEventListener('click', (e) => {
      if (e.target.classList.contains('modal-backdrop')) {
        closeModal(overlay.id);
      }
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay:not(.hidden)').forEach((el) => {
        closeModal(el.id);
      });
    }
  });

  // Confirmation avant action (boutons avec data-confirm)
  document.querySelectorAll('[data-confirm]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      const message = e.currentTarget.getAttribute('data-confirm');
      const formId = e.currentTarget.getAttribute('data-confirm-form');
      const form = formId ? document.getElementById(formId) : e.currentTarget.closest('form');
      if (!message) return;
      e.preventDefault();
      const msgEl = document.getElementById('confirm-modal-message');
      const submitBtn = document.getElementById('confirm-modal-submit');
      if (msgEl) msgEl.textContent = message;
      if (submitBtn) {
        submitBtn.onclick = () => {
          if (form) form.submit();
          closeModal('confirm-modal');
        };
      }
      openModal('confirm-modal');
    });
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initModals);
} else {
  initModals();
}
