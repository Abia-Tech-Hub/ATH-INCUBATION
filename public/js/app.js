
// Abia Tech Hub Incubation's own code, as Anticode's model wrote it. It runs once, in the browser, when the page is on screen
// (components/AppRuntime.tsx). `anticode.content` is content/collections.json, and `anticode.send(form, fields)` posts
// to this project's routes in app/api/. It's plain JavaScript in a TypeScript file: add types as you change it.
function start() {
document.documentElement.classList.add('js');

(() => {
  'use strict';

  /* ---------- Constants ---------- */
  const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const DRAFT_KEY = 'ath-incubation-application-v1';
  const COLORS = { deep:'#0B334D', forest:'#0B334D', mid:'#164967', leaf:'#64748B', lime:'#543C9E', mint:'#28556D' };
  const persistentEvents = new AbortController();
  let pageEvents = new AbortController();
  let activeNavigation = null;
  const pageObservers = new Set();

  function observePage(callback, options) {
    const observer = new IntersectionObserver(callback, options);
    pageObservers.add(observer);
    return observer;
  }

  const STEPS = [
    { id:1, name:'About you', title:'About you', desc:'Start with your details so the team knows who you are and how to reach you.' },
    { id:2, name:'Startup', title:'Your startup', desc:'Tell us what you are building. Plain, specific answers work best.' },
    { id:3, name:'Mentorship', title:'Mentorship needs', desc:'Help the team understand where you want support during the 6 weeks.' },
    { id:4, name:'Review', title:'Review and send', desc:'Check your answers. You can edit any section before sending.' }
  ];

  const MENTOR_AREAS = [
    'Validating the idea','Finding customers','Product and technology','Business model and pricing',
    'Branding and marketing','Pitching','Legal and registration','Building a team','Managing money'
  ];

  const FIELD_LABELS = {
    full_name:'Full name', email:'Email', phone:'Phone', institution:'Institution', course:'Course or department', year:'Year of study',
    startup_name:'Startup name', sector:'Sector', stage:'Stage', problem:'Problem', solution:'Solution', team_size:'Team size', link:'Link',
    mentor_areas:'Mentorship areas', goal:'Goal for the 6 weeks', commitment:'Availability', heard:'Heard about it from'
  };

  const MULTI = new Set(['mentor_areas']);
  const STEP_FIELDS = {
    1:['full_name','email','phone','institution','course','year'],
    2:['startup_name','sector','stage','problem','solution','team_size','link'],
    3:['mentor_areas','goal','commitment','heard','consent']
  };

  /* ---------- State ---------- */
  const state = {
    step: 1,
    maxStep: 1,
    sending: false,
    sent: false,
    elig: { student:null, abia:null },
    journey: 0
  };

  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
  const CHECK_SVG = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4.5 4.5L19 7"/></svg>';
  const ERR_SVG = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>';

  /* ---------- Header, nav, sticky CTA ---------- */
  function initHeader() {
    const header = $('#site-header');
    const menuBtn = $('#menu-btn');
    const mobileNav = $('#mobile-nav');
    if (!header || !menuBtn || !mobileNav) return;
    const onScroll = () => header.classList.toggle('is-solid', header.dataset.solid === 'true' || window.scrollY > 24 || mobileNav.classList.contains('is-open'));
    window.addEventListener('scroll', onScroll, { passive:true, signal:persistentEvents.signal });
    onScroll();

    menuBtn.addEventListener('click', () => {
      const open = !mobileNav.classList.contains('is-open');
      mobileNav.classList.toggle('is-open', open);
      menuBtn.setAttribute('aria-expanded', String(open));
      menuBtn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      onScroll();
    });
    const closeMenu = () => {
      mobileNav.classList.remove('is-open');
      menuBtn.setAttribute('aria-expanded', 'false');
      menuBtn.setAttribute('aria-label', 'Open menu');
      onScroll();
    };
    $$('a', mobileNav).forEach(a => a.addEventListener('click', closeMenu));
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && mobileNav.classList.contains('is-open')) { closeMenu(); menuBtn.focus(); }
    }, { signal:persistentEvents.signal });

    updateCurrentRoute();
    const links = $$('.nav a[href^="#"]');
    const sections = links.map(a => $(a.getAttribute('href'))).filter(Boolean);
    const navObs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          links.forEach(l => l.classList.toggle('is-current', l.getAttribute('href') === '#' + e.target.id));
        }
      });
    }, { rootMargin:'-45% 0px -50% 0px' });
    sections.forEach(s => navObs.observe(s));
  }

  function updateCurrentRoute() {
    $$('.nav a[href^="/"], .mobile-nav a[href^="/"]').forEach(link => {
      const isCurrent = new URL(link.href).pathname === location.pathname;
      link.classList.toggle('is-current', isCurrent);
      if (isCurrent) link.setAttribute('aria-current', 'page');
      else link.removeAttribute('aria-current');
    });
  }

  function initStickyApply() {
    const bar = $('#sticky-apply');
    const hero = $('#top');
    const apply = $('#apply');
    const footer = $('.site-footer');
    if (!bar || !hero || !apply || !footer) return;
    let heroVisible = true, applyVisible = false, footerVisible = false;
    const update = () => bar.classList.toggle('is-visible', !heroVisible && !applyVisible && !footerVisible);
    observePage(([e]) => { heroVisible = e.isIntersecting; update(); }, { threshold:0.15 }).observe(hero);
    observePage(([e]) => { applyVisible = e.isIntersecting; update(); }, { threshold:0.05 }).observe(apply);
    observePage(([e]) => { footerVisible = e.isIntersecting; update(); }, { threshold:0 }).observe(footer);
  }

  function initReveal() {
    const items = $$('.reveal, #chain, .impact-card');
    if (REDUCED || !('IntersectionObserver' in window)) { items.forEach(i => i.classList.add('in')); return; }
    const obs = observePage(entries => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); obs.unobserve(e.target); } });
    }, { threshold:0.14 });
    items.forEach(i => obs.observe(i));
  }

  /* ---------- Number Reading / Animated Counters (inspired by youngafricainnovates.ng) ---------- */
  function initCounters() {
    const counterEls = $$('[data-counter]');
    if (!counterEls.length) return;

    if (REDUCED || !('IntersectionObserver' in window)) {
      counterEls.forEach(el => {
        const target = parseFloat(el.getAttribute('data-counter')) || 0;
        const prefix = el.getAttribute('data-prefix') || '';
        const suffix = el.getAttribute('data-suffix') || '';
        const decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
        el.textContent = `${prefix}${target.toLocaleString('en-US', {
          minimumFractionDigits: decimals,
          maximumFractionDigits: decimals
        })}${suffix}`;
      });
      return;
    }

    const easeOutExpo = t => (t === 1 ? 1 : 1 - Math.pow(2, -10 * t));

    const animateCounter = el => {
      if (el.dataset.counted === 'true') return;
      el.dataset.counted = 'true';

      const target = parseFloat(el.getAttribute('data-counter')) || 0;
      const prefix = el.getAttribute('data-prefix') || '';
      const suffix = el.getAttribute('data-suffix') || '';
      const duration = parseInt(el.getAttribute('data-duration') || '1600', 10);
      const decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
      const startVal = 0;
      const startTime = performance.now();

      const frame = currentTime => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const eased = easeOutExpo(progress);
        const current = startVal + (target - startVal) * eased;

        el.textContent = `${prefix}${current.toLocaleString('en-US', {
          minimumFractionDigits: decimals,
          maximumFractionDigits: decimals
        })}${suffix}`;

        if (progress < 1) {
          requestAnimationFrame(frame);
        } else {
          el.textContent = `${prefix}${target.toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
          })}${suffix}`;
        }
      };

      requestAnimationFrame(frame);
    };

    const counterObs = observePage(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          counterObs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.2 });

    counterEls.forEach(el => {
      const prefix = el.getAttribute('data-prefix') || '';
      const suffix = el.getAttribute('data-suffix') || '';
      const decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
      if (el.dataset.counted !== 'true') {
        el.textContent = `${prefix}${(0).toLocaleString('en-US', {
          minimumFractionDigits: decimals,
          maximumFractionDigits: decimals
        })}${suffix}`;
      }
      counterObs.observe(el);
    });
  }

  function initPageTransition() {
    const main = $('#main');
    if (!main || REDUCED) return;

    main.classList.remove('page-enter');
    void main.offsetWidth;
    main.classList.add('page-enter');

    const clearTransition = event => {
      if (event.target !== main || event.animationName !== 'pageEnter') return;
      main.classList.remove('page-enter');
      main.removeEventListener('animationend', clearTransition);
    };
    main.addEventListener('animationend', clearTransition, { signal:pageEvents.signal });
  }

  /* ---------- Hero weave: a lattice of diamonds in chevron bands that ripples toward the pointer ---------- */
  const Weave = {
    canvas:null, ctx:null, cells:[], w:0, h:0, dpr:1, running:false, raf:0,
    pointer:{ x:-9999, y:-9999, strength:0, target:0 },

    init(canvas) {
      if (!canvas) return;
      this.canvas = canvas;
      this.ctx = canvas.getContext('2d');
      if (!this.ctx) return;
      this.resize();
      window.addEventListener('resize', () => {
        clearTimeout(this.resizeTimer);
        this.resizeTimer = setTimeout(() => this.resize(), 120);
      }, { signal:pageEvents.signal });
      const hero = canvas.parentElement;
      hero.addEventListener('pointermove', e => {
        const r = canvas.getBoundingClientRect();
        this.pointer.x = e.clientX - r.left;
        this.pointer.y = e.clientY - r.top;
        this.pointer.target = 1;
        if (REDUCED) this.draw(0);
      });
      hero.addEventListener('pointerleave', () => { this.pointer.target = 0; if (REDUCED) { this.pointer.strength = 0; this.draw(0); } });
      if (REDUCED) { this.draw(0); return; }
      observePage(([e]) => e.isIntersecting ? this.start() : this.stop()).observe(canvas);
      document.addEventListener('visibilitychange', () => document.hidden ? this.stop() : this.start(), { signal:pageEvents.signal });
    },

    resize() {
      const r = this.canvas.getBoundingClientRect();
      this.dpr = Math.min(window.devicePixelRatio || 1, 2);
      this.w = r.width; this.h = r.height;
      this.canvas.width = Math.round(this.w * this.dpr);
      this.canvas.height = Math.round(this.h * this.dpr);
      this.ctx.setTransform(this.dpr, 0, 0, this.dpr, 0, 0);
      this.build();
      if (REDUCED || !this.running) this.draw(0);
    },

    build() {
      const s = this.w < 700 ? 26 : 34;
      this.size = s;
      const palette = [COLORS.deep, COLORS.forest, COLORS.mid, COLORS.forest, COLORS.mint, COLORS.forest, COLORS.leaf, COLORS.mid];
      this.cells = [];
      const rows = Math.ceil(this.h / (s / 2)) + 2;
      const cols = Math.ceil(this.w / s) + 2;
      for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
          const x = c * s + (r % 2 ? s / 2 : 0);
          const y = r * s / 2;
          const u = c * 2 + (r % 2);
          // chevron bands like woven cloth
          const chev = Math.abs((u % 24) - 12);
          const band = Math.floor((r + chev) / 3) % palette.length;
          const isLime = band === 6 && ((u + r) % 4 === 0);
          this.cells.push({ x, y, color: isLime ? COLORS.lime : palette[band], lime:isLime, phase:(u * 0.37 + r * 0.53) % (Math.PI * 2) });
        }
      }
    },

    start() { if (this.running || REDUCED) return; this.running = true; const loop = t => { this.draw(t / 1000); this.raf = requestAnimationFrame(loop); }; this.raf = requestAnimationFrame(loop); },
    stop() { this.running = false; cancelAnimationFrame(this.raf); },

    draw(t) {
      const { ctx, cells, size } = this;
      const p = this.pointer;
      p.strength += (p.target - p.strength) * 0.06;
      ctx.fillStyle = COLORS.deep;
      ctx.fillRect(0, 0, this.w, this.h);
      const half = size / 2;
      for (let i = 0; i < cells.length; i++) {
        const c = cells[i];
        const breathe = REDUCED ? 0 : Math.sin(t * 0.9 + c.phase) * 0.07;
        let scale = 0.82 + breathe;
        let glow = 0;
        if (p.strength > 0.01) {
          const dx = c.x - p.x, dy = c.y - p.y;
          const d = Math.sqrt(dx * dx + dy * dy);
          const ripple = Math.exp(-d / 150) * p.strength;
          const ring = REDUCED ? 0 : Math.max(0, Math.sin(d / 26 - t * 4)) * Math.exp(-d / 220) * p.strength;
          scale += ripple * 0.18 - ring * 0.12;
          glow = Math.max(ripple * 0.85, ring * 0.35);
        }
        // a slow diagonal sweep of light
        if (!REDUCED) {
          const sweep = Math.sin((c.x + c.y) / 260 - t * 0.7);
          if (sweep > 0.94) glow = Math.max(glow, (sweep - 0.94) * 8 * 0.5);
        }
        const h = half * scale;
        ctx.beginPath();
        ctx.moveTo(c.x, c.y - h); ctx.lineTo(c.x + h, c.y); ctx.lineTo(c.x, c.y + h); ctx.lineTo(c.x - h, c.y);
        ctx.closePath();
        ctx.fillStyle = c.color;
        ctx.fill();
        if (glow > 0.03 && !c.lime) {
          ctx.globalAlpha = Math.min(glow, 0.9);
          ctx.fillStyle = COLORS.lime;
          ctx.fill();
          ctx.globalAlpha = 1;
        }
      }
    }
  };

  /* ---------- How it works: scroll-driven 6-week journey ---------- */
  const Journey = {
    tris:[], weeksEls:[],
    init() {
      this.tris = [];
      this.weeksEls = [];
      this.ticking = false;
      const svg = $('#journey-svg');
      if (!svg) return;
      const ns = 'http://www.w3.org/2000/svg';
      const C = [300, 190], R = 150;
      const P = [[300,40],[375,115],[450,190],[375,265],[300,340],[225,265],[150,190],[225,115]];
      const fills = [COLORS.lime, COLORS.leaf, '#FFFFFF', COLORS.lime, COLORS.leaf, '#FFFFFF', COLORS.lime, COLORS.mid];
      // faint outline of the finished shape
      const ghost = document.createElementNS(ns, 'polygon');
      ghost.setAttribute('points', P.map(p => p.join(',')).join(' '));
      ghost.setAttribute('fill', 'none');
      ghost.setAttribute('stroke', 'rgba(255,255,255,.14)');
      ghost.setAttribute('stroke-dasharray', '4 8');
      svg.appendChild(ghost);
      for (let k = 0; k < 8; k++) {
        const a = P[k], b = P[(k + 1) % 8];
        const poly = document.createElementNS(ns, 'polygon');
        poly.setAttribute('points', `${C[0]},${C[1]} ${a[0]},${a[1]} ${b[0]},${b[1]}`);
        poly.setAttribute('stroke', COLORS.lime);
        poly.setAttribute('stroke-width', '2');
        poly.setAttribute('stroke-linejoin', 'round');
        const cx = (C[0] + a[0] + b[0]) / 3, cy = (C[1] + a[1] + b[1]) / 3;
        const ang = (k * 45 + 22.5 - 90) * Math.PI / 180;
        const dist = 95 + (k % 3) * 28;
        svg.appendChild(poly);
        this.tris.push({ el:poly, cx, cy, dx:Math.cos(ang) * dist, dy:Math.sin(ang) * dist * 0.7, rot:(k % 2 ? 1 : -1) * (70 + k * 12), fill:fills[k] });
      }
      void R;
      const weeks = $('#weeks');
      for (let i = 1; i <= 6; i++) {
        const li = document.createElement('li');
        li.className = 'week';
        li.innerHTML = '<i></i><span><span class="lw">Wk </span>' + i + '</span>';
        weeks.appendChild(li);
        this.weeksEls.push(li);
      }
      this.section = $('#how');
      this.track = $('#how-track');
      this.visual = $('.journey');
      this.phases = $$('#phases .phase');
      this.mq = window.matchMedia('(min-width: 900px) and (min-height: 640px)');
      this.applyMode();
      if (this.mq.addEventListener) {
        this.mq.addEventListener('change', () => this.applyMode(), { signal:pageEvents.signal });
      } else {
        const onChange = () => this.applyMode();
        this.mq.addListener(onChange);
        pageEvents.signal.addEventListener('abort', () => this.mq.removeListener(onChange), { once:true });
      }
      window.addEventListener('scroll', () => this.onScroll(), { passive:true, signal:pageEvents.signal });
      window.addEventListener('resize', () => this.onScroll(), { signal:pageEvents.signal });
      this.onScroll();
    },
    applyMode() {
      this.scrolly = this.mq.matches && !REDUCED;
      this.section.classList.toggle('is-scrolly', this.scrolly);
      this.onScroll();
    },
    onScroll() {
      if (this.ticking) return;
      this.ticking = true;
      this.raf = requestAnimationFrame(() => {
        this.ticking = false;
        let p;
        const vh = window.innerHeight;
        if (REDUCED) p = 1;
        else if (this.scrolly) {
          const r = this.track.getBoundingClientRect();
          p = clamp(-r.top / Math.max(1, r.height - vh));
        } else {
          const r = this.visual.getBoundingClientRect();
          p = clamp((vh * 0.9 - r.top) / (vh * 0.75));
        }
        this.render(p);
      });
    },
    render(p) {
      state.journey = p;
      const shapeProgress = p * 8.6;
      const weekProgress = p * 6.6;
      this.tris.forEach((t, k) => {
        const raw = clamp(shapeProgress - k);
        const e = 1 - Math.pow(1 - raw, 3);
        const inv = 1 - e;
        t.el.setAttribute('transform', `translate(${(t.dx * inv).toFixed(1)} ${(t.dy * inv).toFixed(1)}) rotate(${(t.rot * inv).toFixed(1)} ${t.cx.toFixed(1)} ${t.cy.toFixed(1)}) `);
        t.el.setAttribute('fill', raw >= 1 ? t.fill : 'transparent');
        t.el.setAttribute('stroke-opacity', raw >= 1 ? '0' : (0.35 + 0.65 * e).toFixed(2));
        t.el.setAttribute('opacity', (0.55 + 0.45 * e).toFixed(2));
      });
      const done = Math.min(6, Math.floor(weekProgress));
      const current = Math.min(6, done + 1);
      this.weeksEls.forEach((w, i) => {
        w.classList.toggle('is-done', i < done);
        w.classList.toggle('is-current', i === done && done < 6);
      });
      $('#weeks-fill').style.width = (Math.min(1, weekProgress / 6) * 87.5) + '%';
      $('#week-count').innerHTML = done >= 6 ? 'Week 6<small> complete</small>' : 'Week ' + current + '<small> of 6</small>';
      const st = done >= 6 ? 'A validated business opportunity' : done >= 5 ? 'Taking shape' : done >= 2 ? 'Pieces coming together' : 'Your idea, in pieces';
      $('#journey-state').textContent = st;
      const phase = p < 0.06 ? 0 : p < 0.13 ? 1 : done < 6 ? 2 : 3;
      this.phases.forEach((el, i) => {
        el.classList.toggle('is-active', i === phase);
        el.classList.toggle('is-done', i < phase);
      });
    }
  };

  /* ---------- Eligibility checker ---------- */
  function initEligibility() {
    const result = $('#elig-result');
    if (!result) return;
    $$('.q-btn').forEach(btn => btn.addEventListener('click', () => {
      const q = btn.dataset.q;
      state.elig[q] = btn.dataset.v;
      $$('.q-btn[data-q="' + q + '"]').forEach(b => b.setAttribute('aria-pressed', String(b === btn)));
      renderEligibility(result);
    }));
  }

  function renderEligibility(result) {
    const { student, abia } = state.elig;
    result.className = 'elig-result';
    if (!student || !abia) return;
    if (student === 'yes' && abia === 'yes') {
      result.classList.add('is-yes');
      result.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg><p>You can apply.<small>Students at any Abia institution are welcome.</small></p><a class="btn btn-lime btn-sm" href="/apply">Start application</a>';
    } else {
      result.classList.add('is-no');
      const msg = student === 'no'
        ? '<p>This programme is for current students.<small>It\'s open to students at institutions in Abia State.</small></p>'
        : '<p>This programme is for Abia institutions.<small>It\'s open to students at institutions in Abia State.</small></p>';
      result.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>' + msg;
    }
  }

  /* ---------- Application form ---------- */
  let form = null;
  const els = {};

  function initForm() {
    form = $('#apply-form');
    if (!form) return;
    els.stepper = $('#stepper');
    els.title = $('#step-title');
    els.desc = $('#step-desc');
    els.back = $('#back-btn');
    els.next = $('#next-btn');
    els.nextLabel = $('#next-label');
    els.status = $('#form-status');
    els.draftNote = $('#draft-note');
    els.restore = $('#restore');

    // mentorship area chips
    const chips = $('#area-chips');
    MENTOR_AREAS.forEach((area, i) => {
      const d = document.createElement('div');
      d.className = 'chip';
      const input = document.createElement('input');
      input.type = 'checkbox'; input.name = 'mentor_areas'; input.id = 'ma-' + i; input.value = area;
      const label = document.createElement('label');
      label.htmlFor = input.id;
      label.innerHTML = CHECK_SVG;
      label.appendChild(document.createTextNode(area));
      d.append(input, label);
      chips.appendChild(d);
    });

    renderStepper();

    // character counters
    ['problem','solution','goal'].forEach(name => {
      const ta = form.elements[name];
      const cnt = $('#cnt-' + name);
      const upd = () => { cnt.textContent = ta.value.length + ' / ' + ta.maxLength; };
      ta.addEventListener('input', upd);
      ta._updateCounter = upd;
    });

    form.addEventListener('submit', e => {
      e.preventDefault();
      if (state.sending) return;
      if (state.step < 4) goNext();
      else submitApplication();
    });
    els.back.addEventListener('click', () => goTo(state.step - 1));

    // live-clear errors and autosave
    let saveT;
    const onChange = e => {
      const name = e.target.name;
      if (name && e.target.closest('[aria-invalid="true"], .field')) {
        const err = $('#err-' + name);
        if (err && err.textContent) validateField(name);
      }
      clearTimeout(saveT);
      saveT = setTimeout(saveDraft, 450);
    };
    form.addEventListener('input', onChange);
    form.addEventListener('change', onChange);

    $('#clear-draft').addEventListener('click', () => {
      try { localStorage.removeItem(DRAFT_KEY); } catch (_) {}
      form.reset();
      ['problem','solution','goal'].forEach(n => form.elements[n]._updateCounter());
      $$('.err', form).forEach(e => { e.textContent = ''; });
      $$('[aria-invalid]', form).forEach(e => e.removeAttribute('aria-invalid'));
      state.maxStep = 1;
      els.restore.hidden = true;
      els.draftNote.textContent = '';
      goTo(1, true);
    });

    restoreDraft();
    showStep(false);
  }

  function renderStepper() {
    els.stepper.innerHTML = '';
    STEPS.forEach(s => {
      const li = document.createElement('li');
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'step-btn';
      if (s.id === state.step) { b.classList.add('is-active'); b.setAttribute('aria-current', 'step'); }
      else if (s.id < state.step) b.classList.add('is-done');
      b.disabled = s.id > state.maxStep || s.id === state.step;
      const doneIcon = s.id < state.step ? CHECK_SVG : '';
      b.innerHTML = '<span class="step-bar"></span><span class="step-name">' + doneIcon + '<span class="sn-num">' + s.id + '</span><span class="sn-text">. ' + s.name + '</span></span>';
      b.setAttribute('aria-label', 'Step ' + s.id + ' of 4: ' + s.name + (s.id < state.step ? ', completed' : s.id === state.step ? ', current' : ''));
      b.addEventListener('click', () => goTo(s.id));
      b.className = b.className.trim();
      li.appendChild(b);
      els.stepper.appendChild(li);
    });
  }

  function showStep(focus) {
    const s = STEPS[state.step - 1];
    form.dataset.step = String(state.step);
    for (let i = 1; i <= 4; i++) $('#step-' + i).hidden = i !== state.step;
    els.title.textContent = s.title;
    els.desc.textContent = s.desc;
    els.back.hidden = state.step === 1;
    els.nextLabel.textContent = state.step === 3 ? 'Review application' : state.step === 4 ? 'Submit application' : 'Continue';
    els.next.classList.toggle('btn-lime', state.step === 4);
    els.next.classList.toggle('btn-forest', state.step !== 4);
    els.status.hidden = true;
    if (state.step === 4) renderReview();
    renderStepper();
    if (focus) {
      els.title.focus({ preventScroll:true });
      const card = $('#form-card');
      const top = card.getBoundingClientRect().top;
      if (top < 60 || top > window.innerHeight * 0.5) {
        window.scrollTo({ top: window.scrollY + top - 96, behavior: REDUCED ? 'auto' : 'smooth' });
      }
    }
  }

  function goTo(step, focus = true) {
    if (step < 1 || step > 4 || step > state.maxStep) return;
    state.step = step;
    showStep(focus);
    saveDraft();
  }

  function goNext() {
    const fields = STEP_FIELDS[state.step];
    const firstBad = fields.map(f => validateField(f) ? null : f).find(Boolean);
    if (firstBad) { focusField(firstBad); return; }
    state.step += 1;
    state.maxStep = Math.max(state.maxStep, state.step);
    showStep(true);
    saveDraft();
  }

  function focusField(name) {
    const el = form.elements[name];
    const target = el && (el.length && !el.tagName ? el[0] : el);
    if (target && target.focus) target.focus();
  }

  /* Validation: returns true when valid, writes the error otherwise */
  function getValue(name) {
    const el = form.elements[name];
    if (!el) return '';
    if (MULTI.has(name)) return $$('input[name="' + name + '"]:checked', form).map(i => i.value);
    if (el.type === 'checkbox') return el.checked ? 'yes' : '';
    if (el.length && !el.tagName) { const c = $$('input[name="' + name + '"]', form).find(i => i.checked); return c ? c.value : ''; }
    return (el.value || '').trim();
  }

  function errorFor(name, v) {
    switch (name) {
      case 'full_name': return v.length < 2 ? 'Enter your full name' : '';
      case 'email': return !v ? 'Enter your email address' : !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v) ? 'Enter a valid email address, with an @ and a domain' : '';
      case 'phone': { const digits = v.replace(/\D/g, ''); return !v ? 'Enter your phone number' : (!/^[+\d\s()-]+$/.test(v) || digits.length < 7 || digits.length > 15) ? 'Enter a valid phone number' : ''; }
      case 'institution': return !v ? 'Choose the institution where you study' : '';
      case 'course': return !v ? 'Enter your course or department' : '';
      case 'year': return !v ? 'Choose your year of study' : '';
      case 'startup_name': return !v ? 'Give your startup a name (a working name is fine)' : '';
      case 'sector': return !v ? 'Choose the sector that fits best' : '';
      case 'stage': return !v ? 'Choose how far along you are' : '';
      case 'problem': return v.length < 20 ? 'Describe the problem in a sentence or two (at least 20 characters)' : '';
      case 'solution': return v.length < 20 ? 'Describe your solution in a sentence or two (at least 20 characters)' : '';
      case 'team_size': return !v ? 'Choose your team size' : '';
      case 'link': return '';
      case 'mentor_areas': return v.length === 0 ? 'Choose at least one area' : '';
      case 'goal': return v.length < 20 ? 'Tell us what you hope to achieve (at least 20 characters)' : '';
      case 'commitment': return !v ? 'Let us know your availability' : '';
      case 'heard': return '';
      case 'consent': return !v ? 'Please confirm to continue' : '';
      default: return '';
    }
  }

  function validateField(name) {
    const v = getValue(name);
    const msg = errorFor(name, v);
    const err = $('#err-' + name);
    if (err) err.innerHTML = msg ? ERR_SVG + '<span></span>' : '';
    if (err && msg) err.lastChild.textContent = msg;
    const group = $('#grp-' + name);
    const el = form.elements[name];
    const target = group || (el && el.tagName ? el : null);
    if (target) { if (msg) target.setAttribute('aria-invalid', 'true'); else target.removeAttribute('aria-invalid'); }
    return !msg;
  }

  /* Draft persistence (local to this device) */
  function readForm() {
    const data = {};
    Object.keys(FIELD_LABELS).concat('consent').forEach(n => { data[n] = getValue(n); });
    return data;
  }

  function saveDraft() {
    if (state.sent) return;
    const data = readForm();
    const hasAny = Object.entries(data).some(([k, v]) => k !== 'consent' && (Array.isArray(v) ? v.length : v));
    try {
      if (!hasAny) { localStorage.removeItem(DRAFT_KEY); return; }
      localStorage.setItem(DRAFT_KEY, JSON.stringify({ data, step:state.step, maxStep:state.maxStep }));
      els.draftNote.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4.5 4.5L19 7"/></svg><span>Draft saved on this device</span>';
    } catch (_) { /* storage unavailable: carry on without drafts */ }
  }

  function restoreDraft() {
    let saved;
    try { saved = JSON.parse(localStorage.getItem(DRAFT_KEY) || 'null'); } catch (_) { saved = null; }
    if (!saved || !saved.data) return;
    const d = saved.data;
    Array.from(form.elements).forEach(el => {
      if (!el.name || !(el.name in d)) return;
      const v = d[el.name];
      if (el.type === 'checkbox' && MULTI.has(el.name)) el.checked = Array.isArray(v) && v.includes(el.value);
      else if (el.type === 'checkbox') el.checked = v === 'yes';
      else if (el.type === 'radio') el.checked = el.value === v;
      else if (typeof v === 'string') el.value = v;
    });
    ['problem','solution','goal'].forEach(n => form.elements[n]._updateCounter());
    state.maxStep = clamp(saved.maxStep || 1, 1, 4);
    state.step = clamp(saved.step || 1, 1, state.maxStep);
    els.restore.hidden = false;
  }

  /* Review */
  function renderReview() {
    const d = readForm();
    const review = $('#review');
    review.innerHTML = '';
    [1, 2, 3].forEach(stepId => {
      const block = document.createElement('section');
      block.className = 'review-block';
      const head = document.createElement('header');
      const h = document.createElement('h4');
      h.textContent = STEPS[stepId - 1].title;
      const edit = document.createElement('button');
      edit.type = 'button'; edit.className = 'edit-btn';
      edit.textContent = 'Edit';
      edit.setAttribute('aria-label', 'Edit ' + STEPS[stepId - 1].title);
      edit.addEventListener('click', () => goTo(stepId));
      head.append(h, edit);
      const dl = document.createElement('dl');
      STEP_FIELDS[stepId].filter(f => f !== 'consent').forEach(f => {
        const v = d[f];
        const text = Array.isArray(v) ? v.join(', ') : v;
        const dt = document.createElement('dt'); dt.textContent = FIELD_LABELS[f];
        const dd = document.createElement('dd'); dd.textContent = text || 'Not provided';
        if (!text) dd.style.color = 'var(--muted)';
        dl.append(dt, dd);
      });
      block.append(head, dl);
      review.appendChild(block);
    });
  }

  /* Sending to the programme owner */
  function buildPayload(d) {
    return {
      full_name: d.full_name,
      email: d.email,
      phone: d.phone,
      institution: d.institution,
      course: d.course,
      year_of_study: d.year,
      startup_name: d.startup_name,
      sector: d.sector,
      stage: d.stage,
      problem: d.problem,
      solution: d.solution,
      team_size: d.team_size,
      link: d.link || null,
      mentor_areas: d.mentor_areas,
      goal: d.goal,
      commitment: d.commitment,
      heard_about: d.heard || null,
      consent: d.consent
    };
  }

  async function submitApplication() {
    // re-check everything in case a draft was edited oddly
    for (const step of [1, 2, 3]) {
      const bad = STEP_FIELDS[step].find(f => !validateField(f));
      if (bad) { state.step = step; showStep(true); focusField(bad); return; }
    }
    const d = readForm();
    state.sending = true;
    els.next.disabled = true;
    els.back.disabled = true;
    els.nextLabel.textContent = 'Sending…';
    els.status.hidden = true;
    let res;
    try {
      res = (window.anticode && typeof window.anticode.send === 'function')
        ? await window.anticode.send('contact', buildPayload(d))
        : { ok:false, message:'Applications can\'t be sent from here right now. Please try again in a moment.' };
    } catch (_) {
      res = { ok:false, message:'Something went wrong while sending. Please try again.' };
    }
    state.sending = false;
    els.next.disabled = false;
    els.back.disabled = false;
    els.nextLabel.textContent = 'Submit application';
    if (res && res.ok) {
      state.sent = true;
      try { localStorage.removeItem(DRAFT_KEY); } catch (_) {}
      const first = d.full_name.split(/\s+/)[0];
      $('#success-text').textContent = 'Thanks, ' + first + '. Your application for ' + (d.startup_name || 'your startup') + ' has reached the Abia Tech Hub team. Keep an eye on ' + d.email + ', which is where they\'ll reach you.';
      $('#form-wrap').hidden = true;
      const success = $('#apply-success');
      success.hidden = false;
      success.focus({ preventScroll:true });
      const top = $('#form-card').getBoundingClientRect().top;
      if (top < 0) window.scrollTo({ top: window.scrollY + top - 96, behavior: REDUCED ? 'auto' : 'smooth' });
    } else {
      els.status.textContent = (res && res.message) || 'Your application couldn\'t be sent. Please try again.';
      els.status.hidden = false;
    }
  }

  function cleanupPage() {
    pageEvents.abort();
    pageObservers.forEach(observer => observer.disconnect());
    pageObservers.clear();
    Weave.stop();
    clearTimeout(Weave.resizeTimer);
    if (Journey.raf) cancelAnimationFrame(Journey.raf);
    pageEvents = new AbortController();
  }

  function initPage() {
    state.step = 1;
    state.maxStep = 1;
    state.sending = false;
    state.sent = false;
    state.elig = { student:null, abia:null };
    state.journey = 0;
    initPageTransition();
    initStickyApply();
    initReveal();
    initCounters();
    Weave.init($('#weave'));
    Journey.init();
    initEligibility();
    initForm();
  }

  async function navigateTo(url, { addHistory = true } = {}) {
    activeNavigation?.abort();
    const navigation = new AbortController();
    activeNavigation = navigation;
    document.documentElement.setAttribute('aria-busy', 'true');

    try {
      const response = await fetch(url.href, {
        credentials:'same-origin',
        headers:{ 'Accept':'text/html', 'X-Requested-With':'XMLHttpRequest' },
        signal:navigation.signal,
      });
      if (!response.ok) {
        window.location.assign(url.href);
        return;
      }

      const finalUrl = new URL(response.url || url.href, location.href);
      const nextDocument = new DOMParser().parseFromString(await response.text(), 'text/html');
      const currentMain = $('#main');
      const nextMain = nextDocument.querySelector('#main');
      if (finalUrl.origin !== location.origin || !currentMain || !nextMain) {
        window.location.assign(url.href);
        return;
      }

      cleanupPage();
      currentMain.innerHTML = nextMain.innerHTML;
      document.title = nextDocument.title;
      const nextDescription = nextDocument.querySelector('meta[name="description"]');
      const description = $('meta[name="description"]');
      if (nextDescription && description) description.content = nextDescription.content;

      const header = $('#site-header');
      const nextHeader = nextDocument.querySelector('#site-header');
      if (header && nextHeader) {
        header.dataset.solid = nextHeader.dataset.solid;
        header.classList.toggle('is-solid', nextHeader.dataset.solid === 'true');
      }

      if (addHistory) history.pushState({}, '', finalUrl.href);
      updateCurrentRoute();
      initPage();

      if (finalUrl.hash) {
        document.getElementById(decodeURIComponent(finalUrl.hash.slice(1)))?.scrollIntoView();
      } else {
        window.scrollTo(0, 0);
      }
      currentMain.setAttribute('tabindex', '-1');
      currentMain.focus({ preventScroll:true });
    } catch (error) {
      if (error.name !== 'AbortError') window.location.assign(url.href);
    } finally {
      if (activeNavigation === navigation) {
        activeNavigation = null;
        document.documentElement.removeAttribute('aria-busy');
      }
    }
  }

  function initRouter() {
    document.addEventListener('click', event => {
      if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
      const link = event.target.closest('a[href]');
      if (!link || link.hasAttribute('download') || link.target || link.relList.contains('external')) return;

      const url = new URL(link.href, location.href);
      if (url.origin !== location.origin || url.href === location.href) return;
      if (url.pathname === location.pathname && url.search === location.search && url.hash) return;
      if (!$('#main')) return;

      event.preventDefault();
      navigateTo(url);
    }, { signal:persistentEvents.signal });

    window.addEventListener('popstate', () => {
      navigateTo(new URL(location.href), { addHistory:false });
    }, { signal:persistentEvents.signal });
  }

  /* ---------- Boot ---------- */
  initHeader();
  initPage();
  initRouter();
})();

}

if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", start, { once: true }); else start();
