/* Light YouTube Embeds by @labnol */
/* Web: http://labnol.org/?p=27941 */

document.addEventListener("DOMContentLoaded",
    function() {
        var div, n,
            v = document.getElementsByClassName("youtube-player");
        for (n = 0; n < v.length; n++) {
            div = document.createElement("div");
            div.setAttribute("data-id", v[n].dataset.id);
            div.setAttribute("data-start", v[n].dataset.start);
            div.innerHTML = labnolThumb(v[n].dataset.id);
            div.onclick = labnolIframe;
            v[n].appendChild(div);
        }
    });

function labnolThumb(id) {
    var thumb = '<img data-src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="thumbnail" class="lazyload">',
        play = '<div class="play"></div>';
    return thumb.replace("ID", id) + play;
}

function labnolIframe() {
    var iframe = document.createElement("iframe");
    var embed = "https://www.youtube.com/embed/ID?autoplay=1";
    if (this.dataset.start) {
      embed += "&start=" + this.dataset.start;
    }
    iframe.setAttribute("src", embed.replace("ID", this.dataset.id));
    iframe.setAttribute("frameborder", "0");
    iframe.setAttribute("allowfullscreen", "1");
    iframe.setAttribute("allow", "autoplay");
    this.parentNode.replaceChild(iframe, this);
}

function HideContent(d) {
  document.getElementById(d).style.display = "none";
}

function ShowContent(d) {
  document.getElementById(d).style.display = "block";
}

function ReverseDisplay(d) {
    if (document.getElementById(d).style.display == "none") {
      document.getElementById(d).style.display = "block";
    } else {
      document.getElementById(d).style.display = "none";
    }
  }

// Night mode toggle
function toggleDarkLight() {
  // Check if localstorage works; if it doesn't then use a cookie
  try {
    var newMode = localStorage.getItem('mode') == "dark" ? "" : "dark";
    localStorage.setItem("mode", newMode);
  } catch (ex) {
    var newMode = document.cookie.split(";")[0].split("=")[1] == "dark" ? "" : "dark";
    var date = new Date();
    date.setTime(date.getTime()+(365*24*60*60*1000));
    document.cookie = "mode=" + newMode + ";expires=" + date.toGMTString();
  }

  applyDarkLightMode();
}

function applyDarkLightMode() {
  // Check if localstorage works; if it doesn't then use a cookie
  var currentClass;
  try {
    currentClass = localStorage.getItem('mode');
  } catch (ex) {
    currentClass = document.cookie.split(";")[0].split("=")[1];
  }

  if (currentClass == "dark") {
    if (document.getElementById("mystyle").getAttribute("href") == "/css/style.css") {
      document.getElementById("mystyle").setAttribute("href", "/css/dark.css");
    }
    // only important for setting state on initial page load for pages with a toggle button
    const toggle = document.querySelector('.toggle-input');
    if (toggle) {
      toggle.checked = true;
    }
  } else {
    if (document.getElementById("mystyle").getAttribute("href") == "/css/dark.css") {
      document.getElementById("mystyle").setAttribute("href", "/css/style.css");
    }
  }
}

const toggle = document.querySelector('.toggle-input');
if (toggle) {
  toggle.addEventListener('change', function() {
    toggleDarkLight();
  });
}
applyDarkLightMode();

/* Resource-page navigation.
   List pages mark every section with a self-anchored <h2 id="x"><a href="#x">,
   but nothing ever links to those anchors. This builds a section rail, a sticky
   title bar, and a back-to-top control from them so long lists are navigable.
   Pages with fewer than two anchored sections are left untouched. */
