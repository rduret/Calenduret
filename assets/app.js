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
import "./template/admin/js/modules/notyf";

//InteractJS
import interact from 'interactjs';
window.interact = interact;

//Cropper et Upload
window.Cropper = require('cropperjs');
import Upload from "/assets/js/common/upload";
window.Upload = Upload;

//ColorPicker
import '@simonwep/pickr/dist/themes/monolith.min.css';  // 'monolith' theme
import Pickr from '@simonwep/pickr';
window.Pickr = Pickr;

//Sweetalert2
const Swal = require("sweetalert2");

//Html2Canvas
import html2canvas from 'html2canvas';
window.html2canvas = html2canvas;

const updateConfirmListeners = function () {
    let confirmElements = document.querySelectorAll(".confirm");
    confirmElements.forEach(function (confirmElement) {
        if (confirmElement.nodeName === "FORM") {
            confirmElement.removeEventListener('submit', sweetConfirm);
            confirmElement.addEventListener('submit', sweetConfirm);
        } else {
            confirmElement.removeEventListener('click', sweetConfirm);
            confirmElement.addEventListener('click', sweetConfirm);
        }
    });
}

const sweetConfirm = function (e) {
    e.preventDefault();
    let href = '#';
    if (this.getAttribute('href')) {
        href = this.href;
    } else if (this.dataset.hasOwnProperty('href')) {
        href = this.dataset.href
    }


    let msg = "Confirmer ?"
    if (this.dataset.hasOwnProperty('msg')) {
        msg = this.dataset.msg;
    }
    Swal.fire({
        text: msg,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#841B1B",
        confirmButtonText: "Confirmer",
        cancelButtonText: "Annuler"
    }).then((confirm) => {
        if (confirm.value === true) {
            if (e.type === 'submit') {
                e.target.submit();
            } else {
                window.location.href = href;
            }
        }
    });
}

updateConfirmListeners();