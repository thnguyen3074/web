// Form validation
document.querySelectorAll('form[data-validate="true"]').forEach((form) => {
  form.addEventListener("submit", (event) => {
    const fields = form.querySelectorAll("input[required], textarea[required], select[required]");
    const hasEmpty = Array.from(fields).some(
      (field) => !field.value.trim().length
    );

    if (hasEmpty) {
      event.preventDefault();
      alert("Vui lòng nhập đầy đủ thông tin!");
      return;
    }

    // Demo success message
    const successMessage = form.dataset.successMessage;
    if (successMessage) {
      event.preventDefault();
      alert(successMessage);
      form.reset();
      closeModal("facilityModal");
      closeModal("specialtyModal");
    }
  });
});

// Modal functions
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

// Tab switching
const tabButtons = document.querySelectorAll(".tab-btn");
tabButtons.forEach((button) => {
  button.addEventListener("click", () => {
    const targetTab = button.dataset.tab;

    // Remove active class from all buttons and content
    tabButtons.forEach((btn) => btn.classList.remove("active"));
    document.querySelectorAll(".tab-content").forEach((content) => {
      content.classList.remove("active");
    });

    // Add active class to clicked button and corresponding content
    button.classList.add("active");
    const targetContent = document.getElementById(`${targetTab}-tab`);
    if (targetContent) {
      targetContent.classList.add("active");
    }
  });
});

// Facility management functions
function editFacility(id) {
  alert(`Chức năng sửa cơ sở y tế ID: ${id} (Demo)`);
  openModal("facilityModal");
}

function deleteFacility(id) {
  if (confirm(`Bạn có chắc muốn xóa cơ sở y tế ID: ${id}?`)) {
    alert(`Đã xóa cơ sở y tế ID: ${id} (Demo)`);
  }
}

// Specialty management functions
function editSpecialty(id) {
  alert(`Chức năng sửa chuyên khoa ID: ${id} (Demo)`);
  openModal("specialtyModal");
}

function deleteSpecialty(id) {
  if (confirm(`Bạn có chắc muốn xóa chuyên khoa ID: ${id}?`)) {
    alert(`Đã xóa chuyên khoa ID: ${id} (Demo)`);
  }
}

// Appointment management functions
function confirmAppointment(id) {
  if (confirm(`Xác nhận lịch hẹn ID: ${id}?`)) {
    alert(`Đã xác nhận lịch hẹn ID: ${id} (Demo)`);
  }
}

function cancelAppointment(id) {
  if (confirm(`Hủy lịch hẹn ID: ${id}?`)) {
    alert(`Đã hủy lịch hẹn ID: ${id} (Demo)`);
  }
}

// User management functions
function editUser(id) {
  alert(`Chức năng sửa người dùng ID: ${id} (Demo)`);
}

function deleteUser(id) {
  if (confirm(`Bạn có chắc muốn xóa người dùng ID: ${id}?`)) {
    alert(`Đã xóa người dùng ID: ${id} (Demo)`);
  }
}

// Filter functionality (demo)
const filterStatus = document.getElementById("filter-status");
const filterFacility = document.getElementById("filter-facility");

if (filterStatus) {
  filterStatus.addEventListener("change", () => {
    console.log("Filter by status:", filterStatus.value);
    // Implement filter logic here
  });
}

if (filterFacility) {
  filterFacility.addEventListener("change", () => {
    console.log("Filter by facility:", filterFacility.value);
    // Implement filter logic here
  });
}

