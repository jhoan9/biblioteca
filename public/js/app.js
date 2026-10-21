(function () {
    "use strict";

    var body = document.body;
    var botonMenu = document.getElementById("menuMovil");
    var velo = document.getElementById("velo");

    function cerrarMenu() {
        body.classList.remove("menu-abierto");
    }

    if (botonMenu) {
        botonMenu.addEventListener("click", function () {
            body.classList.toggle("menu-abierto");
        });
    }

    if (velo) {
        velo.addEventListener("click", cerrarMenu);
    }

    document.querySelectorAll(".menu-item").forEach(function (enlace) {
        enlace.addEventListener("click", cerrarMenu);
    });

    document.addEventListener("keydown", function (evento) {
        if (evento.key === "Escape") {
            cerrarMenu();
        }
    });

    document.querySelectorAll("form[data-confirm]").forEach(function (formulario) {
        formulario.addEventListener("submit", function (evento) {
            if (!window.confirm(formulario.getAttribute("data-confirm"))) {
                evento.preventDefault();
            }
        });
    });

    var aviso = document.querySelector(".aviso");
    if (aviso) {
        window.setTimeout(function () {
            aviso.style.transition = "opacity .4s ease, transform .4s ease";
            aviso.style.opacity = "0";
            aviso.style.transform = "translateY(-6px)";
            window.setTimeout(function () {
                aviso.remove();
            }, 400);
        }, 4500);
    }
})();
