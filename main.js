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

// MODAL FUNCTIONS
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

// Close modal khi click outside
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


// SLIDER FUNCTIONALITY - Xử lý cuộn ngang cho danh sách bệnh viện/phòng khám
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

    // Cập nhật trạng thái nút (enable/disable) dựa trên vị trí cuộn
    function updateButtonStates() {
        const scrollLeft = sliderWrapper.scrollLeft;
        const scrollWidth = sliderWrapper.scrollWidth;
        const clientWidth = sliderWrapper.clientWidth;

        // Disable nút trái nếu đã cuộn hết về đầu
        leftBtn.disabled = scrollLeft <= 0;
        // Disable nút phải nếu đã cuộn hết về cuối (tolerance 10px)
        rightBtn.disabled = scrollLeft + clientWidth >= scrollWidth - 10;
    }

    // Cuộn trái một card
    leftBtn.addEventListener("click", () => {
        const cardWidth = sliderTrack.querySelector(".slider-card")?.offsetWidth || 274; // Default width nếu không tìm thấy
        sliderWrapper.scrollBy({ left: -cardWidth, behavior: "smooth" });
    });

    // Cuộn phải một card
    rightBtn.addEventListener("click", () => {
        const cardWidth = sliderTrack.querySelector(".slider-card")?.offsetWidth || 274;
        sliderWrapper.scrollBy({ left: cardWidth, behavior: "smooth" });
    });

    // Cập nhật khi cuộn hoặc resize window
    sliderWrapper.addEventListener("scroll", updateButtonStates);
    window.addEventListener("resize", updateButtonStates);
    updateButtonStates(); // Cập nhật ngay khi load
  });
}

document.addEventListener("DOMContentLoaded", initSliders);

// BOOKING PAGE - Date/Time Validation (UX only, validation đã xử lý trong PHP)
function initBookingValidation() {
    const dateInput = document.getElementById('booking-date');
    const timeSelect = document.getElementById('booking-time');
    
    if (!dateInput || !timeSelect) return;
    
    const today = new Date();
    const todayDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());

    // Disable các giờ đã qua nếu chọn hôm nay (cải thiện UX)
    function updateTimeSlots() {
        const selectedDate = dateInput.value;
        const now = new Date();
        const currentTime = now.toTimeString().slice(0, 5); // Format HH:MM

        // Reset tất cả options về enabled
        for (let option of timeSelect.options) {
            option.disabled = false;
        }

        if (!selectedDate) return;

        // Parse ngày đã chọn và so sánh với hôm nay
        const [yearSelected, monthSelected, daySelected] = selectedDate.split('-').map(Number);
        const chosenDate = new Date(yearSelected, monthSelected - 1, daySelected);

        // Nếu chọn đúng hôm nay, disable các giờ đã qua
        if (chosenDate.getTime() === todayDate.getTime()) {
            for (let option of timeSelect.options) {
                if (option.value && option.value < currentTime) {
                    option.disabled = true;
                }
            }
        }
    }

    dateInput.addEventListener('change', updateTimeSlots);
    updateTimeSlots(); // Cập nhật ngay khi load trang
}

// CHANGE PASSWORD FORM Validation (UX only, validation đã xử lý trong PHP)
function initChangePasswordValidation() {
    const changePasswordForm = document.getElementById('changePasswordForm');
    if (!changePasswordForm) return;
    
    // Client-side validation để cải thiện UX (không thay thế server-side validation)
    changePasswordForm.addEventListener('submit', function(e) {
        const newPassword = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        // Kiểm tra độ dài mật khẩu
        if (newPassword.length < 6) {
            e.preventDefault();
            alert('Mật khẩu mới phải có ít nhất 6 ký tự.');
            return false;
        }
        
        // Kiểm tra password và confirm password khớp nhau
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Mật khẩu mới và xác nhận không khớp. Vui lòng thử lại.');
            return false;
        }
    });
}

// REGISTER FORM Validation (UX only, validation đã xử lý trong PHP)
function initRegisterValidation() {
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return;
    
    // Client-side validation để cải thiện UX (không thay thế server-side validation)
    registerForm.addEventListener('submit', function(e) {
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('register-confirm').value;
        
        // Kiểm tra password và confirm password khớp nhau
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Mật khẩu xác nhận không khớp. Vui lòng thử lại.');
            return false;
        }
    });
}

// FACILITY PAGE - Tab Switching
function initFacilityTabs() {
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tab = this.getAttribute('data-tab-target');
            if (tab) {
                switchFacilityTab(tab, true);
            }
        });
    });
}

function switchFacilityTab(tab, updateUrl = true) {
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.remove('active', 'default-show');
    });
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    const tabPanel = document.getElementById(tab + '-tab');
    const tabButton = document.querySelector('[data-tab-target="' + tab + '"]');
    
    if (tabPanel) tabPanel.classList.add('active');
    if (tabButton) tabButton.classList.add('active');
    
    if (updateUrl) {
        window.history.pushState({}, '', 'Facility.php?tab=' + tab);
    }
}

// Initialize functions
document.addEventListener('DOMContentLoaded', function() {
    initBookingValidation();
    initChangePasswordValidation();
    initRegisterValidation();
    initFacilityTabs();
});