function initPageNav() {
  // Bail if this page has already been enhanced, or has no content section.
  if (document.querySelector('.page-layout')) return;

  var contentSection = null;
  var sections = document.querySelectorAll('section');
  for (var s = 0; s < sections.length; s++) {
    if (sections[s].querySelector('h2[id]')) { contentSection = sections[s]; break; }
  }
  if (!contentSection) return;

  var row = contentSection.querySelector('.container > .row');
  if (!row) return;

  var headings = row.querySelectorAll('h2[id]');
  if (headings.length < 2) return;

  // Section headings end with a colon, and the hub page appends a decorative
  // arrow that means "links to another page" — neither belongs in the rail.
  // Strip trailing whitespace/colons and any trailing arrow glyph (U+2190–U+27BF).
  var label = function (h2) {
    return (h2.textContent || '').trim().replace(/[\s:\u2190-\u27BF]+$/, '');
  };

  // --- section rail ---
  var rail = document.createElement('nav');
  rail.className = 'section-rail';
  rail.setAttribute('aria-label', 'Sections on this page');

  var toggleBtn = document.createElement('button');
  toggleBtn.type = 'button';
  toggleBtn.className = 'rail-toggle';
  toggleBtn.setAttribute('aria-expanded', 'false');
  toggleBtn.textContent = 'On this page';
  rail.appendChild(toggleBtn);

  var railTitle = document.createElement('p');
  railTitle.className = 'rail-title';
  railTitle.textContent = 'On this page';
  rail.appendChild(railTitle);

  var list = document.createElement('ul');
  list.className = 'rail-list';
  var railLinks = [];
  for (var i = 0; i < headings.length; i++) {
    var id = headings[i].id;
    var li = document.createElement('li');
    var a = document.createElement('a');
    a.href = '#' + id;
    a.textContent = label(headings[i]);
    li.appendChild(a);
    list.appendChild(li);
    railLinks.push(a);
  }
  rail.appendChild(list);

  toggleBtn.addEventListener('click', function () {
    var open = rail.classList.toggle('open');
    toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  // --- wrap the two-column list so the rail can sit beside it ---
  var layout = document.createElement('div');
  layout.className = 'page-layout';
  row.parentNode.insertBefore(layout, row);
  layout.appendChild(rail);
  row.classList.add('page-main');
  layout.appendChild(row);

  // --- sticky title bar ---
  var h1 = document.querySelector('#main-background-section h1');
  var entryCount = row.querySelectorAll('li a[href]').length;
  var bar = document.createElement('div');
  bar.className = 'page-sticky-bar';
  var barInner = document.createElement('div');
  barInner.className = 'container';
  var barTitle = document.createElement('span');
  barTitle.className = 'bar-title';
  barTitle.textContent = h1 ? h1.textContent.trim() : document.title;
  barInner.appendChild(barTitle);
  var barMeta = document.createElement('span');
  barMeta.className = 'bar-meta';
  barMeta.textContent = entryCount
    ? entryCount + ' resources \u00B7 ' + headings.length + ' sections'
    : headings.length + ' sections';
  barInner.appendChild(barMeta);
  bar.appendChild(barInner);
  contentSection.parentNode.insertBefore(bar, contentSection);

  // --- highlight the section currently in view ---
  var current = null;
  var setCurrent = function (a) {
    if (a === current) return;
    if (current) current.classList.remove('current');
    current = a;
    if (current) current.classList.add('current');
  };

  if ('IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function (entries) {
      // Track the last heading that has crossed just under the sticky bar.
      for (var e = 0; e < entries.length; e++) {
        if (!entries[e].isIntersecting) continue;
        var idx = Array.prototype.indexOf.call(headings, entries[e].target);
        if (idx > -1) setCurrent(railLinks[idx]);
      }
    }, { rootMargin: '-56px 0px -70% 0px', threshold: 0 });
    for (var h = 0; h < headings.length; h++) observer.observe(headings[h]);
  }

  // --- back to top ---
  var top = document.createElement('button');
  top.type = 'button';
  top.className = 'back-to-top';
  top.setAttribute('aria-label', 'Back to top');
  top.textContent = '\u2191';
  document.body.appendChild(top);

  var reduceMotion = window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  top.addEventListener('click', function () {
    if (reduceMotion) { window.scrollTo(0, 0); }
    else { window.scrollTo({ top: 0, behavior: 'smooth' }); }
  });

  var onScroll = function () {
    if (window.scrollY > 600) { top.classList.add('visible'); }
    else { top.classList.remove('visible'); }
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Smooth in-page jumps from the rail, except when motion is reduced.
  if (!reduceMotion) {
    list.addEventListener('click', function (ev) {
      var a = ev.target.closest ? ev.target.closest('a') : null;
      if (!a) return;
      var target = document.getElementById(a.getAttribute('href').slice(1));
      if (!target) return;
      ev.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      if (history.replaceState) history.replaceState(null, '', a.getAttribute('href'));
    });
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initPageNav);
} else {
  initPageNav();
}