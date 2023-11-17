const CropperModal = ({id, roundCrop = false}) => {
    let cropperModal = document.createElement('div');
    cropperModal.id = id;
    if(roundCrop === true) {
        cropperModal.className = `modal fade round-crop`;
    } else {
        cropperModal.className = `modal fade`;
    }
    cropperModal.setAttribute('tabindex',  "-1");
    cropperModal.setAttribute('aria-labelledby',  "modalLabel");
    cropperModal.setAttribute('aria-hidden',  "true");
    cropperModal.innerHTML = `<div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Recadrer l'image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="img-container">
                        <img id="image-${id}" src="" alt="Image" class="w-100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary validate-crop" data-bs-dismiss="modal" aria-label="Cancel"><i class="fa fa-check"></i></button>
                </div>
            </div>
        </div>`;
    return cropperModal;
}
export default CropperModal;