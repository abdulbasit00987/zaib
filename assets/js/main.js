/* MR ZAIB – front-end interactions */
(function ($) {
  'use strict';
  var $body = $('body');

  /* Announcement bar rotation */
  $('[data-announcement]').each(function () {
    var $items = $(this).find('.ann-item'), i = 0;
    if ($items.length < 2) { $(this).find('.ann-nav').hide(); return; }
    function go(n) { $items.eq(i).removeClass('is-active'); i = (n + $items.length) % $items.length; $items.eq(i).addClass('is-active'); }
    var t = setInterval(function () { go(i + 1); }, 4000);
    $(this).on('click', '.ann-next', function () { clearInterval(t); go(i + 1); });
    $(this).on('click', '.ann-prev', function () { clearInterval(t); go(i - 1); });
  });

  /* Sticky header shadow */
  var $h = $('[data-header]');
  /* Compact sticky header (like reference): after the announcement bar scrolls away
     the header pins to the top as one row, the nav row hides and a menu icon appears. */
  var $wrap = $('[data-header-wrap]'), $ann = $('.announcement-bar'), hdrRaf = 0;
  function hdrSize() { if (!$h.hasClass('is-compact')) $wrap.css('height', $h.outerHeight()); }
  function hdrScroll() {
    // Pin only after the full header has scrolled out of view, then slide the
    // compact row in smoothly (no jump, no blank gap under it).
    var start = ($ann.length ? $ann.outerHeight() : 0) + $wrap.outerHeight();
    var y = window.scrollY, c = y > start;
    if (c && !$h.hasClass('is-compact')) {
      hdrSize(); $h.addClass('is-compact');
      cancelAnimationFrame(hdrRaf);
      hdrRaf = requestAnimationFrame(function () { hdrRaf = requestAnimationFrame(function () { $h.addClass('is-open'); }); });
    } else if (!c && $h.hasClass('is-compact') && y <= start - 1) {
      cancelAnimationFrame(hdrRaf);
      $h.removeClass('is-compact is-open nav-show');
    }
    $h.toggleClass('is-stuck', c);
  }
  hdrSize(); hdrScroll();
  // Mobile/tablet menu (like reference): hamburger on the right drops the menu down under the header.
  var $mnav = $h.find('.mobile-nav');
  $mnav.find('li.menu-item-has-children, li.page_item_has_children').each(function () {
    var $li = $(this);
    if (!$li.children('.mnav-sub-toggle').length) {
      $li.children('a').after('<button type="button" class="mnav-sub-toggle" aria-label="Open"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m6 9 6 6 6-6"/></svg></button>');
    }
  });
  $mnav.on('click', '.mnav-sub-toggle', function (e) {
    e.preventDefault();
    var $li = $(this).parent();
    $li.toggleClass('is-open');
    $(this).attr('aria-expanded', $li.hasClass('is-open') ? 'true' : 'false');
  });
  function mnavSet(open) {
    $h.toggleClass('mnav-open', open);
    $('body').toggleClass('mnav-lock', open);
    $h.find('.mnav-toggle').attr('aria-expanded', open ? 'true' : 'false');
  }
  $h.on('click', '.mnav-toggle', function (e) { e.preventDefault(); mnavSet(!$h.hasClass('mnav-open')); });
  $(window).on('resize', function () { if (window.innerWidth >= 990 && $h.hasClass('mnav-open')) mnavSet(false); });
  $(document).on('keydown', function (e) { if (e.key === 'Escape' && $h.hasClass('mnav-open')) mnavSet(false); });

  // Compact header menu icon (desktop), like reference: slides the nav row down under the
  // header and the icon turns into a cross; clicking the cross slides it back up.
  $h.find('.menu-toggle').append('<svg class="mz-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19"/></svg>');
  document.addEventListener('click', function (e) {
    var t = e.target.closest && e.target.closest('.site-header .menu-toggle');
    if (t && $h.hasClass('is-compact') && window.innerWidth >= 990) {
      e.preventDefault(); e.stopImmediatePropagation();
      $h.toggleClass('nav-show');
      t.setAttribute('aria-expanded', $h.hasClass('nav-show') ? 'true' : 'false');
    }
  }, true);
  $(window).on('scroll', hdrScroll).on('resize load', function () { if (!$h.hasClass('is-compact')) { $wrap.css('height', ''); hdrSize(); } hdrScroll(); });

  /* Drawers & search */
  function openEl(id) {
    var $el = $('#' + id);
    if (!$el.length) return false;
    $el.addClass('is-open').attr('aria-hidden', 'false');
    if ($el.hasClass('drawer')) $body.addClass('drawer-lock');
    if (id === 'search-overlay') setTimeout(function () { $el.find('input[type=search]').trigger('focus'); }, 200);
    return true;
  }
  function closeAll() {
    $('.drawer.is-open,.search-overlay.is-open').removeClass('is-open').attr('aria-hidden', 'true');
    $body.removeClass('drawer-lock');
  }
  $(document).on('click', '[data-open]', function (e) {
    if ($(this).is('.cart-toggle') && pluginCart()) return;
    if (openEl($(this).data('open'))) e.preventDefault();
  });
  $(document).on('click', '[data-close]', function (e) { e.preventDefault(); closeAll(); });
  $(document).on('keyup', function (e) { if (e.key === 'Escape') closeAll(); });

  /* Cart drawer: uses "Mini Cart Drawer for WooCommerce" plugin when active, else the theme drawer */
  function pluginCart() { return !!(window.apbd_nmca || document.getElementById('apbd-mini-cart-container')); }
  function openCart() {
    if (pluginCart()) { window.postMessage({ type: 'mc_open', status: 'CS' }, '*'); return true; }
    return openEl('drawer-cart');
  }
  window.mrzaibOpenCart = openCart;
  // Mini Cart Drawer (lite) renders item links relative to the current page (/shop/https://...): fix on click.
  document.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('#apbd-mini-cart-container a[href]');
    if (!a) return;
    var h = a.getAttribute('href') || '', i = h.indexOf('http', 1);
    if (i > 0) { e.preventDefault(); e.stopPropagation(); window.location.href = h.slice(i); }
  }, true);
  $(document).on('click', '.cart-toggle', function (e) {
    if (pluginCart()) { e.preventDefault(); e.stopImmediatePropagation(); openCart(); }
  });
  $(document.body).on('added_to_cart', function () {
    // Close the Quick View modal first (variable products), then slide in the cart.
    var delay = 150;
    if ($.magnificPopup && $.magnificPopup.instance && $.magnificPopup.instance.isOpen) { $.magnificPopup.close(); delay = 400; }
    setTimeout(openCart, pluginCart() ? delay + 350 : delay);
  });
  // Single product page posts the form and reloads: open the cart drawer when the success notice is shown.
  if ($('body').hasClass('single-product') && $('.woocommerce-message .wc-forward').length) {
    setTimeout(openCart, 900);
  }

  /* Product card icons -> Reno QuickView modal */
  $(document).on('click', '.mz-qv', function (e) {
    e.preventDefault();
    var $src = $(this).closest('.card-icons').find('.sp-wqv-view-button');
    if (!$src.length) $src = $('.sp-wqv-view-button[data-id="' + $(this).data('id') + '"]').first();
    if ($src.length) $src.trigger('click'); else window.location = $(this).closest('.product-card').find('a.card-media').attr('href');
  });
  $(document.body).on('adding_to_cart', function (e, $btn) { if ($btn && $btn.hasClass('card-icon')) $btn.addClass('is-loading'); });
  $(document.body).on('added_to_cart', function (e, f, h, $btn) { if ($btn && $btn.hasClass && $btn.hasClass('card-icon')) $btn.removeClass('is-loading'); });


  /* ===== Reference-style product UI: size pills, qty stepper, drawer free-shipping bar ===== */
  function mzPills(scope) {
    $(scope).find('table.variations select').each(function () {
      var $sel = $(this);
      if ($sel.data('mzPills')) return;
      $sel.data('mzPills', 1).addClass('mz-pill-select');
      var $wrap = $('<div class="mz-pills" role="radiogroup"></div>');
      $sel.find('option').each(function () {
        if (!this.value) return;
        $('<button type="button" class="mz-pill"></button>').text($(this).text()).attr('data-value', this.value).appendTo($wrap);
      });
      $sel.after($wrap);
      function sync() {
        var v = $sel.val();
        $wrap.find('.mz-pill').each(function () {
          var $p = $(this), ok = $sel.find('option[value="' + $p.attr('data-value').replace(/"/g, '\\"') + '"]').length && !$sel.find('option[value="' + $p.attr('data-value').replace(/"/g, '\\"') + '"]').is(':disabled');
          $p.toggleClass('is-active', $p.attr('data-value') === v).toggleClass('is-disabled', !ok).attr('aria-pressed', $p.attr('data-value') === v);
        });
      }
      $wrap.on('click', '.mz-pill', function () {
        var v = $(this).attr('data-value');
        $sel.val($sel.val() === v ? '' : v).trigger('change');
        sync();
      });
      $sel.on('change', sync);
      $sel.closest('form').on('woocommerce_update_variation_values reset_data', function () { setTimeout(sync, 0); });
      sync();
    });
  }
  function mzQty(scope) {
    $(scope).find('.quantity').each(function () {
      var $q = $(this), $in = $q.find('input.qty');
      if (!$in.length || $q.data('mzQty') || $in.attr('type') === 'hidden') return;
      $q.data('mzQty', 1).addClass('mz-qty');
      $('<button type="button" class="mz-qty-btn mz-minus" aria-label="Decrease quantity">&minus;</button>').insertBefore($in);
      $('<button type="button" class="mz-qty-btn mz-plus" aria-label="Increase quantity">+</button>').insertAfter($in);
    });
  }
  $(document).on('click', '.mz-qty-btn', function () {
    var $in = $(this).siblings('input.qty'), v = parseFloat($in.val()) || 0,
      step = parseFloat($in.attr('step')) || 1, min = parseFloat($in.attr('min')) || 0, max = parseFloat($in.attr('max')) || Infinity;
    v = $(this).hasClass('mz-plus') ? Math.min(max, v + step) : Math.max(min || 1, v - step);
    $in.val(v).trigger('change');
  });
  function mzEnhance(scope) { mzPills(scope); mzQty(scope); }
  mzEnhance(document);
  if (window.MutationObserver) {
    var mzT;
    new MutationObserver(function () {
      clearTimeout(mzT);
      mzT = setTimeout(function () {
        var $qv = $('.sp-wqv-content');
        if ($qv.length) mzEnhance($qv);
        mzDrawerBar();
      }, 60);
    }).observe(document.body, { childList: true, subtree: true });
  }

  /* Free-shipping bar inside the Mini Cart Drawer plugin */
  var mzLastSub = null;
  function mzDrawerBar() {
    var $c = $('#apbd-mini-cart-container');
    if (!$c.length) return;
    $c.find('a[href]').each(function () {
      var h = this.getAttribute('href'), i = h.indexOf('http', 1);
      if (i > 0 && h.charAt(0) !== 'h') this.setAttribute('href', h.slice(i));
    });
    var $body = $c.find('.apbd-nmca-vt-cart-footer').first();
    if (!$body.length) return;
    var $src = $('#mz-freeship-holder .mz-freeship');
    var $bar = $c.find('.mz-freeship.mz-in-drawer');
    if (!$bar.length) { $bar = $src.clone().addClass('mz-in-drawer'); $body.before($bar); }
    else if ($bar.html() !== $src.html()) { $bar.attr('class', $src.attr('class') + ' mz-in-drawer').html($src.html()); }
    // plugin changed quantities by itself -> refresh fragments once so the bar follows
    var sub = $.trim($c.find('.apbd-nmca-vt-cart-footer').text());
    if (mzLastSub !== null && sub !== mzLastSub) { $(document.body).trigger('wc_fragment_refresh'); }
    mzLastSub = sub;
  }
  $(document.body).on('wc_fragments_refreshed wc_fragments_loaded added_to_cart removed_from_cart', function () { setTimeout(mzDrawerBar, 50); });

  /* Drawer nav accordions */
  $('.drawer-nav li.menu-item-has-children').each(function () {
    $(this).children('a').after('<button class="sub-toggle" aria-label="Open"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 6 6 6-6 6"/></svg></button>');
  });
  $(document).on('click', '.drawer-nav .sub-toggle', function (e) {
    e.preventDefault();
    $(this).parent().toggleClass('is-open');
  });

  /* Hero slider */
  $('[data-slider]').each(function () {
    var $s = $(this), $slides = $s.find('.slide'), $dots = $s.find('.slider-dots'), i = 0, t;
    if ($slides.length < 2) { $s.find('.slider-arrow,.slider-dots').hide(); return; }
    $slides.each(function (n) { $dots.append('<button aria-label="Slide ' + (n + 1) + '"' + (n === 0 ? ' class="is-active"' : '') + '></button>'); });
    function go(n) {
      $slides.eq(i).removeClass('is-active'); $dots.children().eq(i).removeClass('is-active');
      i = (n + $slides.length) % $slides.length;
      $slides.eq(i).addClass('is-active'); $dots.children().eq(i).addClass('is-active');
    }
    function play() { clearInterval(t); t = setInterval(function () { go(i + 1); }, parseInt($s.data('autoplay'), 10) || 5000); }
    $s.on('click', '.next', function () { go(i + 1); play(); });
    $s.on('click', '.prev', function () { go(i - 1); play(); });
    $dots.on('click', 'button', function () { go($(this).index()); play(); });
    // swipe
    var x0 = null;
    $s.on('touchstart', function (e) { x0 = e.originalEvent.touches[0].clientX; });
    $s.on('touchend', function (e) {
      if (x0 === null) return;
      var dx = e.originalEvent.changedTouches[0].clientX - x0;
      if (Math.abs(dx) > 40) { go(dx < 0 ? i + 1 : i - 1); play(); }
      x0 = null;
    });
    play();
  });

  /* Tabs */
  $('[data-tabs]').on('click', '.tab-btn', function () {
    var $w = $(this).closest('[data-tabs]'), id = $(this).data('tab');
    $w.find('.tab-btn').removeClass('is-active'); $(this).addClass('is-active');
    $w.find('.tab-panel').removeClass('is-active').filter('[data-panel="' + id + '"]').addClass('is-active');
  });

  /* Read more */
  $(document).on('click', '.readmore-btn', function () {
    var $c = $(this).closest('[data-readmore]').toggleClass('is-open');
    $(this).text($c.hasClass('is-open') ? $(this).data('less') : $(this).data('more'));
  });

  /* Count-up stats */
  var counted = false;
  function countUp() {
    if (counted) return;
    var $n = $('.stat .num, .tstat .num');
    if (!$n.length) return;
    var top = $n.first().offset().top;
    if (window.scrollY + window.innerHeight > top) {
      counted = true;
      $n.each(function () {
        var $e = $(this), target = parseFloat($e.data('count')) || 0, start = null;
        function step(ts) {
          if (!start) start = ts;
          var p = Math.min((ts - start) / 1500, 1);
          $e.text(Math.round(target * p));
          if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
      });
    }
  }
  $(window).on('scroll load', countUp);

  /* Video showcase: load + play only when visible */
  (function () {
    var vids = document.querySelectorAll('.vs-video[data-src]');
    if (!vids.length) return;
    function load(v) { if (!v.src) { v.src = v.getAttribute('data-src'); } var pr = v.play(); if (pr && pr.catch) pr.catch(function () {}); }
    if (!('IntersectionObserver' in window)) { vids.forEach(load); return; }
    var io = new IntersectionObserver(function (en) {
      en.forEach(function (e) { if (e.isIntersecting) { load(e.target); } else if (e.target.src) { e.target.pause(); } });
    }, { rootMargin: '200px' });
    vids.forEach(function (v) { io.observe(v); });
  })();

  /* Newsletter (front-end only; connect Mailchimp etc. later) */
  $('[data-newsletter]').on('submit', function (e) {
    e.preventDefault();
    $(this).find('.nl-msg').prop('hidden', false);
    this.reset();
  });
})(jQuery);

