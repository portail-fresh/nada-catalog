// Override propre de la fonction du plugin : wp-content/plugins/cf7-add-password-field/js/eye.js

window.pushHideButton = function (name) {
    var wrapper = document.querySelector(
        '.wpcf7-form-control-wrap[data-name="' + name + '"]'
    );
    if (!wrapper) return;

    var input = wrapper.querySelector('input');
    var btnEye = document.getElementById('buttonEye-' + name);

    if (!input || !btnEye) return;

    if (input.type === 'password') {
        input.type = 'text';
        btnEye.className = 'fa fa-eye';
    } else {
        input.type = 'password';
        btnEye.className = 'fa fa-eye-slash';
    }
};