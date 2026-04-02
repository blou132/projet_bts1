(() => {
  const $ = (s, root = document) => root.querySelector(s);
  const $$ = (s, root = document) => [...root.querySelectorAll(s)];
  const rgpdText =
    "Les informations collectees sont utilisees uniquement pour repondre a votre demande. Conformement au RGPD, vous pouvez exercer vos droits d'acces, de rectification et de suppression en nous contactant.";

  const parseCell = (text) => {
    const v = (text || '').trim();
    if (!v) return '';

    const n = Number(v.replace(/[^0-9.-]/g, ''));
    if (!Number.isNaN(n) && /\d/.test(v)) return n;

    const d = Date.parse(v);
    if (!Number.isNaN(d) && /[-/]/.test(v)) return d;

    return v.toLowerCase();
  };

  const initFilters = () => {
    $$('[data-filter-table]').forEach((input) => {
      const table = document.getElementById(input.dataset.filterTable || '');
      const body = table && table.tBodies ? table.tBodies[0] : null;
      if (!body) return;

      const rows = [...body.rows];
      const count = document.getElementById(input.dataset.filterCount || '');

      const update = () => {
        const q = (input.value || '').trim().toLowerCase();
        let visible = 0;

        rows.forEach((row) => {
          const show = !q || row.textContent.toLowerCase().includes(q);
          row.style.display = show ? '' : 'none';
          if (show) visible += 1;
        });

        if (count) count.textContent = `${visible} / ${rows.length}`;
      };

      input.addEventListener('input', update);
      update();
    });
  };

  const initSort = () => {
    $$('table[data-sortable="true"]').forEach((table) => {
      const head = table.tHead && table.tHead.rows ? table.tHead.rows[0] : null;
      const body = table.tBodies ? table.tBodies[0] : null;
      if (!head || !body) return;

      const headers = [...head.cells];

      headers.forEach((th, col) => {
        if (th.dataset.sort === 'false') return;

        th.classList.add('sortable');
        th.tabIndex = 0;

        const sort = () => {
          const asc = !th.classList.contains('is-asc');
          headers.forEach((h) => h.classList.remove('is-asc', 'is-desc'));
          th.classList.add(asc ? 'is-asc' : 'is-desc');

          [...body.rows]
            .map((row, i) => ({ row, i, v: parseCell((row.cells[col] && row.cells[col].textContent) || '') }))
            .sort((a, b) => {
              if (a.v < b.v) return asc ? -1 : 1;
              if (a.v > b.v) return asc ? 1 : -1;
              return a.i - b.i;
            })
            .forEach(({ row }) => body.appendChild(row));
        };

        th.addEventListener('click', sort);
        th.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            sort();
          }
        });
      });
    });
  };

  const initCountdown = () => {
    const cards = $$('.calendar-card[data-date]');
    if (!cards.length) return;

    const now = Date.now();
    let next = null;

    cards.forEach((card) => {
      const raw = card.dataset.date;
      const date = raw ? new Date(`${raw}T00:00:00`) : null;
      if (!date || Number.isNaN(date.getTime()) || date.getTime() <= now) return;
      if (!next || date.getTime() < next.time) {
        next = { card, time: date.getTime(), name: card.dataset.name || '-', location: card.dataset.location || '-' };
      }
    });

    if (!next) return;

    next.card.classList.add('is-next');
    const nameEl = $('.js-next-name');
    const metaEl = $('.js-next-meta');
    const counter = $('.js-next-countdown');

    if (nameEl) nameEl.textContent = next.name;
    if (metaEl) metaEl.textContent = next.location;
    if (!counter) return;

    const tick = () => {
      const diff = next.time - Date.now();
      if (diff <= 0) {
        counter.textContent = 'Course en cours';
        return;
      }

      const t = Math.floor(diff / 1000);
      const d = Math.floor(t / 86400);
      const h = Math.floor((t % 86400) / 3600);
      const m = Math.floor((t % 3600) / 60);
      const s = t % 60;
      counter.textContent = `${d}j ${h}h ${m}m ${s}s`;
    };

    tick();
    setInterval(tick, 1000);
  };

  const initAjaxResults = () => {
    document.addEventListener('submit', async (e) => {
      const form = e.target.closest('form[data-ajax="results"]');
      if (!form) return;

      const panel = form.closest('.js-course-results');
      if (!panel) return;

      e.preventDefault();
      const btn = $('button', form);
      if (btn) btn.disabled = true;

      try {
        const res = await fetch(form.action, {
          method: form.method || 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: new FormData(form),
        });
        const html = await res.text();
        if (html.trim()) {
          panel.outerHTML = html;
          addRgpdNotices();
        }
      } finally {
        if (btn) btn.disabled = false;
      }
    });
  };

  const addRgpdNotices = (root = document) => {
    $$('form', root).forEach((form) => {
      if (form.dataset.rgpdNotice === '1') return;

      const note = document.createElement('p');
      note.className = 'rgpd-note';
      note.textContent = rgpdText;
      form.appendChild(note);
      form.dataset.rgpdNotice = '1';
    });
  };

  const initCookieBanner = () => {
    const banner = $('#cookie-banner');
    const button = $('#cookie-accept');
    if (!banner || !button) return;

    const key = 'tpformula1_cookie_ok';
    const readCookie = () => document.cookie.split('; ').some((item) => item === `${key}=1`);
    const readConsent = () => {
      try {
        if (window.localStorage.getItem(key) === '1') return true;
      } catch (_) {
        // localStorage can be blocked by browser privacy settings.
      }
      return readCookie();
    };

    const saveConsent = () => {
      try {
        window.localStorage.setItem(key, '1');
      } catch (_) {
        // Ignore storage errors.
      }
      const secure = window.location.protocol === 'https:' ? '; Secure' : '';
      document.cookie = `${key}=1; Max-Age=31536000; Path=/; SameSite=Lax${secure}`;
    };

    if (!readConsent()) {
      banner.hidden = false;
    }

    button.addEventListener('click', () => {
      banner.hidden = true;
      saveConsent();
    });
  };

  const initModal = () => {
    const modal = $('#login-modal');
    if (!modal) return;

    const redirectInput = $('.js-modal-redirect', modal);
    const registerLink = $('.js-modal-register', modal);
    const emailInput = $('input[name="email"]', modal);
    const registerBase = modal.dataset.registerBase || '/auth/register';
    const defaultRedirect = modal.dataset.defaultRedirect || '/accueil';

    const fallback = () => `${window.location.pathname || ''}${window.location.search || ''}${window.location.hash || ''}` || defaultRedirect;
    const safe = (v) => {
      const value = (v || '').trim();
      const isLocal = value.startsWith('?') || value.startsWith('/');
      return isLocal && !/[\r\n]/.test(value) ? value : fallback();
    };

    const close = () => {
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('modal-open');
    };

    document.addEventListener('click', (e) => {
      const opener = e.target.closest('[data-open-modal="login"]');
      if (opener) {
        e.preventDefault();
        const redirect = safe(opener.dataset.redirect || fallback());
        if (redirectInput) redirectInput.value = redirect;
        if (registerLink) registerLink.href = `${registerBase}?redirect=${encodeURIComponent(redirect)}`;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        if (emailInput) setTimeout(() => emailInput.focus(), 0);
        return;
      }

      if (e.target.closest('[data-close-modal]')) {
        e.preventDefault();
        close();
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal.classList.contains('is-open')) close();
    });
  };

  const initMobileMenu = () => {
    const wrap = $('.header-right');
    const toggle = $('.nav-toggle', wrap || document);
    if (!wrap || !toggle) return;

    const media = window.matchMedia('(max-width: 720px)');
    const setOpen = (open) => {
      wrap.classList.toggle('is-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.textContent = open ? 'Fermer' : 'Menu';
    };

    const onMedia = () => !media.matches && setOpen(false);
    toggle.addEventListener('click', () => setOpen(!wrap.classList.contains('is-open')));
    if (typeof media.addEventListener === 'function') media.addEventListener('change', onMedia);
    else if (typeof media.addListener === 'function') media.addListener(onMedia);
    wrap.addEventListener('click', (e) => media.matches && e.target.closest('.main-nav .nav-link') && setOpen(false));
    setOpen(false);
  };

  document.addEventListener('DOMContentLoaded', () => {
    const safeRun = (fn) => {
      try {
        fn();
      } catch (err) {
        console.error(err);
      }
    };

    safeRun(initCookieBanner);
    safeRun(addRgpdNotices);
    safeRun(initFilters);
    safeRun(initSort);
    safeRun(initCountdown);
    safeRun(initAjaxResults);
    safeRun(initModal);
    safeRun(initMobileMenu);
  });
})();
