const i18n = {
    fr: {
        required: "^Champ obligatoire",
        emailRequired: "^Email requis",
        emailInvalid: "^Email invalide",
        passwordRequired: "^Mot de passe requis",
        passwordMin: "^Minimum 12 caractères",
        passwordFormat: "^Majuscule, chiffre et caractère spécial requis",
        passwordConfirm: "^Les mots de passe ne correspondent pas",
        confirmationRequired: "^Confirmation requise"
    },
    en: {
        required: "^Required field",
        emailRequired: "^Email required",
        emailInvalid: "^Invalid email address",
        passwordRequired: "^Password required",
        passwordMin: "^Minimum 12 characters",
        passwordFormat: "^Uppercase, number and special character required",
        passwordConfirm: "^Passwords do not match",
        confirmationRequired: "^Confirmation required"
    }
};

// Détecter la langue du site
let lang = document.documentElement.lang || 'fr';
lang = lang.substring(0, 2); // "en-US" -> "en", "fr-FR" -> "fr"

// Fonction helper pour récupérer le message
const t = key => i18n[lang][key] || key;

const constraints = {
    last_name: {
        presence: { allowEmpty: false, message: t('required') }
    },
    first_name: {
        presence: { allowEmpty: false, message: t('required') }
    },
    email: {
        presence: { allowEmpty: false, message: t('emailRequired') },
        email: { message: t('emailInvalid') }
    },
    
};

const form_updateCompte = document.getElementById('editprofile');

//updateCompte
if(form_updateCompte){
    form_updateCompte.addEventListener('submit', function (e) {
        const values = validate.collectFormValues(form_updateCompte);
        
        const errors = validate(values, constraints);

        clearErrors();

        if (errors) {
            e.preventDefault();
            showErrors(errors);
        }
    });

    // Validation live
    form_updateCompte.addEventListener('input', function () {
        const values = validate.collectFormValues(form_updateCompte);
        const errors = validate(values, constraints);
        clearErrors();
        if (errors) showErrors(errors);
    });
}





// Update mot de passe

const constraints_mdp = {
    old_password: {
        presence: { allowEmpty: false, message: t('required') }
    },
    new_password: {
        presence: { allowEmpty: false, message: t('passwordRequired') },
        length: { minimum: 12, message: t('passwordMin') },
        format: {
            pattern: "^(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).+$",
            message: t('passwordFormat')
        }
    },
    confirm_password: {
        presence: { allowEmpty: false, message: t('confirmationRequired') },
        equality: { attribute: "new_password", message: t('passwordConfirm') }
    },
   
};

const form_updateMdp = document.getElementById('resetPasswordForm');

//updateCompte
if(form_updateMdp){
    form_updateMdp.addEventListener('submit', function (e) {
        const values = validate.collectFormValues(form_updateMdp);
        
        const errors = validate(values, constraints_mdp);

        clearErrors();

        if (errors) {
            e.preventDefault();
            showErrors(errors);
        }
    });

    // Validation live
    form_updateMdp.addEventListener('input', function () {
        const values = validate.collectFormValues(form_updateMdp);
        const errors = validate(values, constraints_mdp);
        clearErrors();
        if (errors) showErrors(errors);
    });
}








const constraints_loginCompte = {
    email: {
        presence: { allowEmpty: false, message: t('emailRequired') },
        email: { message: t('emailInvalid') }
    },
    password: {
        presence: { allowEmpty: false, message: t('passwordRequired') },
        length: { minimum: 12, message: t('passwordMin') },
        format: {
            pattern: "^(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).+$",
            message: t('passwordFormat')
        }
    }
}
const loginCompte = document.getElementById('loginCompte');

//loginCompte
if(loginCompte){
    loginCompte.addEventListener('submit', function (e) {
 
        const values = validate.collectFormValues(loginCompte);
        
        const errors = validate(values, constraints_loginCompte);


        clearErrors();

        if (errors) {
            e.preventDefault();
            showErrors(errors);
        }
    });

    // loginCompte : Validation live 
    loginCompte.addEventListener('input', function () {
        const values = validate.collectFormValues(loginCompte);
        const errors = validate(values, constraints_loginCompte);
        clearErrors();
        if (errors) showErrors(errors);
    });
}

