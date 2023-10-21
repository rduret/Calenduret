const showHidePassword = () => {
    let passwordInputs = document.querySelectorAll(`.show-hide-pwd`);
    let iconEyeDefault = `far fa-eye`;
    let iconEyeSlashDefault = `far fa-eye-slash`;
    passwordInputs.forEach(function (el) {
        let container = el.querySelector(`.input-group-text`);
        let icon = el.querySelector(`.input-group-text i`);
        let iconEye = el.dataset.hasOwnProperty('iconEye') ? el.dataset.iconEye : iconEyeDefault;
        let iconEyeSlash = el.dataset.hasOwnProperty('iconEyeSlash') ? el.dataset.iconEyeSlash : iconEyeSlashDefault;
        el.style.cursor = "pointer";
        container.addEventListener('click', function (event) {
            let input = el.querySelector('input');
            let end = input.value.length;
            input.focus();
            setTimeout(function () {
                input.setSelectionRange(end, end);
            }, 0);

            if(input.type === 'password') {
                input.type = "text";
                icon.classList.remove(...iconEye.split(' '));
                icon.classList.add(...iconEyeSlash.split(' '));
            } else if(input.type === 'text') {
                input.type = "password";
                icon.classList.remove(...iconEyeSlash.split(' '));
                icon.classList.add(...iconEye.split(' '));
            }
        });
    });
}

document.addEventListener("DOMContentLoaded", function () {
    console.log("remi");
    showHidePassword();
});