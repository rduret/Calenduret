//Global
window.$ = window.jQuery = require('jquery');

//Style
import './styles/app.scss';
import './styles/calendar.scss';
import 'cropperjs/dist/cropper.min.css'

//JS
import 'bootstrap';
import './js/common/jquery.collection';
import './js/common/calendar.js';
import './js/common/common';

import interact from 'interactjs';
window.interact = interact;

window.Cropper = require('cropperjs');
import Upload from "/assets/js/common/upload";
window.Upload = Upload;