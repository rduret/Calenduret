import CropperModal from './cropper-modal'

class Upload {
    _allowedCropperTypes = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ]
    _baseInputData = {
        input: null,
        previewTarget: null,
        preview: null,
        cropperTarget: null,
        cropperInput: null,
        cropper: null,
        buttonTarget: null,
        button: null,
        modal: null,
        croppable: false,
        rounded: false,
        ratio: null
    }
    _inputsData = []

    _getRoundedCanvas(sourceCanvas) {
        let canvas = document.createElement('canvas');
        let context = canvas.getContext('2d');
        let width = sourceCanvas.width;
        let height = sourceCanvas.height;

        canvas.width = width;
        canvas.height = height;
        context.imageSmoothingEnabled = true;
        context.drawImage(sourceCanvas, 0, 0, width, height);
        context.globalCompositeOperation = 'destination-in';
        context.beginPath();
        context.arc(width / 2, height / 2, Math.min(width, height) / 2, 0, 2 * Math.PI, true);
        context.fill();
        return canvas;
    }

    async _readURL(source, inputData) {
        if (typeof source.files !== 'undefined' && source.files.length !== 0) {
            if (this._allowedCropperTypes.includes(source.files[0].type)) {
                if (typeof source.nextSibling.classList !== `undefined`) {
                    if (source.nextSibling.classList.contains(`no-preview`)) {
                        source.parentNode.removeChild(source.nextSibling);
                    }
                }
                inputData.preview.style.display = `initial`;
                let content = await this._readUploadedFile(source.files[0])
                if (typeof inputData.preview.src !== 'undefined') {
                    inputData.preview.src = content;
                }

                return {
                    inputData: inputData,
                    croppable: true
                }
            } else {
                let span = document.createElement(`span`);
                span.className = `no-preview text text-danger`;
                span.innerHTML = `Preview indisponible`;

                source.parentNode.insertBefore(span, source.nextSibling);
                inputData.preview.style.display = `none`;

                return {
                    inputData: inputData,
                    croppable: false
                }
            }
        }
    }

    _addCropperElements(inputData) {
        if (inputData.buttonTarget !== null) {
            inputData.button = document.querySelector(inputData.buttonTarget);
        }

        if (inputData.button === null) {
            let button = document.createElement('button');
            button.type = `button`;
            button.id = `cropper-button-${inputData.preview.id}`;
            button.className = `btn btn-primary btn-crop mt-3`;
            button.innerHTML = `Recadrer l'image`;
            inputData.preview.parentNode.insertBefore(button, inputData.preview.nextSibling);
            inputData.button = button;
        }

        inputData.button.dataset.bsToggle = `modal`;
        inputData.button.dataset.bsTarget = `#cropper-modal-${inputData.preview.id}`;

        let cropperModal = CropperModal({ id: `cropper-modal-${inputData.preview.id}`, roundCrop: inputData.rounded });
        inputData.button.parentNode.insertBefore(cropperModal, inputData.button.nextSibling);

        let cropper = null;
        let doCrop = false;
        let image = document.getElementById(`image-cropper-modal-${inputData.preview.id}`);
        if (inputData.preview.dataset.hasOwnProperty('cropSrc')) {
            image.src = inputData.preview.dataset.cropSrc;
        } else {
            image.src = inputData.preview.src;
        }
        inputData.modal = document.getElementById(`cropper-modal-${inputData.preview.id}`);
        inputData.modal.addEventListener('shown.bs.modal', () => {
            let aspectRatio = "NaN";
            if (inputData.rounded) {
                aspectRatio = 1;
            } else if (inputData.ratio !== null) {
                aspectRatio = inputData.ratio
            }
            cropper = new Cropper(image, {
                aspectRatio: aspectRatio,
                viewMode: 1
            });
        });

        inputData.modal.querySelector('.validate-crop').addEventListener('click', () => {
            doCrop = true;
        });

        inputData.modal.addEventListener('hidden.bs.modal', (e) => {
            if (doCrop) {
                //Cropped Image
                let croppedCanvas = cropper.getCroppedCanvas();

                if (inputData.rounded) {
                    //Rounded Image
                    croppedCanvas = this._getRoundedCanvas(croppedCanvas)
                }

                let dataUrl = croppedCanvas.toDataURL();
                inputData.preview.src = dataUrl;
                inputData.cropperInput.value = dataUrl;
            }
            cropper.destroy();
            doCrop = false;
        });
    }

    _removeCropperElements(inputData) {
        if (inputData.button !== null) {
            if (inputData.buttonTarget !== null) {
                inputData.button = null;
            } else {
                inputData.button.parentNode.removeChild(inputData.button);
            }
        }
        let modal = document.getElementById(`cropper-modal-${inputData.preview.id}`);
        if (inputData.modal !== null) {
            modal.parentNode.removeChild(modal);
        }
    }

    _readUploadedFile(file) {
        let reader = new FileReader();

        return new Promise((resolve) => {
            reader.onload = (e) => {
                resolve(e.target.result);
            }
            reader.readAsDataURL(file); // convert to base64 string
        });
    }

    async _parseInputs({ inputs, croppable = false }) {
        if (inputs.length > 0) {
            inputs.forEach((input) => {
                if (input.dataset.hasOwnProperty('previewTarget')) {
                    //Copie de l'objet de base d'input
                    let inputData = { ...this._baseInputData };
                    inputData.input = input;
                    inputData.croppable = croppable;

                    if (input.dataset.hasOwnProperty('cropperRounded')) {
                        inputData.rounded = input.dataset.cropperRounded === "true";
                    }
                    if (input.dataset.hasOwnProperty('cropperRatio')) {
                        inputData.ratio = Number(input.dataset.cropperRatio);
                    }
                    if (input.dataset.hasOwnProperty('cropperWidth') && input.dataset.hasOwnProperty('cropperHeight')) {
                        inputData.ratio = Number(input.dataset.cropperWidth) / Number(input.dataset.cropperHeight);
                    }

                    //Récupération de l'élément de preview
                    inputData.previewTarget = input.dataset.previewTarget;
                    let preview = document.querySelector(input.dataset.previewTarget);
                    if (preview === null) {
                        notyf.open({ type: 'error', message: `Impossible de trouver l'élément de preview: ${input.dataset.previewTarget}`, dismissible: true });
                        return false
                    } else {
                        inputData.previewTarget = input.dataset.previewTarget;
                        inputData.preview = preview;
                    }

                    //Récupération de l'élément permettant l'envoi de l'image cropper
                    if (croppable && input.dataset.hasOwnProperty('croppedTarget')) {
                        inputData.cropperTarget = input.dataset.croppedTarget;
                        let cropInput = document.querySelector(input.dataset.croppedTarget);
                        if (cropInput === null) {
                            notyf.open({ type: 'error', message: `Impossible de trouver le champs: ${input.dataset.croppedTarget}`, dismissible: true });
                            return false
                        } else {
                            inputData.cropperTarget = input.dataset.croppedTarget;
                            inputData.cropperInput = cropInput;
                        }
                    } else if (croppable) {
                        notyf.open({ type: 'error', message: `Missing crop input target`, dismissible: true });
                        return false;
                    }

                    this._inputsData[input.id] = inputData;
                } else {
                    notyf.open({ type: 'error', message: `Missing preview target`, dismissible: true });
                    return false;
                }
            })
        }
    }

    async _parsePreviews({ previews, croppable = false }) {
        if (previews.length > 0) {
            previews.forEach((previewEl) => {
                if (!this._inputsData.hasOwnProperty(previewEl.id)) {
                    //Copie de l'objet de base d'input
                    let inputData = { ...this._baseInputData };
                    inputData.input = previewEl;
                    inputData.croppable = croppable;

                    if (previewEl.dataset.hasOwnProperty('cropperButtonTarget')) {
                        let cropperButton = document.querySelector(previewEl.dataset.cropperButtonTarget);
                        if (cropperButton !== null) {
                            inputData.buttonTarget = previewEl.dataset.cropperButtonTarget;
                        }
                    }
                    if (previewEl.dataset.hasOwnProperty('cropperRounded')) {
                        inputData.rounded = previewEl.dataset.cropperRounded === "true";
                    }
                    if (previewEl.dataset.hasOwnProperty('cropperRatio')) {
                        inputData.ratio = Number(previewEl.dataset.cropperRatio);
                    }
                    if (previewEl.dataset.hasOwnProperty('cropperWidth') && previewEl.dataset.hasOwnProperty('cropperHeight')) {
                        inputData.ratio = Number(previewEl.dataset.cropperWidth) / Number(previewEl.dataset.cropperHeight);
                    }

                    //Récupération de l'élément de preview
                    inputData.previewTarget = previewEl.id;
                    inputData.preview = previewEl;

                    //Récupération de l'élément permettant l'envoi de l'image cropper
                    if (croppable && previewEl.dataset.hasOwnProperty('croppedTarget')) {
                        inputData.cropperTarget = previewEl.dataset.croppedTarget;
                        let cropInput = document.querySelector(previewEl.dataset.croppedTarget);
                        if (cropInput === null) {
                            notyf.open({
                                type: 'error',
                                message: `Impossible de trouver le champs: ${input.dataset.croppedTarget}`,
                                dismissible: true
                            });
                            return false
                        } else {
                            inputData.cropperTarget = previewEl.dataset.croppedTarget;
                            inputData.cropperInput = cropInput;
                        }
                    } else if (croppable) {
                        notyf.open({ type: 'error', message: `Missing crop input target`, dismissible: true });
                        return false;
                    }

                    this._inputsData[previewEl.id] = inputData;
                }
            });
        }
    }

    uploadAndPreview({ input }) {
        let els = document.querySelectorAll(`input${input}`);
        //Vérification sur le paramètrage des inputs
        this._parseInputs({ inputs: els, croppable: false }).then(() => {
            //On utilise uniquement les inputs bien paramétrés
            for (const inputData of Object.values(this._inputsData)) {
                //On écoute le changement de fichier sur l'input
                inputData.input.addEventListener("change", (ev) => {
                    let currentInputData = this._inputsData[ev.target.id];
                    this._readURL(ev.target, currentInputData);
                });
            }
        });
    }

    uploadAndCropPreview({ input }) {
        let els = document.querySelectorAll(`input${input}`);

        //Vérification sur le paramètrage des inputs
        this._parseInputs({ inputs: els, croppable: true }).then(() => {

            //On utilise uniquement les inputs bien paramétrés
            for (const inputData of Object.values(this._inputsData)) {
                //On écoute le changement de fichier sur l'input
                inputData.input.addEventListener('change', (ev) => {
                    let currentInputData = this._inputsData[ev.target.id];
                    this._readURL(ev.target, currentInputData).then(({ inputData, croppable }) => {
                        //Remove modal and button for cropper
                        this._removeCropperElements(inputData);

                        if (croppable && inputData.croppable) {
                            //Add modal and button for cropper
                            this._addCropperElements(inputData);
                        }
                    });
                })
            }
        });
    }

    cropPreview({ previews }) {
        let els = document.querySelectorAll(`img${previews}`);

        //Vérification sur le paramètrage des previews
        this._parsePreviews({ previews: els, croppable: true }).then(() => {

            //On utilise uniquement les inputs bien paramétrés
            for (const inputData of Object.values(this._inputsData)) {
                //Remove modal and button for cropper
                this._removeCropperElements(inputData);
                this._addCropperElements(inputData);
            }
        });
    }
}
export default Upload