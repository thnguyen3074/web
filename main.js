// Menu Toggle - Mobile menu
$(document).ready(function() {
    $('.menu-toggle').on('click', function() {
        $('.nav').toggleClass('open');
        $('.auth').toggleClass('open');
    });

    // Form validation
    $('form[data-validate="true"]').on('submit', function(event) {
        const $form = $(this);
        const $fields = $form.find('input, textarea, select');
        const hasEmpty = $fields.filter(function() {
            return !$(this).val().trim().length;
        }).length > 0;

        if (hasEmpty) {
            event.preventDefault();
            alert('Vui lòng nhập đầy đủ thông tin!');
            return;
        }

        const successMessage = $form.data('success-message');
        if (successMessage) {
            event.preventDefault();
            alert(successMessage);
            $form[0].reset();
        }
    });

    // Tab switching - Generic tab system
    const $tabButtons = $('[data-tab-target]');
    const $tabPanels = $('[data-tab-panel]');

    function setActiveTab(targetId) {
        if (!targetId) return;

        $tabButtons.each(function() {
            const $button = $(this);
            const isActive = $button.data('tab-target') === targetId;
            $button.toggleClass('active', isActive);
        });

        $tabPanels.each(function() {
            const $panel = $(this);
            const isMatch = $panel.data('tab-panel') === targetId;
            const displayType = $panel.data('display') || 'block';
            $panel.css('display', isMatch ? displayType : 'none');
        });
    }

    if ($tabButtons.length && $tabPanels.length) {
        const defaultTab = $('.tab-button.active').data('tab-target') || $tabButtons.first().data('tab-target');
        setActiveTab(defaultTab);

        $tabButtons.on('click', function() {
            setActiveTab($(this).data('tab-target'));
        });
    }

    // SLIDER FUNCTIONALITY - Xử lý cuộn ngang cho danh sách bệnh viện/phòng khám
    function initSliders() {
        $('.slider-container').each(function() {
            const $container = $(this);
            const $sliderWrapper = $container.find('.slider-wrapper');
            const $sliderTrack = $container.find('.slider-track');
            const $leftBtn = $container.find('.slider-btn-left');
            const $rightBtn = $container.find('.slider-btn-right');

            if (!$sliderWrapper.length || !$sliderTrack.length || !$leftBtn.length || !$rightBtn.length) {
                return;
            }

            // Cập nhật trạng thái nút (enable/disable) dựa trên vị trí cuộn
            function updateButtonStates() {
                const scrollLeft = $sliderWrapper[0].scrollLeft;
                const scrollWidth = $sliderWrapper[0].scrollWidth;
                const clientWidth = $sliderWrapper[0].clientWidth;

                // Disable nút trái nếu đã cuộn hết về đầu
                $leftBtn.prop('disabled', scrollLeft <= 0);
                // Disable nút phải nếu đã cuộn hết về cuối (tolerance 10px)
                $rightBtn.prop('disabled', scrollLeft + clientWidth >= scrollWidth - 10);
            }

            // Cuộn trái một card
            $leftBtn.on('click', function() {
                const cardWidth = $sliderTrack.find('.slider-card').first().outerWidth() || 274;
                $sliderWrapper[0].scrollBy({ left: -cardWidth, behavior: 'smooth' });
            });

            // Cuộn phải một card
            $rightBtn.on('click', function() {
                const cardWidth = $sliderTrack.find('.slider-card').first().outerWidth() || 274;
                $sliderWrapper[0].scrollBy({ left: cardWidth, behavior: 'smooth' });
            });

            // Cập nhật khi cuộn hoặc resize window
            $sliderWrapper.on('scroll', updateButtonStates);
            $(window).on('resize', updateButtonStates);
            updateButtonStates(); // Cập nhật ngay khi load
        });
    }

    initSliders();

    // BOOKING PAGE - Date/Time Validation (UX only, validation đã xử lý trong PHP)
    function initBookingValidation() {
        const $dateInput = $('#booking-date');
        const $timeSelect = $('#booking-time');
        
        if (!$dateInput.length || !$timeSelect.length) return;
        
        const today = new Date();
        const todayDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());

        // Disable các giờ đã qua nếu chọn hôm nay (cải thiện UX)
        function updateTimeSlots() {
            const selectedDate = $dateInput.val();
            const now = new Date();
            const currentTime = now.toTimeString().slice(0, 5); // Format HH:MM

            // Reset tất cả options về enabled
            $timeSelect.find('option').prop('disabled', false);

            if (!selectedDate) return;

            // Parse ngày đã chọn và so sánh với hôm nay
            const [yearSelected, monthSelected, daySelected] = selectedDate.split('-').map(Number);
            const chosenDate = new Date(yearSelected, monthSelected - 1, daySelected);

            // Nếu chọn đúng hôm nay, disable các giờ đã qua
            if (chosenDate.getTime() === todayDate.getTime()) {
                $timeSelect.find('option').each(function() {
                    const $option = $(this);
                    if ($option.val() && $option.val() < currentTime) {
                        $option.prop('disabled', true);
                    }
                });
            }
        }

        $dateInput.on('change', updateTimeSlots);
        updateTimeSlots(); // Cập nhật ngay khi load trang
    }

    // CHANGE PASSWORD FORM Validation (UX only, validation đã xử lý trong PHP)
    function initChangePasswordValidation() {
        const $changePasswordForm = $('#changePasswordForm');
        if (!$changePasswordForm.length) return;
        
        // Client-side validation để cải thiện UX (không thay thế server-side validation)
        $changePasswordForm.on('submit', function(e) {
            const newPassword = $('#new-password').val();
            const confirmPassword = $('#confirm-password').val();
            
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
        const $registerForm = $('#registerForm');
        if (!$registerForm.length) return;
        
        // Client-side validation để cải thiện UX (không thay thế server-side validation)
        $registerForm.on('submit', function(e) {
            const password = $('#register-password').val();
            const confirmPassword = $('#register-confirm').val();
            
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
        $('.tab-button').on('click', function() {
            const tab = $(this).attr('data-tab-target');
            if (tab) {
                switchFacilityTab(tab, true);
            }
        });
    }

    function switchFacilityTab(tab, updateUrl = true) {
        $('.tab-panel').removeClass('active default-show');
        $('.tab-button').removeClass('active');
        
        const $tabPanel = $('#' + tab + '-tab');
        const $tabButton = $('[data-tab-target="' + tab + '"]');
        
        if ($tabPanel.length) $tabPanel.addClass('active');
        if ($tabButton.length) $tabButton.addClass('active');
        
        if (updateUrl) {
            window.history.pushState({}, '', 'Facility.php?tab=' + tab);
        }
    }

    // Initialize functions
    initBookingValidation();
    initChangePasswordValidation();
    initRegisterValidation();
    initFacilityTabs();
});
