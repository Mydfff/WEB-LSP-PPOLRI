/*
=========================================
LSP PPPOLRI
Component Loader
=========================================
*/

document.addEventListener("DOMContentLoaded", () => {
  /*
  ==========================================================
  MENENTUKAN PATH COMPONENT
  ==========================================================
  */

  const path = window.location.pathname;

  let componentPath = "components/";

  /*
  Jika halaman berada di dalam folder pages,
  naik kembali ke root project.
  */

  if (path.includes("/pages/")) {
    componentPath = "../../components/";
  }

  loadComponent("top-header", componentPath + "top-header.html");

  loadComponent("navbar", componentPath + "navbar.html");

  loadComponent("footer", componentPath + "footer.html");
});

/*
=========================================
FUNCTION LOAD COMPONENT
=========================================
*/

function loadComponent(id, file) {
  const element = document.getElementById(id);

  if (!element) return;

  fetch(file)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Component tidak ditemukan : " + file);
      }

      return response.text();
    })

    .then((data) => {
      element.innerHTML = data;

      /*
            =====================================
            SET ACTIVE MENU
            =====================================
            */

      setActiveMenu();

      /*
            =====================================
            INIT BOOTSTRAP NAVBAR
            =====================================
            */

      initNavbar();
    })

    .catch((error) => {
      console.error(error);
    });
}

/*
=========================================
ACTIVE MENU
=========================================
*/

function setActiveMenu() {
  /*
    =====================================
    AMBIL HALAMAN SEKARANG
    =====================================
    */

  let currentPage = location.pathname.split("/").pop().toLowerCase();

  /*
    =====================================
    JIKA ROOT / KOSONG
    =====================================
    */

  if (currentPage === "" || currentPage === "/") {
    currentPage = "index.php";
  }

  /*
    =====================================
    AMBIL SEMUA NAV LINK
    =====================================
    */

  const navLinks = document.querySelectorAll(".navbar-nav .nav-link");

  /*
    =====================================
    HAPUS ACTIVE DARI SEMUA MENU
    =====================================
    */

  navLinks.forEach((link) => {
    link.classList.remove("active");
  });

  /*
    =====================================
    CEK LINK AKTIF
    =====================================
    */

  navLinks.forEach((link) => {
    const href = link.getAttribute("href");

    /*
        -------------------------------------
        LINK KOSONG / #
        -------------------------------------
        */

    if (!href || href === "#") {
      return;
    }

    /*
        -------------------------------------
        AMBIL NAMA FILE
        -------------------------------------
        */

    const linkPage = href.split("/").pop().split("?")[0].toLowerCase();

    /*
        -------------------------------------
        HALAMAN SAMA
        -------------------------------------
        */

    if (linkPage === currentPage) {
      link.classList.add("active");
    }
  });

  /*
    =====================================
    KHUSUS HALAMAN BERITA
    =====================================
    */

  if (currentPage === "berita.php") {
    const informasiMenu = document.querySelector(".navbar-nav > .nav-item:nth-child(4) > .nav-link");

    if (informasiMenu) {
      informasiMenu.classList.add("active");
    }
  }
}

/*
=========================================
INIT NAVBAR
=========================================
*/

function initNavbar() {
  /*
    =====================================
    BOOTSTRAP DROPDOWN
    =====================================
    */

  const dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');

  dropdowns.forEach((dropdown) => {
    new bootstrap.Dropdown(dropdown);
  });
}
