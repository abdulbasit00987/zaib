/* MR ZAIB – Customer Reviews & Questions widget */
(function () {
  'use strict';
  var root = document.querySelector('.mzr');
  if (!root) return;
  var D;
  try { D = JSON.parse(root.getAttribute('data-mzr')); } catch (e) { return; }
  var PAGE = 5, QPAGE = 4;
  var st = {
    reviews: { shown: PAGE, q: '', sort: 'recent', filter: '' },
    questions: { shown: QPAGE, q: '', sort: 'recent' }
  };

  function $(s, c) { return (c || root).querySelector(s); }
  function $$(s, c) { return Array.prototype.slice.call((c || root).querySelectorAll(s)); }
  function esc(t) { var d = document.createElement('div'); d.textContent = t == null ? '' : String(t); return d.innerHTML; }
  function nl(t) { return esc(t).replace(/\n/g, '<br>'); }
  function stars(r, size) {
    var h = '<span class="mzr-stars" style="--s:' + (size || 16) + 'px">';
    for (var i = 1; i <= 5; i++) { var f = Math.max(0, Math.min(1, r - (i - 1))) * 100; h += '<span class="mzr-star"><span style="width:' + f + '%"></span></span>'; }
    return h + '</span>';
  }
  var usedKey = 'mzr_useful_' + D.pid;
  var used = {};
  try { used = JSON.parse(localStorage.getItem(usedKey) || '{}'); } catch (e) {}

  /* ---------- reviews ---------- */
  function reviewList() {
    var s = st.reviews, q = s.q.toLowerCase();
    var list = D.reviews.filter(function (r) {
      if (s.filter === 'photos' && !r.images.length) return false;
      if (s.filter === 'verified' && !r.verified) return false;
      if (/^[1-5]$/.test(s.filter) && r.rating !== +s.filter) return false;
      if (q && (r.title + ' ' + r.body + ' ' + r.name).toLowerCase().indexOf(q) === -1) return false;
      return true;
    });
    list.sort(function (a, b) {
      if (s.sort === 'high') return b.rating - a.rating || b.ts - a.ts;
      if (s.sort === 'low') return a.rating - b.rating || b.ts - a.ts;
      if (s.sort === 'useful') return b.useful - a.useful || b.ts - a.ts;
      return b.ts - a.ts;
    });
    return list;
  }
  function reviewHTML(r) {
    var h = '<li class="mzr-review" id="review-' + r.id + '">';
    h += '<div class="mzr-who"><span class="mzr-ava">' + esc(r.initials) + '</span><div><strong>' + esc(r.name) + '</strong>';
    if (r.verified) h += '<span class="mzr-verified"><svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m5 12 4 4 10-10"/></svg>Verified buyer</span>';
    h += '</div></div>';
    h += '<div class="mzr-rbody"><div class="mzr-rhead">' + stars(r.rating, 15) + '<span class="mzr-date">' + esc(r.date) + '</span></div>';
    if (r.title) h += '<h4 class="mzr-rtitle">' + esc(r.title) + '</h4>';
    h += '<div class="mzr-text">' + nl(r.body) + '</div>';
    if (r.images.length) {
      h += '<div class="mzr-thumbs">';
      r.images.forEach(function (im) { h += '<button type="button" class="mzr-thumb" data-full="' + esc(im.f) + '"><img src="' + esc(im.t) + '" alt="" loading="lazy"></button>'; });
      h += '</div>';
    }
    if (r.recommend) {
      h += '<p class="mzr-recline ' + (r.recommend === 'yes' ? 'is-yes' : 'is-no') + '">' +
        (r.recommend === 'yes' ? '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m5 12 4 4 10-10"/></svg>Recommends this product' : '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 6l12 12M18 6 6 18"/></svg>Doesn\'t recommend this product') + '</p>';
    }
    if (r.reply) {
      h += '<div class="mzr-answer"><div class="mzr-ahead"><span class="mzr-ava mzr-ava--store">' + esc(D.storeIni) + '</span><strong>' + esc(D.store) + '</strong><span class="mzr-tag">Store</span><span class="mzr-date">' + esc(r.reply.date) + '</span></div><div class="mzr-text">' + nl(r.reply.body) + '</div></div>';
    }
    h += '<div class="mzr-actions"><button type="button" class="mzr-act' + (used[r.id] ? ' is-done' : '') + '" data-useful="' + r.id + '"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 11v9H4v-9h3Zm0 0 4-8a2 2 0 0 1 2 2v4h5.5a2 2 0 0 1 2 2.3l-1.2 7A2 2 0 0 1 17.3 20H7"/></svg>Useful <span>(' + r.useful + ')</span></button>';
    h += '<button type="button" class="mzr-act" data-share="' + r.id + '"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7M16 6l-4-4-4 4M12 2v13"/></svg>Share</button></div>';
    return h + '</div></li>';
  }
  function renderReviews() {
    var list = reviewList(), s = st.reviews, ul = $('[data-list="reviews"]');
    ul.innerHTML = list.length ? list.slice(0, s.shown).map(reviewHTML).join('') :
      '<li class="mzr-empty">' + (D.reviews.length ? 'No reviews match your search.' : 'No reviews yet. Be the first to share your thoughts!') + '</li>';
    var n = Math.min(s.shown, list.length);
    $('[data-showing="reviews"]').textContent = list.length ? 'Showing ' + n + ' of ' + list.length + ' reviews' : '';
    $('[data-more="reviews"]').hidden = n >= list.length;
    $$('.mzr-bar').forEach(function (b) { b.classList.toggle('is-on', s.filter === b.getAttribute('data-star')); });
  }

  /* ---------- questions ---------- */
  function questionList() {
    var s = st.questions, q = s.q.toLowerCase();
    var list = D.questions.filter(function (x) {
      return !q || (x.q + ' ' + (x.answer ? x.answer.body : '')).toLowerCase().indexOf(q) !== -1;
    });
    list.sort(function (a, b) {
      if (s.sort === 'oldest') return a.ts - b.ts;
      if (s.sort === 'answered') return (b.answer ? 1 : 0) - (a.answer ? 1 : 0) || b.ts - a.ts;
      return b.ts - a.ts;
    });
    return list;
  }
  function questionHTML(x) {
    var h = '<li class="mzr-question" id="question-' + x.id + '"><h4 class="mzr-q">' + esc(x.q) + '</h4><p class="mzr-meta">' + esc(x.name) + ', ' + esc(x.date) + '</p>';
    if (x.answer) {
      h += '<div class="mzr-answer"><div class="mzr-ahead"><span class="mzr-ava mzr-ava--store">' + esc(D.storeIni) + '</span><strong>' + esc(D.store) + '</strong><span class="mzr-tag">Store</span><span class="mzr-date">' + esc(x.answer.date) + '</span></div><div class="mzr-text">' + nl(x.answer.body) + '</div></div>';
    }
    return h + '</li>';
  }
  function renderQuestions() {
    var list = questionList(), s = st.questions, ul = $('[data-list="questions"]');
    ul.innerHTML = list.length ? list.slice(0, s.shown).map(questionHTML).join('') :
      '<li class="mzr-empty">' + (D.questions.length ? 'No questions match your search.' : 'No questions yet. Ask the first one!') + '</li>';
    var n = Math.min(s.shown, list.length);
    $('[data-showing="questions"]').textContent = list.length ? 'Showing ' + n + ' of ' + list.length + ' questions' : '';
    $('[data-more="questions"]').hidden = n >= list.length;
  }

  /* ---------- tabs ---------- */
  function showTab(t) {
    $$('.mzr-tab').forEach(function (b) { var on = b.getAttribute('data-tab') === t; b.classList.toggle('is-active', on); b.setAttribute('aria-selected', on); });
    $$('.mzr-panel').forEach(function (p) { var on = p.getAttribute('data-panel') === t; p.classList.toggle('is-active', on); p.hidden = !on; });
  }
  root.addEventListener('click', function (e) {
    var t = e.target.closest('.mzr-tab'); if (t) { showTab(t.getAttribute('data-tab')); return; }
    var b = e.target.closest('.mzr-bar');
    if (b && b.getAttribute('aria-disabled') !== 'true') { var v = b.getAttribute('data-star'); st.reviews.filter = st.reviews.filter === v ? '' : v; $('[data-filter="reviews"]').value = st.reviews.filter; st.reviews.shown = PAGE; renderReviews(); return; }
    var m = e.target.closest('[data-more]');
    if (m) { var k = m.getAttribute('data-more'); st[k].shown += k === 'reviews' ? PAGE * 2 : QPAGE * 2; k === 'reviews' ? renderReviews() : renderQuestions(); return; }
    var u = e.target.closest('[data-useful]');
    if (u) { useful(u); return; }
    var sh = e.target.closest('[data-share]');
    if (sh) { share(sh); return; }
    var im = e.target.closest('.mzr-thumb,.mzr-tile');
    if (im) { lightbox(im.getAttribute('data-full')); return; }
    var o = e.target.closest('[data-open]');
    if (o) { openModal(o.getAttribute('data-open')); return; }
    if (e.target.closest('[data-close-lb]') || e.target.classList.contains('mzr-lightbox')) { $('.mzr-lightbox').hidden = true; document.documentElement.classList.remove('mzr-lock'); return; }
    var ar = e.target.closest('.mzr-strip-arrow');
    if (ar) { var tr = $('.mzr-strip-track'); tr.scrollBy({ left: (ar.classList.contains('is-next') ? 1 : -1) * tr.clientWidth * 0.8, behavior: 'smooth' }); }
  });
  root.addEventListener('input', function (e) {
    var k = e.target.getAttribute('data-search');
    if (k) { st[k].q = e.target.value; st[k].shown = k === 'reviews' ? PAGE : QPAGE; k === 'reviews' ? renderReviews() : renderQuestions(); }
  });
  root.addEventListener('change', function (e) {
    var k = e.target.getAttribute('data-sort');
    if (k) { st[k].sort = e.target.value; k === 'reviews' ? renderReviews() : renderQuestions(); }
    if (e.target.getAttribute('data-filter') === 'reviews') { st.reviews.filter = e.target.value; st.reviews.shown = PAGE; renderReviews(); }
  });
  /* rating link under the title + hash */
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[href="#reviews"]');
    if (!a) return;
    e.preventDefault(); showTab('reviews'); root.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
  if (/^#(reviews|review-\d+)$/.test(location.hash)) showTab('reviews');
  if (/^#(questions|question-\d+)$/.test(location.hash)) showTab('questions');

  function post(action, fd) {
    fd.append('action', action); fd.append('pid', D.pid); fd.append('nonce', D.nonce);
    return fetch(D.ajax, { method: 'POST', body: fd, credentials: 'same-origin' }).then(function (r) { return r.json(); });
  }
  function useful(btn) {
    var id = btn.getAttribute('data-useful');
    if (used[id]) return;
    used[id] = 1; try { localStorage.setItem(usedKey, JSON.stringify(used)); } catch (e) {}
    btn.classList.add('is-done');
    var fd = new FormData(); fd.append('cid', id);
    post('mzr_useful', fd).then(function (res) {
      if (res && res.success) {
        D.reviews.forEach(function (r) { if (String(r.id) === id) r.useful = res.data.n; });
        btn.querySelector('span').textContent = '(' + res.data.n + ')';
      }
    }).catch(function () {});
  }
  function share(btn) {
    var url = location.href.split('#')[0] + '#review-' + btn.getAttribute('data-share');
    if (navigator.share) { navigator.share({ title: D.title, url: url }).catch(function () {}); return; }
    var done = function () { var o = btn.innerHTML; btn.textContent = 'Link copied'; setTimeout(function () { btn.innerHTML = o; }, 1600); };
    if (navigator.clipboard) navigator.clipboard.writeText(url).then(done, done); else done();
  }
  function lightbox(src) {
    var lb = $('.mzr-lightbox'); lb.querySelector('img').src = src; lb.hidden = false; document.documentElement.classList.add('mzr-lock');
  }

  /* ---------- modals ---------- */
  var openM = null, dirty = false;
  function openModal(name) {
    var m = $('[data-modal="' + name + '"]'); if (!m) return;
    m.hidden = false; openM = m; dirty = false; document.documentElement.classList.add('mzr-lock');
    resetForm(m); setTimeout(function () { var f = m.querySelector('.mzr-step.is-active button, .mzr-step.is-active textarea, .mzr-step.is-active input:not([type=hidden])'); if (f) f.focus(); }, 30);
  }
  function closeModal(force) {
    if (!openM) return;
    var f = openM.querySelector('form');
    if (!force && dirty && !f.classList.contains('is-sent') && !window.confirm('Leave without saving? Your text will be lost.')) return;
    openM.hidden = true; openM = null; document.documentElement.classList.remove('mzr-lock');
    if (f.getAttribute('data-live') === '1') location.reload();
  }
  function resetForm(m) {
    var f = m.querySelector('form'); f.reset(); f.classList.remove('is-sent'); f.removeAttribute('data-live'); files = [];
    $$('.mzr-rate-star,.mzr-yn button', m).forEach(function (b) { b.classList.remove('is-on'); });
    $$('input[type=hidden]', m).forEach(function (i) { i.value = ''; });
    var pv = m.querySelector('.mzr-previews'); if (pv) pv.innerHTML = '';
    $$('.mzr-err', m).forEach(function (e) { e.hidden = true; });
    goStep(m, 1); validate(m);
  }
  function goStep(m, n) {
    var steps = $$('.mzr-step', m);
    steps.forEach(function (s, i) { s.classList.toggle('is-active', i === n - 1); });
    $$('.mzr-progress span', m).forEach(function (p, i) { p.classList.toggle('is-on', i < n); });
    m.setAttribute('data-step', n);
    var back = m.querySelector('.mzr-back'); if (back) back.style.visibility = (n > 1 && n < 6) ? 'visible' : 'hidden';
  }
  function cur(m) { return +m.getAttribute('data-step') || 1; }
  function validate(m) {
    var f = m.querySelector('form');
    if (f.getAttribute('data-form') === 'review') {
      $('.mzr-step[data-step="1"] .mzr-next', m).disabled = !f.rating.value;
      var len = f.body.value.trim().length, nb = $('.mzr-step[data-step="3"] .mzr-next', m);
      nb.disabled = len < 10; nb.textContent = len < 10 ? 'Write at least 10 characters' : 'Continue';
      $('.mzr-count', m).textContent = len < 10 ? (10 - len) + ' more characters' : '';
      $('.mzr-step[data-step="4"] .mzr-next', m).disabled = !f.recommend.value;
      $('.mzr-step[data-step="5"] [type=submit]', m).disabled = !(f.name.value.trim() && /\S+@\S+\.\S+/.test(f.email.value));
    } else {
      $('[type=submit]', m).disabled = !(f.question.value.trim().length >= 3 && f.name.value.trim() && /\S+@\S+\.\S+/.test(f.email.value));
    }
  }
  var files = [];
  $$('.mzr-modal').forEach(function (m) {
    var f = m.querySelector('form');
    m.addEventListener('click', function (e) {
      if (e.target === m || e.target.closest('[data-close]')) { closeModal(e.target.closest('.mzr-done')); return; }
      if (e.target.closest('.mzr-back')) { goStep(m, Math.max(1, cur(m) - 1)); return; }
      var s = e.target.closest('.mzr-rate-star');
      if (s) { f.rating.value = s.getAttribute('data-v'); $$('.mzr-rate-star', m).forEach(function (b) { b.classList.toggle('is-on', +b.getAttribute('data-v') <= +f.rating.value); }); dirty = true; validate(m); setTimeout(function () { goStep(m, 2); }, 250); return; }
      var y = e.target.closest('[data-rec]');
      if (y) { f.recommend.value = y.getAttribute('data-rec'); $$('.mzr-yn button', m).forEach(function (b) { b.classList.toggle('is-on', b === y); }); validate(m); return; }
      var rm = e.target.closest('[data-rm]');
      if (rm) { files.splice(+rm.getAttribute('data-rm'), 1); previews(m); return; }
      var nx = e.target.closest('.mzr-next');
      if (nx && !nx.disabled) { goStep(m, cur(m) + 1); }
    });
    m.addEventListener('input', function () { dirty = true; validate(m); });
    m.addEventListener('mouseover', function (e) {
      var s = e.target.closest('.mzr-rate-star'); if (!s) return;
      $$('.mzr-rate-star', m).forEach(function (b) { b.classList.toggle('is-hover', +b.getAttribute('data-v') <= +s.getAttribute('data-v')); });
    });
    m.addEventListener('mouseout', function () { $$('.mzr-rate-star', m).forEach(function (b) { b.classList.remove('is-hover'); }); });
    var fi = m.querySelector('input[type=file]');
    if (fi) {
      fi.addEventListener('change', function () { addFiles(fi.files, m); fi.value = ''; });
      var zone = m.querySelector('.mzr-upload');
      ['dragover', 'dragenter'].forEach(function (ev) { zone.addEventListener(ev, function (e) { e.preventDefault(); zone.classList.add('is-drag'); }); });
      ['dragleave', 'drop'].forEach(function (ev) { zone.addEventListener(ev, function (e) { e.preventDefault(); zone.classList.remove('is-drag'); }); });
      zone.addEventListener('drop', function (e) { addFiles(e.dataTransfer.files, m); });
    }
    f.addEventListener('submit', function (e) {
      e.preventDefault();
      var btn = f.querySelector('.mzr-step.is-active [type=submit]'); if (!btn || btn.disabled) return;
      var err = f.querySelector('.mzr-step.is-active .mzr-err'); err.hidden = true;
      var fd = new FormData();
      Array.prototype.forEach.call(f.elements, function (el) { if (el.name && el.type !== 'file') fd.append(el.name, el.value); });
      files.forEach(function (file) { fd.append('images[]', file); });
      var label = btn.textContent; btn.disabled = true; btn.textContent = 'Sending…';
      post(f.getAttribute('data-form') === 'review' ? 'mzr_review' : 'mzr_question', fd).then(function (res) {
        btn.textContent = label; btn.disabled = false;
        if (res && res.success) {
          f.classList.add('is-sent');
          if (res.data && res.data.approved) f.setAttribute('data-live', '1');
          var msg = f.querySelector('[data-msg]'); if (msg && res.data) msg.textContent = res.data.msg;
          goStep(m, $$('.mzr-step', m).length);
        } else { err.textContent = (res && res.data && res.data.msg) || 'Something went wrong. Please try again.'; err.hidden = false; }
      }).catch(function () { btn.textContent = label; btn.disabled = false; err.textContent = 'Network error. Please try again.'; err.hidden = false; });
    });
  });
  function addFiles(list, m) {
    Array.prototype.forEach.call(list, function (file) {
      if (files.length >= 5 || !/^image\/(jpeg|png|webp)$/.test(file.type) || file.size > 5 * 1024 * 1024) return;
      files.push(file);
    });
    previews(m);
  }
  function previews(m) {
    var pv = m.querySelector('.mzr-previews');
    pv.innerHTML = files.map(function (file, i) { return '<span class="mzr-prev"><img src="' + URL.createObjectURL(file) + '" alt=""><button type="button" data-rm="' + i + '" aria-label="Remove">&times;</button></span>'; }).join('');
    var b = m.querySelector('[data-step="2"] .mzr-next');
    b.textContent = files.length ? 'Continue' : 'Skip';
    b.classList.toggle('mzr-btn--primary', !!files.length); b.classList.toggle('mzr-btn--secondary', !files.length);
  }
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    var lb = $('.mzr-lightbox'); if (!lb.hidden) { lb.hidden = true; document.documentElement.classList.remove('mzr-lock'); return; }
    closeModal(false);
  });

  renderReviews(); renderQuestions();
})();
