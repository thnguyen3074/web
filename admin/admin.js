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

// Modal functions - Mở/đóng modal dialog
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = 'flex'; // Hiển thị modal
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = 'none'; // Ẩn modal
  }
}

// Close modal khi click outside (click vào backdrop)
window.addEventListener("click", (event) => {
  if (event.target.classList.contains("modal")) {
    event.target.style.display = 'none';
  }
});

// Tab switching - Chuyển đổi giữa các tab (hospital/clinic)
const tabButtons = document.querySelectorAll(".tab-btn");
tabButtons.forEach((button) => {
  button.addEventListener("click", () => {
    const targetTab = button.dataset.tab; // Lấy tab target từ data attribute

    // Xóa active từ tất cả buttons và contents
    tabButtons.forEach((btn) => btn.classList.remove("active"));
    document.querySelectorAll(".tab-content").forEach((content) => {
      content.classList.remove("active");
    });
    
    // Thêm active cho button và content được chọn
    button.classList.add("active");
    const targetContent = document.getElementById(`${targetTab}-tab`);
    if (targetContent) {
      targetContent.classList.add("active");
    }
  });
});

// Facility management
function editFacility(id) {
  alert(`Chức năng sửa cơ sở y tế ID: ${id} (Demo)`);
  openModal("facilityModal");
}

function deleteFacility(id) {
  if (confirm(`Bạn có chắc muốn xóa cơ sở y tế ID: ${id}?`)) {
    alert(`Đã xóa cơ sở y tế ID: ${id} (Demo)`);
  }
}

// Specialty management
function editSpecialty(id) {
  alert(`Chức năng sửa chuyên khoa ID: ${id} (Demo)`);
  openModal("specialtyModal");
}

function deleteSpecialty(id) {
  if (confirm(`Bạn có chắc muốn xóa chuyên khoa ID: ${id}?`)) {
    alert(`Đã xóa chuyên khoa ID: ${id} (Demo)`);
  }
}

// Appointment management
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

// User management
function editUser(id) {
  alert(`Chức năng sửa người dùng ID: ${id} (Demo)`);
}

function deleteUser(id) {
  if (confirm(`Bạn có chắc muốn xóa người dùng ID: ${id}?`)) {
    alert(`Đã xóa người dùng ID: ${id} (Demo)`);
  }
}

// Filter functionality
const filterStatus = document.getElementById("filter-status");
const filterFacility = document.getElementById("filter-facility");

if (filterStatus) {
  filterStatus.addEventListener("change", () => {
    console.log("Filter by status:", filterStatus.value);
  });
}

if (filterFacility) {
  filterFacility.addEventListener("change", () => {
    console.log("Filter by facility:", filterFacility.value);
  });
}

