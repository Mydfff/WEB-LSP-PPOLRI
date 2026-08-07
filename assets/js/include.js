/*
=========================================
LSP PPPOLRI
Component Loader
=========================================
*/

document.addEventListener("DOMContentLoaded", () => {
  loadComponent("top-header", "components/top-header.html");

  loadComponent("navbar", "components/navbar.html");

  loadComponent("footer", "components/footer.html");
});

/*
=========================================
Function Load Component
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

      setActiveMenu();
    })

    .catch((error) => {
      console.error(error);
    });
}

/*
=========================================
Active Menu
=========================================
*/

function setActiveMenu() {
  const currentPage = location.pathname.split("/").pop();

  const navLinks = document.querySelectorAll(".navbar-nav .nav-link");

  navLinks.forEach((link) => {
    const href = link.getAttribute("href");

    if (!href || href === "#") return;

    if (href === currentPage) {
      link.classList.add("active");
    }
  });
}
