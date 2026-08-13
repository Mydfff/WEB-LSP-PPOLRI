document.addEventListener("DOMContentLoaded", function () {
  const popup = document.getElementById("securityPopup");
  const closeButton = document.getElementById("securityPopupClose");
  const overlay = document.querySelector(".security-popup-overlay");

  if (!popup) {
    console.log("Popup Security tidak ditemukan.");
    return;
  }

  console.log("Popup Security berhasil dimuat.");

  /* =====================================================
       TAMPILKAN POPUP
    ===================================================== */

  function showPopup() {
    popup.classList.add("show");

    popup.setAttribute("aria-hidden", "false");

    document.body.style.overflow = "hidden";

    console.log("Popup Security ditampilkan.");
  }

  /* =====================================================
       TUTUP POPUP
    ===================================================== */

  function closePopup() {
    popup.classList.remove("show");

    popup.setAttribute("aria-hidden", "true");

    document.body.style.overflow = "";
  }

  /* =====================================================
       TEST
       Popup muncul 3 detik setelah halaman dibuka
    ===================================================== */

  setTimeout(function () {
    showPopup();
  }, 1000);

  /* =====================================================
       CLOSE BUTTON
    ===================================================== */

  if (closeButton) {
    closeButton.addEventListener("click", closePopup);
  }

  /* =====================================================
       CLICK OVERLAY
    ===================================================== */

  if (overlay) {
    overlay.addEventListener("click", closePopup);
  }

  /* =====================================================
       ESC KEY
    ===================================================== */

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && popup.classList.contains("show")) {
      closePopup();
    }
  });
});
