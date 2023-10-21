// Global
window.$ = window.jQuery = require('jquery');


import '../scss/template_admin.scss';

import "./modules/bootstrap";
import "./modules/theme";
import "./modules/sidebar";
import "./modules/notyf";
import "./modules/moment";
import "./modules/flatpickr";
import "./modules/select2"; // requires jQuery

// Charts
import "./modules/apexcharts";

// Tables
import "./modules/datatables"; // requires jQuery

//Cropper & upload
import 'cropperjs/dist/cropper.min.css'

window.Cropper = require('cropperjs');
import Upload from "/assets/js/common/upload";
window.Upload = Upload;