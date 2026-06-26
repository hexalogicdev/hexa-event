import '../assets/vendor/libs/toastr/toastr';
import '../assets/vendor/libs/toastr/toastr.scss';

const TOAST_BASE_OPTIONS = {
  closeButton: true,
  debug: false,
  newestOnTop: true,
  progressBar: true,
  preventDuplicates: true,
  showDuration: 200,
  hideDuration: 100,
  timeOut: 3200,
  extendedTimeOut: 1200,
  showEasing: 'swing',
  hideEasing: 'linear',
  showMethod: 'fadeIn',
  hideMethod: 'fadeOut'
};

function getToastPlacement() {
  return document.documentElement.getAttribute('dir') === 'rtl' ? 'toast-top-left' : 'toast-top-right';
}

function ensureToastOptions() {
  if (!window.toastr) {
    return false;
  }

  window.toastr.options = {
    ...TOAST_BASE_OPTIONS,
    positionClass: getToastPlacement()
  };

  return true;
}

function toast(message, type = 'info', title = null) {
  if (!message || !ensureToastOptions()) {
    return;
  }

  const normalizedType = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'info';
  const resolvedTitle =
    title ||
    {
      success: 'Success',
      error: 'Error',
      warning: 'Warning',
      info: 'Notice'
    }[normalizedType];

  window.toastr[normalizedType](message, resolvedTitle);
}

function pushFlashNotifications() {
  toast(window.successMessage, 'success');
  toast(window.errorMessage, 'error');

  const validationErrors = Array.isArray(window.validationErrors) ? window.validationErrors : [];
  validationErrors.forEach((error) => toast(error, 'warning', 'Validation'));
}

window.EmsFlashToast = toast;

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', pushFlashNotifications);
} else {
  pushFlashNotifications();
}
