import './bootstrap';
import { Html5Qrcode } from 'html5-qrcode';
import './flash-notifications';
/*
  Add custom scripts here
*/
import.meta.glob([
  '../assets/img/**',
  // '../assets/json/**',
  '../assets/vendor/fonts/**'
]);

window.Html5Qrcode = Html5Qrcode;