/* Predictive search (reference-style dropdown) */
jQuery(function ($) {
  if (!window.MRZAIB) return;
  var $screen = $('<div class="mzp-screen" hidden></div>').appendTo('body');
  var timer, xhr, cache = {};
  function close($f) {
    ($f || $('form[data-predictive].is-open')).each(function () {
      $(this).removeClass('is-open is-focus').find('.mz-predictive').attr('hidden', true).empty();
      $(this).find('input[type=search]').attr('aria-expanded', 'false');
    });
    $screen.attr('hidden', true); $('body').removeClass('mzp-on');
  }
  function show($f, html) {
    var $p = $f.find('.mz-predictive');
    if (!html) { close($f); return; }
    $p.html(html).removeAttr('hidden'); $f.addClass('is-open');
    $f.find('input[type=search]').attr('aria-expanded', 'true');
    if (!$f.closest('.search-overlay').length) { $screen.removeAttr('hidden'); $('body').addClass('mzp-on'); }
  }
  $(document).on('focus', 'form[data-predictive] input[type=search]', function () {
    var $f = $(this).closest('form').addClass('is-focus');
    if ($.trim(this.value)) $(this).trigger('input');
  });
  $(document).on('input', 'form[data-predictive] input[type=search]', function () {
    var $f = $(this).closest('form'), q = $.trim(this.value);
    clearTimeout(timer);
    if (!q) { close($f); $f.addClass('is-focus'); return; }
    if (cache[q]) { show($f, cache[q]); return; }
    timer = setTimeout(function () {
      if (xhr) xhr.abort();
      xhr = $.getJSON(MRZAIB.ajax, { action: 'mrzaib_suggest', q: q }, function (r) {
        cache[q] = r.html; show($f, r.html);
      });
    }, 220);
  });
  $(document).on('click', '.mz-search-close', function () {
    var $f = $(this).closest('form'); $f.find('input[type=search]').val('').trigger('blur'); close($f); $f.removeClass('is-focus');
  });
  $screen.on('click', function () { close(); $('form[data-predictive]').removeClass('is-focus'); });
  $(document).on('keydown', function (e) { if (e.key === 'Escape') { close(); $('form[data-predictive]').removeClass('is-focus'); } });
  $(document).on('click', function (e) {
    if (!$(e.target).closest('form[data-predictive]').length) $('form[data-predictive]').not('.is-open').removeClass('is-focus');
  });
});
