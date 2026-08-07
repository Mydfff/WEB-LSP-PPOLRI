document.addEventListener("DOMContentLoaded", function () {
  const checkNavbar = setInterval(() => {
    const navbar = document.querySelector(".navbar-custom");

    if (navbar) {
      clearInterval(checkNavbar);

      window.addEventListener("scroll", function () {
        if (window.scrollY > 120) {
          navbar.classList.add("fixed-navbar");
        } else {
          navbar.classList.remove("fixed-navbar");
        }
      });
    }
  }, 100);
});
