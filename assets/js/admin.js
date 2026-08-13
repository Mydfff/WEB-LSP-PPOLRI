document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.getElementById("adminSidebar");
  const toggle = document.getElementById("sidebarToggle");
  const overlay = document.getElementById("sidebarOverlay");

  if (!sidebar || !toggle) {
    return;
  }

  function openSidebar() {
    sidebar.classList.add("sidebar-open");

    if (overlay) {
      overlay.classList.add("active");
    }

    toggle.setAttribute("aria-expanded", "true");
  }

  function closeSidebar() {
    sidebar.classList.remove("sidebar-open");

    if (overlay) {
      overlay.classList.remove("active");
    }

    toggle.setAttribute("aria-expanded", "false");
  }

  toggle.addEventListener("click", function () {
    if (sidebar.classList.contains("sidebar-open")) {
      closeSidebar();
    } else {
      openSidebar();
    }
  });

  if (overlay) {
    overlay.addEventListener("click", function () {
      closeSidebar();
    });
  }

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      closeSidebar();
    }
  });

  /*
   * Tutup sidebar setelah memilih menu
   * hanya pada layar mobile/tablet.
   */

  const sidebarLinks = sidebar.querySelectorAll(".sidebar-link");

  sidebarLinks.forEach(function (link) {
    link.addEventListener("click", function () {
      if (window.innerWidth <= 991) {
        closeSidebar();
      }
    });
  });

  /*
   * Jika layar kembali ke desktop,
   * reset keadaan sidebar mobile.
   */

  window.addEventListener("resize", function () {
    if (window.innerWidth > 991) {
      closeSidebar();
    }
  });
});