const constraints_lostPasswordForm = {
    user_email: {
        presence: { allowEmpty: false, message: t('emailRequired') },
        email: { message: t('emailInvalid') }
    }
}
const lostPasswordForm = document.getElementById('lostPasswordForm');

//lostPasswordForm
if(lostPasswordForm){
    lostPasswordForm.addEventListener('submit', function (e) {
 
        const values = validate.collectFormValues(lostPasswordForm);
        
        const errors = validate(values, constraints_lostPasswordForm);


        clearErrors();

        if (errors) {
            e.preventDefault();
            showErrors(errors);
        }
    });

    // lostPasswordForm : Validation live 
    lostPasswordForm.addEventListener('input', function () {
        const values = validate.collectFormValues(lostPasswordForm);
        const errors = validate(values, constraints_lostPasswordForm);
        clearErrors();
        if (errors) showErrors(errors);
    });
}


const constraints_changePasswordForm = {
    new_password: {
        presence: { allowEmpty: false, message: t('passwordRequired') },
        length: { minimum: 12, message: t('passwordMin') },
        format: {
            pattern: "^(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).+$",
            message: t('passwordFormat')
        }
    },
    confirm_password: {
        presence: { allowEmpty: false, message: t('confirmationRequired') },
        equality: { attribute: "new_password", message: t('passwordConfirm') }
    },
}
const changePasswordForm = document.getElementById('changePasswordForm');

//changePasswordForm
if(changePasswordForm){
    changePasswordForm.addEventListener('submit', function (e) {
 
        const values = validate.collectFormValues(changePasswordForm);
        
        const errors = validate(values, constraints_changePasswordForm);


        clearErrors();

        if (errors) {
            e.preventDefault();
            showErrors(errors);
        }
    });

    // changePasswordForm : Validation live 
    changePasswordForm.addEventListener('input', function () {
        const values = validate.collectFormValues(changePasswordForm);
        const errors = validate(values, constraints_changePasswordForm);
        clearErrors();
        if (errors) showErrors(errors);
    });
}


//form-inscription-page
const constraints_inscription = {

    lastname: {
        presence: { allowEmpty: false, message: t('required') }
    },
    firstname: {
        presence: { allowEmpty: false, message: t('required') }
    },

    email: {
        presence: { allowEmpty: false, message: t('emailRequired') },
        email: { message: t('emailInvalid') }
    },

    password: {
        presence: { allowEmpty: false, message: t('passwordRequired') },
        length: { minimum: 12, message: t('passwordMin') },
        format: {
            pattern: "^(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).+$",
            message: t('passwordFormat')
        }
    },
    password_confirm: {
        presence: { allowEmpty: false, message: t('confirmationRequired') },
        equality: { attribute: "password", message: t('passwordConfirm') }
    },
}
const inscriptionForm = document.querySelector('#form-inscription-page form');

//inscriptionForm
if(inscriptionForm){
    inscriptionForm.addEventListener('submit', function (e) {
 
        const values = validate.collectFormValues(inscriptionForm);
        
        const errors = validate(values, constraints_inscription);
       

        clearErrors();

        if (errors) {
            e.preventDefault();
            showErrors(errors);
        }
    });

    // inscriptionForm : Validation live 
    inscriptionForm.addEventListener('input', function () {
        const values = validate.collectFormValues(inscriptionForm);
        const errors = validate(values, constraints_inscription);
        clearErrors();
        if (errors) showErrors(errors);
    });
}

// fonction public
function showErrors(errors) {
    for (const field in errors) {
     

        const errorDiv = document.querySelector(`[data-error-for="${field}"]`);
       
        if (errorDiv) {
            errorDiv.textContent = errors[field][0];
        }
    }
}

function clearErrors() {
    document.querySelectorAll('.error').forEach(el => el.textContent = '');
}