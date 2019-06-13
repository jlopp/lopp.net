function MM_swapImgRestore() { //v3.0
  var i, x, a = document.MM_sr;
  for (i = 0; a && i < a.length && (x = a[i]) && x.oSrc; i++) x.src = x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d = document;
  if (d.images) {
    if (!d.MM_p) d.MM_p = new Array();
    var i, j = d.MM_p.length,
      a = MM_preloadImages.arguments;
    for (i = 0; i < a.length; i++)
      if (a[i].indexOf("#") != 0) {
        d.MM_p[j] = new Image;
        d.MM_p[j++].src = a[i];
      }
  }
}

function MM_findObj(n, d) { //v4.01
  var p, i, x;
  if (!d) d = document;
  if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
    d = parent.frames[n.substring(p + 1)].document;
    n = n.substring(0, p);
  }
  if (!(x = d[n]) && d.all) x = d.all[n];
  for (i = 0; !x && i < d.forms.length; i++) x = d.forms[i][n];
  for (i = 0; !x && d.layers && i < d.layers.length; i++) x = MM_findObj(n, d.layers[i].document);
  if (!x && d.getElementById) x = d.getElementById(n);
  return x;
}

function MM_swapImage() { //v3.0
  var i, j = 0,
    x, a = MM_swapImage.arguments;
  document.MM_sr = new Array;
  for (i = 0; i < (a.length - 2); i += 3)
    if ((x = MM_findObj(a[i])) != null) {
      document.MM_sr[j++] = x;
      if (!x.oSrc) x.oSrc = x.src;
      x.src = a[i + 2];
    }
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
  } else {
    if (document.getElementById("mystyle").getAttribute("href") == "css/dark.css") {
      document.getElementById("mystyle").setAttribute("href", "css/style.css");
    } else if (document.getElementById("mystyle").getAttribute("href") == "../css/dark.css") {
      document.getElementById("mystyle").setAttribute("href", "../css/style.css");
    }
  }
}

applyDarkLightMode();