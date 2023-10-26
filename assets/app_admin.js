//Style
import './styles/app_admin.scss';
import './styles/calendar.scss';


//Js
import './js/common/jquery.collection';
import './js/common/calendar.js';
import './js/common/common';

//InteractJS
import interact from 'interactjs';
window.interact = interact;

//ColorPicker
import '@simonwep/pickr/dist/themes/monolith.min.css';  // 'monolith' theme
import Pickr from '@simonwep/pickr';
window.Pickr = Pickr;

const dataTableFr = require('./js/datatable-fr.json');
const Swal = require("sweetalert2");

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

// Datatables Responsive
$("table.auto-table").DataTable({
    responsive: true,
    language: dataTableFr,
    stateSave: true,
    drawCallback: function () {
        updateConfirmListeners();
    }
}).on('responsive-display', function () {
    updateConfirmListeners();
});

const sweetConfirm = function (e) {
    e.preventDefault();
    let href = '#';
    if (this.hasOwnProperty('href')) {
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
        confirmButtonColor: "#DD6B55",
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