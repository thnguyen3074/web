const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("visible");
        observer.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.2 }
);

document.querySelectorAll(".reveal").forEach((el) => observer.observe(el));

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

// User Profile functions
function openEditModal() {
  openModal("editModal");
}

function openChangePasswordModal() {
  openModal("passwordModal");
}

// ========== URL PARAMS FUNCTIONS ==========
function getURLParams() {
  const params = new URLSearchParams(window.location.search);
  return {
    facility: params.get("facility") || "",
    specialty: params.get("specialty") || "",
    date: params.get("date") || "",
    time: params.get("time") || "",
    name: params.get("name") || "",
    phone: params.get("phone") || "",
    email: params.get("email") || "",
    symptoms: params.get("symptoms") || "",
  };
}

// ========== BOOKING CONFIRM PAGE ==========
if (window.location.pathname.includes("BookingConfirm.html")) {
  document.addEventListener("DOMContentLoaded", () => {
    const params = getURLParams();
    
    document.getElementById("confirm-facility").textContent = params.facility || "-";
    document.getElementById("confirm-specialty").textContent = params.specialty || "-";
    document.getElementById("confirm-date").textContent = params.date || "-";
    document.getElementById("confirm-time").textContent = params.time || "-";
    document.getElementById("confirm-name").textContent = params.name || "-";
    document.getElementById("confirm-email").textContent = params.email || "-";
    document.getElementById("confirm-phone").textContent = params.phone || "-";
    document.getElementById("confirm-symptoms").textContent = params.symptoms || "-";
  });
}

function confirmBooking() {
  const params = getURLParams();
  const queryString = new URLSearchParams(params).toString();
  window.location.href = `BookingSuccess.html?${queryString}`;
}

// ========== BOOKING SUCCESS PAGE ==========
if (window.location.pathname.includes("BookingSuccess.html")) {
  document.addEventListener("DOMContentLoaded", () => {
    const params = getURLParams();
    
    document.getElementById("success-facility").textContent = params.facility || "-";
    document.getElementById("success-specialty").textContent = params.specialty || "-";
    document.getElementById("success-date").textContent = params.date || "-";
    document.getElementById("success-time").textContent = params.time || "-";
  });
}

// ========== APPOINTMENTS DATA ==========
const appointmentsData = [
  {
    id: 1,
    facility: "Bệnh viện Quốc tế Medicare",
    specialty: "Tim mạch",
    date: "20/12/2024",
    time: "08:00",
    symptoms: "Đau ngực, khó thở",
    status: "upcoming",
  },
  {
    id: 2,
    facility: "Bệnh viện Đa khoa Tâm Đức",
    specialty: "Da liễu",
    date: "25/12/2024",
    time: "09:30",
    symptoms: "Nổi mẩn đỏ trên da",
    status: "upcoming",
  },
  {
    id: 3,
    facility: "Phòng khám Tim mạch An Tâm",
    specialty: "Tim mạch",
    date: "10/12/2024",
    time: "10:00",
    symptoms: "Khám định kỳ",
    status: "completed",
  },
  {
    id: 4,
    facility: "Bệnh viện Chuyên tim An Tâm",
    specialty: "Tim mạch",
    date: "05/12/2024",
    time: "14:00",
    symptoms: "Tái khám",
    status: "cancelled",
  },
];

// ========== RENDER APPOINTMENTS ==========
function renderAppointments() {
  const upcomingContainer = document.getElementById("upcoming-appointments");
  const completedContainer = document.getElementById("completed-appointments");

  if (!upcomingContainer || !completedContainer) return;

  const upcoming = appointmentsData.filter((apt) => apt.status === "upcoming");
  const completed = appointmentsData.filter(
    (apt) => apt.status === "completed" || apt.status === "cancelled"
  );

  upcomingContainer.innerHTML = upcoming
    .map(
      (apt) => `
    <div class="appointment-card">
      <div class="appointment-header">
        <div class="appointment-facility">${apt.facility}</div>
        <span class="appointment-status upcoming">Sắp tới</span>
      </div>
      <div class="appointment-info">
        <div class="appointment-info-item">
          <strong>Chuyên khoa:</strong>
          <span>${apt.specialty}</span>
        </div>
        <div class="appointment-info-item">
          <strong>Ngày khám:</strong>
          <span>${apt.date}</span>
        </div>
        <div class="appointment-info-item">
          <strong>Giờ khám:</strong>
          <span>${apt.time}</span>
        </div>
        <div class="appointment-info-item">
          <strong>Triệu chứng:</strong>
          <span>${apt.symptoms}</span>
        </div>
      </div>
      <div class="appointment-actions">
        <button class="btn-delete" onclick="cancelAppointment(${apt.id})">Hủy lịch</button>
      </div>
    </div>
  `
    )
    .join("");

  completedContainer.innerHTML = completed
    .map(
      (apt) => `
    <div class="appointment-card">
      <div class="appointment-header">
        <div class="appointment-facility">${apt.facility}</div>
        <span class="appointment-status ${
          apt.status === "completed" ? "completed" : "cancelled"
        }">${apt.status === "completed" ? "Đã khám" : "Đã hủy"}</span>
      </div>
      <div class="appointment-info">
        <div class="appointment-info-item">
          <strong>Chuyên khoa:</strong>
          <span>${apt.specialty}</span>
        </div>
        <div class="appointment-info-item">
          <strong>Ngày khám:</strong>
          <span>${apt.date}</span>
        </div>
        <div class="appointment-info-item">
          <strong>Giờ khám:</strong>
          <span>${apt.time}</span>
        </div>
        <div class="appointment-info-item">
          <strong>Triệu chứng:</strong>
          <span>${apt.symptoms}</span>
        </div>
      </div>
    </div>
  `
    )
    .join("");
}

// ========== CANCEL APPOINTMENT ==========
function cancelAppointment(id) {
  if (confirm("Bạn có chắc muốn hủy lịch hẹn này?")) {
    const appointment = appointmentsData.find((apt) => apt.id === id);
    if (appointment) {
      appointment.status = "cancelled";
      renderAppointments();
      alert("Đã hủy lịch hẹn thành công!");
    }
  }
}

// ========== INITIALIZE APPOINTMENTS PAGE ==========
if (window.location.pathname.includes("MyAppointments.html")) {
  document.addEventListener("DOMContentLoaded", renderAppointments);
}

// ========== BOOKING FORM SUBMIT ==========
const bookingForm = document.getElementById("bookingForm");
if (bookingForm) {
  bookingForm.addEventListener("submit", (event) => {
    event.preventDefault();
    
    const formData = new FormData(bookingForm);
    const facility = "Bệnh viện Quốc tế Medicare"; // Default or get from FacilityDetail
    const specialty = formData.get("specialty");
    const date = formData.get("date");
    const time = formData.get("time");
    const name = formData.get("fullname");
    const phone = formData.get("phone");
    const email = formData.get("email");
    const symptoms = formData.get("symptom");

    // Validate all fields
    if (!specialty || !date || !time || !name || !phone || !email || !symptoms) {
      alert("Vui lòng nhập đầy đủ thông tin!");
      return;
    }

    // Format date for display
    const dateObj = new Date(date);
    const formattedDate = dateObj.toLocaleDateString("vi-VN");

    // Build query string
    const params = new URLSearchParams({
      facility: facility,
      specialty: specialty,
      date: formattedDate,
      time: time,
      name: name,
      phone: phone,
      email: email,
      symptoms: symptoms,
    });

    // Redirect to confirm page
    window.location.href = `BookingConfirm.html?${params.toString()}`;
  });
}

