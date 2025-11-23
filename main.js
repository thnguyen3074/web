const menuToggle = document.querySelector(".menu-toggle");
const nav = document.querySelector(".nav");
const auth = document.querySelector(".auth");

if (menuToggle && nav && auth) {
  menuToggle.addEventListener("click", () => {
    nav.classList.toggle("open");
    auth.classList.toggle("open");
  });
}

document.querySelectorAll('form[data-validate="true"]').forEach((form) => {
  form.addEventListener("submit", (event) => {
    const fields = form.querySelectorAll("input, textarea, select");
    const hasEmpty = Array.from(fields).some(
      (field) => !field.value.trim().length
    );

    if (hasEmpty) {
      event.preventDefault();
      alert("Vui lòng nhập đầy đủ thông tin!");
      return;
    }

    const successMessage = form.dataset.successMessage;
    if (successMessage) {
      event.preventDefault();
      alert(successMessage);
      form.reset();
    }
  });
});

const tabButtons = document.querySelectorAll("[data-tab-target]");
const tabPanels = document.querySelectorAll("[data-tab-panel]");

function setActiveTab(targetId) {
  if (!targetId) return;

  tabButtons.forEach((button) => {
    const isActive = button.dataset.tabTarget === targetId;
    button.classList.toggle("active", isActive);
  });

  tabPanels.forEach((panel) => {
    const isMatch = panel.dataset.tabPanel === targetId;
    const displayType = panel.dataset.display || "block";
    panel.style.display = isMatch ? displayType : "none";
  });
}

if (tabButtons.length && tabPanels.length) {
  const defaultTab =
    document.querySelector(".tab-button.active")?.dataset.tabTarget ||
    tabButtons[0].dataset.tabTarget;

  setActiveTab(defaultTab);

  tabButtons.forEach((button) => {
    button.addEventListener("click", () => {
      setActiveTab(button.dataset.tabTarget);
    });
  });
}

// ========== MODAL FUNCTIONS ==========
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add("active");
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove("active");
  }
}

// Close modal when clicking outside
window.addEventListener("click", (event) => {
  if (event.target.classList.contains("modal")) {
    event.target.classList.remove("active");
  }
});


function openChangePasswordModal() {
  openModal("passwordModal");
}

// The rest of the booking and appointment logic is now handled server-side
// via the PHP modules (Booking.php, MyAppointments.php).


// ========== SLIDER FUNCTIONALITY (CHUNG CHO CẢ 3 KHU VỰC) ==========
function initSliders() {
  const sliderContainers = document.querySelectorAll(".slider-container");

  sliderContainers.forEach((container) => {
    const sliderWrapper = container.querySelector(".slider-wrapper");
    const sliderTrack = container.querySelector(".slider-track");
    const leftBtn = container.querySelector(".slider-btn-left");
    const rightBtn = container.querySelector(".slider-btn-right");

    if (!sliderWrapper || !sliderTrack || !leftBtn || !rightBtn) {
      return;
    }

    // Hàm cập nhật trạng thái nút
    function updateButtonStates() {
      const scrollLeft = sliderWrapper.scrollLeft;
      const scrollWidth = sliderWrapper.scrollWidth;
      const clientWidth = sliderWrapper.clientWidth;

      // Ẩn/hiện nút trái
      if (scrollLeft <= 0) {
        leftBtn.disabled = true;
      } else {
        leftBtn.disabled = false;
      }

      // Ẩn/hiện nút phải
      if (scrollLeft + clientWidth >= scrollWidth - 10) {
        rightBtn.disabled = true;
      } else {
        rightBtn.disabled = false;
      }
    }

    // Cuộn trái
    leftBtn.addEventListener("click", () => {
      const cardWidth = sliderTrack.querySelector(".slider-card")?.offsetWidth || 274; // 250px + 24px gap
      sliderWrapper.scrollBy({
        left: -cardWidth,
        behavior: "smooth",
      });
    });

    // Cuộn phải
    rightBtn.addEventListener("click", () => {
      const cardWidth = sliderTrack.querySelector(".slider-card")?.offsetWidth || 274; // 250px + 24px gap
      sliderWrapper.scrollBy({
        left: cardWidth,
        behavior: "smooth",
      });
    });

    // Cập nhật trạng thái khi cuộn
    sliderWrapper.addEventListener("scroll", updateButtonStates);

    // Cập nhật trạng thái khi resize
    window.addEventListener("resize", updateButtonStates);

    // Cập nhật trạng thái ban đầu
    updateButtonStates();
  });
}

document.addEventListener("DOMContentLoaded", initSliders);

