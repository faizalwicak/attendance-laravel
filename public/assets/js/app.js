! function() {
    "use strict";
    var t, e, a, s;

    function r() {
        var t = document.querySelectorAll(".counter-value");
        t && t.forEach(function(n) {
            ! function t() {
                var e = +n.getAttribute("data-target"),
                    a = +n.innerText,
                    s = e / 250;
                s < 1 && (s = 1), a < e ? (n.innerText = (a + s).toFixed(0), setTimeout(t, 1)) : n.innerText = e
            }()
        })
    }

    function i() {
        setTimeout(function() {
            var t, e, a, s = document.getElementById("side-menu");
            s && (t = s.querySelector(".mm-active .active"), 300 < (e = t ? t.offsetTop : 0) && (e -= 100, (a = document.getElementsByClassName("vertical-menu") ? document.getElementsByClassName("vertical-menu")[0] : "") && a.querySelector(".simplebar-content-wrapper") && setTimeout(function() {
                a.querySelector(".simplebar-content-wrapper").scrollTop = e
            }, 0)))
        }, 0)
    }

    function d() {
        for (var t = document.getElementById("topnav-menu-content").getElementsByTagName("a"), e = 0, a = t.length; e < a; e++) "nav-item dropdown active" === t[e].parentElement.getAttribute("class") && (t[e].parentElement.classList.remove("active"), t[e].nextElementSibling.classList.remove("show"))
    }

    function c(t) {
        var e = document.getElementById(t);
        e.style.display = "block";
        var a = setInterval(function() {
            e.style.opacity || (e.style.opacity = 1), 0 < e.style.opacity ? e.style.opacity -= .2 : (clearInterval(a), e.style.display = "none")
        }, 200)
    }

    function u() {
        var t, e, a;
        eva.replace(), window.sessionStorage && ((t = sessionStorage.getItem("is_visited")) ? null !== (e = document.querySelector("#" + t)) && (e.checked = !0, a = t, 1 == document.getElementById("layout-direction-ltr").checked && "layout-direction-ltr" === a ? (document.getElementsByTagName("html")[0].removeAttribute("dir"), document.getElementById("layout-direction-rtl").checked = !1, document.getElementById("bootstrap-style").setAttribute("href", "/assets/css/bootstrap.min.css"), document.getElementById("app-style").setAttribute("href", "/assets/css/app.min.css"), sessionStorage.setItem("is_visited", "layout-direction-ltr")) : 1 == document.getElementById("layout-direction-rtl").checked && "layout-direction-rtl" === a && (document.getElementById("layout-direction-ltr").checked = !1, document.getElementById("bootstrap-style").setAttribute("href", "/assets/css/bootstrap-rtl.min.css"), document.getElementById("app-style").setAttribute("href", "/assets/css/app-rtl.min.css"), document.getElementsByTagName("html")[0].setAttribute("dir", "rtl"), sessionStorage.setItem("is_visited", "layout-direction-rtl"))) : sessionStorage.setItem("is_visited", "layout-direction-ltr"));
    }

    function m(t) {
        document.getElementById(t) && (document.getElementById(t).checked = !0)
    }

    function b() {
        document.webkitIsFullScreen || document.mozFullScreen || document.msFullscreenElement || document.body.classList.remove("fullscreen-enable")
    }
    window.onload = function() {
            document.getElementById("preloader") && (c("pre-status"), c("preloader"))
        }, u(), document.addEventListener("DOMContentLoaded", function(t) {
            document.getElementById("side-menu") && new MetisMenu("#side-menu")
        }), r(),
        function() {
            var e = document.body.getAttribute("data-sidebar-size");
            window.onload = function() {
                1024 <= window.innerWidth && window.innerWidth <= 1366 && (document.body.setAttribute("data-sidebar-size", "sm"), m("sidebar-size-small"))
            };
            for (var t = document.getElementsByClassName("vertical-menu-btn"), a = 0; a < t.length; a++) t[a] && t[a].addEventListener("click", function(t) {
                t.preventDefault(), document.body.classList.toggle("sidebar-enable"), 992 <= window.innerWidth ? null == e ? null == document.body.getAttribute("data-sidebar-size") || "lg" == document.body.getAttribute("data-sidebar-size") ? document.body.setAttribute("data-sidebar-size", "sm") : document.body.setAttribute("data-sidebar-size", "lg") : "md" == e ? "md" == document.body.getAttribute("data-sidebar-size") ? document.body.setAttribute("data-sidebar-size", "sm") : document.body.setAttribute("data-sidebar-size", "md") : "sm" == document.body.getAttribute("data-sidebar-size") ? document.body.setAttribute("data-sidebar-size", "lg") : document.body.setAttribute("data-sidebar-size", "sm") : i()
            })
        }(), setTimeout(function() {
            var t = document.querySelectorAll("#sidebar-menu a");
            t && t.forEach(function(t) {
                var e, a, s, n, o, l = window.location.href.split(/[?#]/)[0];
                t.href == l && (t.classList.add("active"), (e = t.parentElement) && "side-menu" !== e.id && (e.classList.add("mm-active"), (a = e.parentElement) && "side-menu" !== a.id && (a.classList.add("mm-show"), a.classList.contains("mm-collapsing") && console.log("has mm-collapsing"), (s = a.parentElement) && "side-menu" !== s.id && (s.classList.add("mm-active"), (n = s.parentElement) && "side-menu" !== n.id && (n.classList.add("mm-show"), (o = n.parentElement) && "side-menu" !== o.id && o.classList.add("mm-active"))))))
            })
        }, 0), (t = document.querySelectorAll(".navbar-nav a")) && t.forEach(function(t) {
            var e, a, s, n, o, l, r = window.location.href.split(/[?#]/)[0];
            t.href == r && (t.classList.add("active"), (e = t.parentElement) && (e.classList.add("active"), (a = e.parentElement).classList.add("active"), (s = a.parentElement) && (s.classList.add("active"), (n = s.parentElement).closest("li") && n.closest("li").classList.add("active"), n && (n.classList.add("active"), (o = n.parentElement) && (o.classList.add("active"), (l = o.parentElement) && l.classList.add("active"))))))
        }), (e = document.querySelector('[data-toggle="fullscreen"]')) && e.addEventListener("click", function(t) {
            t.preventDefault(), document.body.classList.toggle("fullscreen-enable"), document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement ? document.cancelFullScreen ? document.cancelFullScreen() : document.mozCancelFullScreen ? document.mozCancelFullScreen() : document.webkitCancelFullScreen && document.webkitCancelFullScreen() : document.documentElement.requestFullscreen ? document.documentElement.requestFullscreen() : document.documentElement.mozRequestFullScreen ? document.documentElement.mozRequestFullScreen() : document.documentElement.webkitRequestFullscreen && document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT)
        }), document.addEventListener("fullscreenchange", b), document.addEventListener("webkitfullscreenchange", b), document.addEventListener("mozfullscreenchange", b),
        function() {
            if (document.getElementById("topnav-menu-content")) {
                for (var t = document.getElementById("topnav-menu-content").getElementsByTagName("a"), e = 0, a = t.length; e < a; e++) t[e].onclick = function(t) {
                    "#" === t.target.getAttribute("href") && (t.target.parentElement.classList.toggle("active"), t.target.nextElementSibling.classList.toggle("show"))
                };
                window.addEventListener("resize", d)
            }
        }(), [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function(t) {
            return new bootstrap.Tooltip(t)
        }), [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]')).map(function(t) {
            return new bootstrap.Popover(t)
        }), [].slice.call(document.querySelectorAll(".toast")).map(function(t) {
            return new bootstrap.Toast(t)
        }), document.querySelectorAll("input[name='layout-mode']").forEach(function(t) {
            t.addEventListener("change", function(t) {
                t && t.target && t.target.value && ("light" == t.target.value ? (document.body.setAttribute("data-layout-mode", "light"), document.body.setAttribute("data-topbar", "light"), document.body.setAttribute("data-sidebar", "light"), a.hasAttribute("data-layout") && "horizontal" == a.getAttribute("data-layout") || document.body.setAttribute("data-sidebar", "light"), m("topbar-color-light"), m("sidebar-color-light")) : "dark" == t.target.value ? (document.body.setAttribute("data-layout-mode", "dark"), document.body.setAttribute("data-topbar", "dark"), document.body.setAttribute("data-sidebar", "dark"), a.hasAttribute("data-layout") && "horizontal" == a.getAttribute("data-layout") || document.body.setAttribute("data-sidebar", "dark"), m("topbar-color-dark")) : "bordered" == t.target.value && document.body.setAttribute("data-layout-mode", "bordered"))
            })
        }), document.querySelectorAll("input[name='layout-direction']").forEach(function(t) {
            t.addEventListener("change", function(t) {
                t && t.target && t.target.value && ("ltr" == t.target.value ? (document.getElementsByTagName("html")[0].removeAttribute("dir"), document.getElementById("bootstrap-style").setAttribute("href", "/assets/css/bootstrap.min.css"), document.getElementById("app-style").setAttribute("href", "/assets/css/app.min.css"), sessionStorage.setItem("is_visited", "layout-direction-ltr")) : (document.getElementById("bootstrap-style").setAttribute("href", "/assets/css/bootstrap-rtl.min.css"), document.getElementById("app-style").setAttribute("href", "/assets/css/app-rtl.min.css"), document.getElementsByTagName("html")[0].setAttribute("dir", "rtl"), sessionStorage.setItem("is_visited", "layout-direction-rtl")))
            })
        }), i(), (s = document.getElementById("checkAll")) && (s.onclick = function() {
            for (var t = document.querySelectorAll('.table-check input[type="checkbox"]'), e = 0; e < t.length; e++) t[e].checked = this.checked
        })
}();