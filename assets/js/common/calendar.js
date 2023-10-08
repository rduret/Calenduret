document.addEventListener("DOMContentLoaded", function () {
    $(".collection-boxes").collection({
        allow_add: true,
        allow_remove: true,
        init_with_n_elements: 1,
        max: 25,
        allow_up: false,
        allow_down: false,
        add_at_the_end: true,
        position_field_selector: '.position',
        fade_in: true,
        fade_out: true,
        prototype_name: "{{ form.modelBoxes.vars.prototype.vars.name }}",
        name_prefix: "{{ form.modelBoxes.vars.prototype.vars.full_name }}",
        add: "<a href='#' class='btn btn-warning action-button'>Ajouter une case</a>",
        remove: "<a href='#' class='btn btn-danger action-button'>Supprimer la case</a>",
        up: '<a href="#" class="btn btn-primary"><span class="fa fa-arrow-alt-circle-up"></span> Monter la case</a>',
        down: '<a href="#" class="btn btn-primary"><span class="fa fa-arrow-alt-circle-down"></span> Descendre la case</a>',
        after_init: function () {
            updateIndexes();
        },
        after_add: function () {
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
});

const updateIndexes = function () {
    let index = 1;
    let boxForms = document.querySelectorAll('.box-form-container');

    boxForms.forEach(boxForm => {
        boxForm.querySelector('.index').innerHTML = index;
        if (boxForm.dataset.id !== undefined) {
            let modelBox = document.querySelector(`.model-box[data-id="${boxForm.dataset.id}"]`);
            modelBox.firstElementChild.innerHTML = index;
        }

        index++;
    });
}