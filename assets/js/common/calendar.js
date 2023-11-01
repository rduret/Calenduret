const boxWidth = 12, boxHeight = 12; //En %
const previewUrl = '/calendriers/previewBox';
const shareUrl = '/calendriers/share';

document.addEventListener("DOMContentLoaded", function () {
    let collectionBoxes = $(".collection-boxes");

    collectionBoxes.collection({
        allow_add: true,
        allow_remove: true,
        init_with_n_elements: 1,
        max: 25,
        allow_up: false,
        allow_down: false,
        add_at_the_end: true,
        preserve_names: true,
        fade_in: true,
        fade_out: true,
        add: "<a href='#' class='w-100 btn btn-primary action-button'>Ajouter une case</a>",
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
    });

    //Initialisation des éléments draggables
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

    // Chargement de la modale d'aperçu 
    let previewModal = document.getElementById('previewModal');
    if (previewModal != null) {
        previewModal.addEventListener('shown.bs.modal', function (event) {
            let button = event.relatedTarget;
            let modelBoxUuid = button.getAttribute('data-bs-uuid');

            let params = new URLSearchParams();
            params.append('uuid', modelBoxUuid);

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

    //Chargement de la modale de partage
    let shareModal = document.getElementById('shareModal');
    if (shareModal != null) {
        shareModal.addEventListener('shown.bs.modal', function (event) {
            let button = event.relatedTarget;
            let modelUuid = button.getAttribute('data-bs-uuid');

            let params = new URLSearchParams();
            params.append('uuid', modelUuid);

            fetch(`${shareUrl}?${params.toString()}`)
                .then(response => response.ok ? response.json() : new Error('Impossible de générer un lien de partage'))
                .then(path => {
                    let shareModalContent = shareModal.querySelector('.modal-body');
                    shareModalContent.innerHTML = `
                    <p>Lien vers le calendrier : </p>
                    <div class="d-flex align-items-center w-100 mx-auto">
                        <div id="clipboard" class="input-group" style="cursor: pointer;">
                            <input type="text" readonly id="calendar-url" class="form-control" style="cursor: pointer;" value="${path}"/>
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
                                    <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                                    <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div id="alert-clipboard" class="alert alert-success align-items-center justify-content-center mx-auto mt-2" role="alert" style="width: fit-content; padding-top: 5px; padding-bottom: 8px; visibility: hidden;">
                        <div>
                            Lien copié dans le presse-papier
                        </div>
                    </div>`;

                    document.querySelector('#clipboard').addEventListener('click', function (event) {
                        let copyText = document.querySelector('#calendar-url');
                        copyText.select();
                        copyText.setSelectionRange(0, 99999);
                        navigator.clipboard.writeText(copyText.value);
                        document.querySelector('#alert-clipboard').style.visibility = 'visible';
                    })
                })
        })

        shareModal.addEventListener('hidden.bs.modal', function () {
            let shareModalContent = shareModal.querySelector('.modal-body');
            shareModalContent.innerHTML = `<div class="d-flex align-items-center w-50 mx-auto">
            <strong>Génération du lien...</strong>
            <div class="spinner-border spinner-border-sm ms-auto" role="status" aria-hidden="true"></div>
            </div>`;
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