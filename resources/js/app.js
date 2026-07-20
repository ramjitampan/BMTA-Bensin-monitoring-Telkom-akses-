(function () {
  'use strict';

  /* ─── Dismiss Flash ─── */
  window.dismissFlash = function (el) {
    var flash = el ? el.closest('#flash') : document.getElementById('flash');
    if (!flash) return;
    flash.style.transition = 'opacity .35s ease, transform .35s ease, max-height .35s ease, margin .35s ease, padding .35s ease';
    flash.style.opacity = '0';
    flash.style.transform = 'translateX(16px)';
    flash.style.maxHeight = '0';
    flash.style.margin = '0';
    flash.style.padding = '0';
    setTimeout(function () { flash.remove(); }, 380);
  };
  var flashEl = document.getElementById('flash');
  if (flashEl) setTimeout(function () { dismissFlash(flashEl); }, 4500);

  /* ─── Live Search ─── */
  window.initSearch = function (config) {
    var input = document.getElementById(config.inputId || 'searchInput');
    var clearBtn = document.getElementById(config.clearId || 'clearSearch');
    var rows = config.rows || document.querySelectorAll((config.tableSelector || '#dataTable') + ' tbody tr');
    var cards = config.cards || null;
    var visibleCount = document.getElementById(config.countId || 'visibleCount');
    var noResult = document.getElementById(config.noResultId || 'noResult');
    var searchBadge = document.getElementById(config.badgeId || 'searchBadge');
    var searchBadgeText = document.getElementById(config.badgeTextId || 'searchBadgeText');
    var fields = config.fields || [];

    function run(kw) {
      var shown = 0;
      rows.forEach(function (r) {
        var hit = fields.every(function (f) { return r.dataset[f].includes(kw); });
        r.style.display = hit ? '' : 'none';
        if (hit) shown++;
      });
      if (cards) {
        cards.forEach(function (c) {
          var hit = fields.every(function (f) { return c.dataset[f].includes(kw); });
          c.style.display = hit ? '' : 'none';
        });
      }
      if (visibleCount) visibleCount.textContent = shown;
      if (noResult) noResult.classList.toggle('hidden', shown > 0);
      if (clearBtn) clearBtn.classList.toggle('hidden', !kw);
      if (searchBadge) {
        if (kw) {
          searchBadge.classList.remove('hidden');
          searchBadge.classList.add('inline-flex');
          if (searchBadgeText) searchBadgeText.textContent = '"' + kw + '" — ' + shown + ' hasil';
        } else {
          searchBadge.classList.add('hidden');
          searchBadge.classList.remove('inline-flex');
        }
      }
    }

    if (input) {
      input.addEventListener('input', function (e) { run(e.target.value.toLowerCase().trim()); });
    }
    if (clearBtn) {
      clearBtn.addEventListener('click', function () {
        if (input) { input.value = ''; input.focus(); }
        run('');
      });
    }
    return run;
  };

  /* ─── Delete Modal ─── */
  var pendingForm = null;

  window.confirmDelete = function (btn, name) {
    pendingForm = btn.closest('form');
    var nameEl = document.getElementById('deleteName');
    if (nameEl) nameEl.textContent = name;
    var modal = document.getElementById('deleteModal');
    if (modal) modal.classList.add('open');
  };

  window.closeModal = function () {
    var box = document.getElementById('modalBox');
    if (box) {
      box.style.transform = 'scale(.9) translateY(16px)';
      box.style.opacity = '0';
    }
    setTimeout(function () {
      var modal = document.getElementById('deleteModal');
      if (modal) modal.classList.remove('open');
      if (box) { box.style.transform = ''; box.style.opacity = ''; }
    }, 260);
    pendingForm = null;
  };

  var confirmBtn = document.getElementById('confirmBtn');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', function () {
      if (!pendingForm) return;
      var btn = confirmBtn;
      btn.disabled = true;
      btn.innerHTML = '<svg class="w-4 h-4 animate-spin inline mr-1.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>Menghapus\u2026';
      pendingForm.submit();
    });
  }

  var deleteModal = document.getElementById('deleteModal');
  if (deleteModal) {
    deleteModal.addEventListener('click', function (e) {
      if (e.target === e.currentTarget) closeModal();
    });
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
  });

  /* ─── Entrance Stagger ─── */
  window.initEntrance = function (selector, baseDelay, rowDelay) {
    baseDelay = baseDelay || 80;
    rowDelay = rowDelay || 50;
    var els = document.querySelectorAll(selector);
    els.forEach(function (el, i) {
      el.style.transition = 'opacity .3s ease, transform .3s cubic-bezier(.22,1,.36,1)';
      setTimeout(function () {
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
        el.classList.remove('row-hidden');
      }, baseDelay + i * rowDelay);
    });
  };

  /* ─── Stat Counter ─── */
  window.initCounter = function (id) {
    var el = document.getElementById(id);
    if (!el) return;
    var target = parseInt(el.textContent) || 0;
    if (target <= 1) return;
    var current = 0;
    var step = Math.ceil(target / 20);
    var timer = setInterval(function () {
      current = Math.min(current + step, target);
      el.textContent = current;
      if (current >= target) clearInterval(timer);
    }, 40);
  };

  /* ─── Observing Count-Up (for perjalanan-style) ─── */
  window.observeCounters = function () {
    function countUp(el, end, isRp) {
      var dur = 800, t0 = performance.now();
      (function step(now) {
        var p = Math.min((now - t0) / dur, 1);
        var e = 1 - Math.pow(1 - p, 3);
        var v = Math.round(end * e);
        el.textContent = isRp ? 'Rp ' + v.toLocaleString('id-ID') : v.toLocaleString('id-ID');
        if (p < 1) requestAnimationFrame(step);
      })(t0);
    }
    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var card = entry.target;
        var ce = card.querySelector('[data-count]');
        var rpe = card.querySelector('[data-count-rp]');
        if (ce) countUp(ce, parseInt(ce.dataset.count), false);
        if (rpe) countUp(rpe, parseInt(rpe.dataset.countRp), true);
        obs.unobserve(card);
      });
    }, { threshold: 0.2 });
    document.querySelectorAll('.stat-card-observe').forEach(function (c) { obs.observe(c); });
  };

})();
