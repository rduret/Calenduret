const boxWidth = 12, boxHeight = 12; //En %
const previewUrl = '/calendriers/previewBox';

document.addEventListener("DOMContentLoaded", function () {
    $(".collection-boxes").collection({
        allow_add: true,
        allow_remove: true,
        init_with_n_elements: 1,
        max: 25,
        allow_up: false,
        allow_down: false,
        add_at_the_end: true,
        fade_in: true,
        fade_out: true,
        add: "<a href='#' class='w-100 btn btn-secondary action-button'>Ajouter une case</a>",
        after_init: function () {
            updateIndexes();
        },
        after_add: function (collection, element) {
            //Ajout d'un id temporaire
            const idTemp = generateRandomId(5);
            element[0].dataset.id = idTemp;

            //Required sur l'input du fichier en cas de nouvelle case
            element[0].querySelector('input[type=file]').required = true;

            drawBox(element[0]);
            updateIndexes();
        },
        after_remove: function (collection, element) {
            updateIndexes();
            let modelBox = document.querySelector(`.model-box[data-id="${element[0].dataset.id}"]`);
            modelBox.remove();
        },
        // after_up: function () {
        //     updateIndexes();
        // },
        // after_down: function () {
        //     updateIndexes();
        // },
    });

    interact('.model-box').draggable({
        listeners: {
            move(event) {
                const modelBox = event.target;
                const x = (parseFloat(modelBox.getAttribute('data-x')) || 0) + event.dx;
                const y = (parseFloat(modelBox.getAttribute('data-y')) || 0) + event.dy;

                modelBox.style.transform = `translate(${x}px, ${y}px)`;

                modelBox.setAttribute('data-x', x);
                modelBox.setAttribute('data-y', y);

                //Récupération des valeurs en pourcentage 
                const containerRect = modelBox.parentElement.getBoundingClientRect();
                let top = parseFloat(modelBox.style.top);
                let left = parseFloat(modelBox.style.left);

                const xPercentage = Math.round(((x) / containerRect.width) * 100 + left);
                const yPercentage = Math.round(((y) / containerRect.height) * 100 + top);


                let modelBoxForm = document.querySelector(`.box-form-container[data-id="${modelBox.dataset.id}"]`);
                modelBoxForm.querySelector('.coordX').value = xPercentage;
                modelBoxForm.querySelector('.coordY').value = yPercentage;
            },
        },
        modifiers: [
            interact.modifiers.restrictRect({
                restriction: 'parent',
                endOnly: false,
            }),
        ],
    })

    let previewModal = document.getElementById('previewModal');
    if (previewModal != null) {
        previewModal.addEventListener('shown.bs.modal', function (event) {
            let button = event.relatedTarget;
            let modelBoxId = button.getAttribute('data-bs-id');

            let params = new URLSearchParams();
            params.append('id', modelBoxId);

            fetch(`${previewUrl}?${params.toString()}`)
            .then(response => response.ok ? response.json() : new Error('Impossible d\'afficher l\'aperçu'))
            .then(data => {
                let previewModalContent = previewModal.querySelector('.modal-body');
                previewModalContent.innerHTML = data;
            })
        })

        previewModal.addEventListener('hidden.bs.modal', function () {
            let previewModalContent = previewModal.querySelector('.modal-body');
            previewModalContent.innerHTML = `<div class="spinner-grow" role="status"></div>
            <div class="spinner-grow" role="status"></div>
            <div class="spinner-grow" role="status"></div>`;
        })
    }

});

//Met à jour les numéros des cases
function updateIndexes() {
    let index = 1;
    let boxForms = document.querySelectorAll('.box-form-container');

    boxForms.forEach(boxForm => {
        boxForm.querySelector('.index').innerHTML = index;
        boxForm.querySelector('.position').value = index;
        if (boxForm.dataset.id !== undefined) {
            let modelBox = document.querySelector(`.model-box[data-id="${boxForm.dataset.id}"]`);
            if (modelBox !== null) {
                modelBox.firstElementChild.innerHTML = index;
            }
        }

        index++;
    });
}

//Ajoute une case sur l'aperçu du calendrier
function drawBox(boxForm) {
    const stepX = 5, stepY = 5;
    const maxTryCount = (100 / stepX) * (100 / stepY);

    let coordX = 5, coordY = 5;
    let tryCount = 0;

    while (!checkAvailability(coordX, coordY) && tryCount < maxTryCount) {
        //Si pas de dispo on reteste plus loin vers la droite en vérifiant que l'on ne dépasse pas les 100%
        if (coordX + stepX <= 95 - boxWidth) {
            coordX = coordX + stepX;
        } else if (coordY + stepY <= 95 - boxHeight) {
            coordX = 5;
            coordY = coordY + stepY;
        } else {
            console.log('Calendrier saturé');
            break;
        }

        tryCount++;
    }

    if (tryCount === maxTryCount) {
        console.log('Calendrier saturé');
    }

    //Ajout de la case
    let newModelBox = document.createElement("div");
    let modelBoxNumber = document.createElement("div");

    modelBoxNumber.classList.add("model-box-number");
    newModelBox.appendChild(modelBoxNumber);

    newModelBox.classList.add('model-box');
    newModelBox.dataset.id = boxForm.dataset.id;
    newModelBox.style.top = coordY + '%';
    newModelBox.style.left = coordX + '%';

    document.getElementById('calendar').appendChild(newModelBox);

    boxForm.querySelector('.coordX').value = coordX;
    boxForm.querySelector('.coordY').value = coordY;
}

//Permet de vérifier si un emplacement est disponible avec les coordonnées du point superieur gauche
function checkAvailability(x, y) {
    let available = true;
    let modelBoxes = document.querySelectorAll('.model-box');

    modelBoxes.forEach(function (modelBox) {
        let left = parseInt(modelBox.style.left);
        let top = parseInt(modelBox.style.top);

        if ((left >= (x - boxWidth)) && (left < (x + boxWidth)) && (top >= (y - boxHeight)) && (top < (y + boxHeight))) {
            available = false;
        }


    });

    return available;
}

function generateRandomId(length) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let randomId = '';
    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length);
        randomId += characters.charAt(randomIndex);
    }
    return randomId;
}

