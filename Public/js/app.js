(() => {
  const $ = (selector, scope = document) => scope.querySelector(selector);
  const $$ = (selector, scope = document) => Array.from(scope.querySelectorAll(selector));

  const setPageLoaded = () => {
    document.body.classList.add('page-loaded');
  };

  const initReveal = () => {
    const selectors = [
      '.dashboard-head',
      '.card',
      '.panel',
      '.gp-card',
      '.pill',
      '.list-item',
      '.calendar-card',
      '.results-panel',
      '.points-table',
      'table',
      '.auth-card',
    ];
    const elements = [];
    selectors.forEach((selector) => {
      $$(selector).forEach((el) => {
        if (!elements.includes(el)) {
          elements.push(el);
        }
      });
    });

    if (!elements.length) {
      return;
    }

    if (!('IntersectionObserver' in window)) {
      elements.forEach((el) => el.classList.add('is-visible'));
      return;
    }

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15 }
    );

    elements.forEach((el, index) => {
      el.classList.add('reveal');
      const delay = (index % 6) * 80;
      el.style.transitionDelay = `${delay}ms`;
      observer.observe(el);
    });
  };

  const animateCounter = (el) => {
    const target = parseInt(el.dataset.target || '0', 10);
    if (!Number.isFinite(target) || target <= 0) {
      return;
    }
    const duration = 900;
    const start = performance.now();
    const tick = (now) => {
      const progress = Math.min((now - start) / duration, 1);
      const value = Math.round(target * progress);
      el.textContent = String(value);
      if (progress < 1) {
        requestAnimationFrame(tick);
      }
    };
    requestAnimationFrame(tick);
  };

  const initCounters = () => {
    const counters = $$('.card-value');
    if (!counters.length) {
      return;
    }
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            animateCounter(entry.target);
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.6 }
    );

    counters.forEach((el) => {
      const raw = el.textContent || '';
      const target = parseInt(raw.replace(/[^\d-]/g, ''), 10);
      if (!Number.isFinite(target)) {
        return;
      }
      el.dataset.target = String(target);
      el.textContent = '0';
      observer.observe(el);
    });
  };

  const initFilters = () => {
    $$('[data-filter-table]').forEach((input) => {
      const tableId = input.dataset.filterTable;
      if (!tableId) {
        return;
      }
      const table = document.getElementById(tableId);
      if (!table || !table.tBodies.length) {
        return;
      }
      const countId = input.dataset.filterCount;
      const countEl = countId ? document.getElementById(countId) : null;
      const rows = Array.from(table.tBodies[0].rows);

      const update = () => {
        const query = (input.value || '').trim().toLowerCase();
        let visible = 0;
        rows.forEach((row) => {
          const text = row.textContent.toLowerCase();
          const match = query === '' || text.includes(query);
          row.style.display = match ? '' : 'none';
          if (match) {
            visible += 1;
          }
        });
        if (countEl) {
          countEl.textContent = `${visible} / ${rows.length}`;
        }
      };

      input.addEventListener('input', update);
      update();
    });
  };

  const parseCellValue = (text) => {
    const trimmed = text.trim();
    if (!trimmed) {
      return { type: 'empty', value: '' };
    }
    const numeric = Number(trimmed.replace(/[^0-9.-]/g, ''));
    if (Number.isFinite(numeric) && /[0-9]/.test(trimmed)) {
      return { type: 'number', value: numeric };
    }
    if (/-|\//.test(trimmed)) {
      const date = Date.parse(trimmed);
      if (!Number.isNaN(date)) {
        return { type: 'date', value: date };
      }
    }
    return { type: 'string', value: trimmed.toLowerCase() };
  };

  const initSortableTables = () => {
    $$('table[data-sortable="true"]').forEach((table) => {
      const headers = Array.from(table.tHead?.rows[0]?.cells || []);
      const tbody = table.tBodies[0];
      if (!headers.length || !tbody) {
        return;
      }
      headers.forEach((th, index) => {
        if (th.dataset.sort === 'false') {
          return;
        }
        th.classList.add('sortable');
        th.tabIndex = 0;
        th.setAttribute('role', 'button');
        const sort = () => {
          const isAsc = th.classList.contains('is-asc');
          headers.forEach((header) => {
            header.classList.remove('is-asc', 'is-desc');
            header.removeAttribute('aria-sort');
          });
          th.classList.add(isAsc ? 'is-desc' : 'is-asc');
          th.setAttribute('aria-sort', isAsc ? 'descending' : 'ascending');

          const rows = Array.from(tbody.rows).map((row, rowIndex) => {
            const cellText = row.cells[index]?.textContent || '';
            return {
              row,
              index: rowIndex,
              value: parseCellValue(cellText),
            };
          });

          rows.sort((a, b) => {
            const aVal = a.value.value;
            const bVal = b.value.value;
            let cmp = 0;
            if (a.value.type === b.value.type) {
              if (aVal < bVal) cmp = -1;
              if (aVal > bVal) cmp = 1;
            } else {
              const aStr = String(aVal);
              const bStr = String(bVal);
              if (aStr < bStr) cmp = -1;
              if (aStr > bStr) cmp = 1;
            }
            if (cmp === 0) {
              cmp = a.index - b.index;
            }
            return isAsc ? -cmp : cmp;
          });

          rows.forEach(({ row }) => tbody.appendChild(row));
        };

        th.addEventListener('click', sort);
        th.addEventListener('keydown', (event) => {
          if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            sort();
          }
        });
      });
    });
  };

  const initCalendarExtras = () => {
    const cards = $$('.calendar-card[data-date]');
    if (!cards.length) {
      return;
    }
    const now = new Date();
    let next = null;
    cards.forEach((card) => {
      const dateStr = card.dataset.date;
      if (!dateStr) {
        return;
      }
      const date = new Date(`${dateStr}T00:00:00`);
      if (Number.isNaN(date.getTime())) {
        return;
      }
      if (date > now && (!next || date < next.date)) {
        next = {
          card,
          date,
          name: card.dataset.name || '',
          location: card.dataset.location || '',
        };
      }
    });

    if (!next) {
      return;
    }

    next.card.classList.add('is-next');
    const nameEl = $('.js-next-name');
    const metaEl = $('.js-next-meta');
    const countdownEl = $('.js-next-countdown');

    if (nameEl) {
      nameEl.textContent = next.name;
    }
    if (metaEl) {
      metaEl.textContent = next.location;
    }

    const updateCountdown = () => {
      const diff = next.date.getTime() - Date.now();
      if (!countdownEl) {
        return;
      }
      if (diff <= 0) {
        countdownEl.textContent = 'Course en cours';
        return;
      }
      const totalSeconds = Math.floor(diff / 1000);
      const days = Math.floor(totalSeconds / 86400);
      const hours = Math.floor((totalSeconds % 86400) / 3600);
      const minutes = Math.floor((totalSeconds % 3600) / 60);
      const seconds = totalSeconds % 60;
      countdownEl.textContent = `${days}j ${hours}h ${minutes}m ${seconds}s`;
    };

    updateCountdown();
    setInterval(updateCountdown, 1000);
  };

  const initCourseAjax = () => {
    let container = $('.js-course-results');
    if (!container) {
      return;
    }

    const bindForms = () => {
      container.querySelectorAll('form[data-ajax="results"]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
          if (event.defaultPrevented) {
            return;
          }
          event.preventDefault();
          const submitBtn = form.querySelector('button');
          if (submitBtn) {
            submitBtn.disabled = true;
          }
          try {
            const response = await fetch(form.action, {
              method: form.method || 'POST',
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
              },
              body: new FormData(form),
            });
            const html = await response.text();
            if (html.trim()) {
              container.outerHTML = html;
              container = $('.js-course-results');
              if (container) {
                bindForms();
              }
            }
          } finally {
            if (submitBtn) {
              submitBtn.disabled = false;
            }
          }
        });
      });
    };

    bindForms();
  };

  const initLoginModal = () => {
    const modal = $('#login-modal');
    if (!modal) {
      return;
    }

    const redirectInput = $('.js-modal-redirect', modal);
    const registerLink = $('.js-modal-register', modal);
    const focusTarget = $('input[name="email"]', modal);

    const getDefaultRedirect = () => {
      const search = window.location.search || '';
      const hash = window.location.hash || '';
      if (search) {
        return `${search}${hash}`;
      }
      return '?route=accueil';
    };

    const sanitizeRedirect = (value) => {
      const raw = (value || '').trim();
      if (!raw || !raw.startsWith('?') || /[\r\n]/.test(raw)) {
        return getDefaultRedirect();
      }
      return raw;
    };

    const setRedirect = (value) => {
      const redirect = sanitizeRedirect(value);
      if (redirectInput) {
        redirectInput.value = redirect;
      }
      if (registerLink) {
        registerLink.setAttribute(
          'href',
          `?route=auth&action=register&redirect=${encodeURIComponent(redirect)}`
        );
      }
    };

    const openModal = (redirectValue) => {
      setRedirect(redirectValue || getDefaultRedirect());
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
      if (focusTarget) {
        setTimeout(() => focusTarget.focus(), 0);
      }
    };

    const closeModal = () => {
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('modal-open');
    };

    $$('[data-open-modal="login"]').forEach((trigger) => {
      trigger.addEventListener('click', (event) => {
        if (event.defaultPrevented) {
          return;
        }
        event.preventDefault();
        openModal(trigger.dataset.redirect || '');
      });
    });

    modal.querySelectorAll('[data-close-modal]').forEach((btn) => {
      btn.addEventListener('click', (event) => {
        event.preventDefault();
        closeModal();
      });
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && modal.classList.contains('is-open')) {
        closeModal();
      }
    });
  };

  document.addEventListener('DOMContentLoaded', () => {
    setPageLoaded();
    initReveal();
    initCounters();
    initFilters();
    initSortableTables();
    initCalendarExtras();
    initCourseAjax();
    initLoginModal();
  });
})();
