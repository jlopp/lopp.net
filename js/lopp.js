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
    if (document.getElementById("mystyle").getAttribute("href") == "css/style.css") {
      document.getElementById("mystyle").setAttribute("href", "css/dark.css");
    } else if (document.getElementById("mystyle").getAttribute("href") == "../css/style.css") {
      document.getElementById("mystyle").setAttribute("href", "../css/dark.css");
    }
    // only important for setting state on initial page load for pages with a toggle button
    const toggle = document.querySelector('.toggle-input');
    if (toggle) {
      toggle.checked = true;
    }
  } else {
    if (document.getElementById("mystyle").getAttribute("href") == "css/dark.css") {
      document.getElementById("mystyle").setAttribute("href", "css/style.css");
    } else if (document.getElementById("mystyle").getAttribute("href") == "../css/dark.css") {
      document.getElementById("mystyle").setAttribute("href", "../css/style.css");
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