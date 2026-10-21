<!-- BEGIN: Vendor JS-->

@vite([
    'resources/assets/vendor/libs/jquery/jquery.js',
    'resources/assets/vendor/libs/popper/popper.js',
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/js/bootstrap.js',
    'resources/assets/vendor/libs/node-waves/node-waves.js',
    'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
    'resources/assets/vendor/libs/hammer/hammer.js',
    'resources/assets/vendor/js/menu.js',
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/fullcalendar/fullcalendar.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/quill/katex.js',
    'resources/assets/vendor/libs/quill/quill.js',
    'resources/assets/vendor/libs/tagify/tagify.js',
    'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
    'resources/assets/vendor/libs/typeahead-js/typeahead.js',
    'resources/assets/vendor/libs/bloodhound/bloodhound.js',
    // 'resources/assets/js/ui-modals.js',
    // 'resources/js/laravel-user-management.js',
    'resources/assets/js/datatables-custom.js',
    'resources/assets/vendor/libs/cleavejs/cleave.js',
    'resources/assets/vendor/libs/cleavejs/cleave-phone.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
    'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js',
    'resources/assets/vendor/libs/pickr/pickr.js',
    'resources/assets/js/forms-selects.js',
    // 'resources/assets/js/forms-tagify.js',
    'resources/assets/js/forms-typeahead.js',
    // 'resources/assets/js/forms-pickers.js',
    'resources/assets/js/main.js',
    'resources/js/main-custom.js',
     'resources/assets/js/tables-datatables-advanced.js'


])
@stack('pricing-script')
@yield('page-script')
@stack('scripts')
