// Admin JavaScript - Sử dụng jQuery

$(document).ready(function() {
    // Form validation
    $('form[data-validate="true"]').on('submit', function(event) {
        const $form = $(this);
        const $fields = $form.find('input[required], textarea[required], select[required]');
        const hasEmpty = $fields.filter(function() {
            return !$(this).val().trim().length;
        }).length > 0;

        if (hasEmpty) {
            event.preventDefault();
            alert('Vui lòng nhập đầy đủ thông tin!');
            return;
        }
    });
});
