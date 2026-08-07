document.addEventListener("DOMContentLoaded", function () {
  const gallerySwiper = new Swiper(".gallerySwiper", {
    slidesPerView: 3,
    spaceBetween: 25,
    loop: true,

    navigation: {
      nextEl: ".gallery-next",
      prevEl: ".gallery-prev",
    },

    breakpoints: {
      0: {
        slidesPerView: 1,
      },

      768: {
        slidesPerView: 2,
      },

      992: {
        slidesPerView: 3,
      },
    },
  });
});
