<form id="resetPasswordForm" class="<?php echo get_locale(); ?>">
<div class="modal" tabindex="-1" id="modal-reset-password" >
<div class="modal-dialog modal-dialog-centered modal-lg">
<div class="modal-content">
<div class="modal-header">
<h3 class="modal-title"> <?php echo get_locale() == "fr_FR"  ? "Modifier mon mot de passe" : "Change my password" ?></h3>
<button type="button" class="btn-close" aria-label="Fermer"></button>
</div>
<div class="modal-body">	
 <div id="password-response"></div>
<div class="blockPassword">

<div class="mb-3">			
<label class="form-label"> <?php echo get_locale() == "fr_FR" ? "Ancien mot de passe" : "Old Password" ?></label>
  
<div class="password-wrapper">
<input type="password" name="old_password" class="cep-placeholder password-field form-control" placeholder="<?php
        echo get_locale() == "fr_FR"
            ? "Veuillez entrer votre ancien mot de passe"
            : "Please enter your old password" ?>">

<span class="toggle-password">
<i class="fa-solid fa-eye-slash"></i>
</span>
</div>
<div class="input-validator error" data-error-for="old_password"></div>
</div>
</div>
<div class="mb-3">
<div class="blockPassword">
<label class="form-label"><?php
         echo get_locale() == "fr_FR" ? "Nouveau mot de passe" : "New Password"?></label>

<div class="password-wrapper">
<input type="password"  name="new_password" required class="cep-placeholder password-field form-control" placeholder="<?php
        echo get_locale() == "fr_FR"
            ? "Veuillez entrer votre nouveau mot de passe"
            : "Please enter your new password" ?>">

<span class="toggle-password">
<i class="fa-solid fa-eye-slash"></i>
</span>
</div>
<div class="input-validator error" data-error-for="new_password"></div>
</div>
</div>
<div class="mb-3">
<div class="blockPassword">
<label class="form-label"><?php
        echo get_locale() == "fr_FR"
            ? "Confirmer mot de passe"
            : "Confirm Password" ?></label>

<div class="password-wrapper">
<input type="password"  name="confirm_password" required class="cep-placeholder password-field form-control" placeholder="<?php
        echo get_locale() == "fr_FR"
            ? "Veuillez confirmer votre nouveau mot de passe"
            : "Please confirm your new password" ?>">

<span class="toggle-password">
<i class="fa-solid fa-eye-slash"></i>
</span>
</div>
<div class="input-validator error" data-error-for="confirm_password"></div>
</div>   
</div>
</div>
<div class="modal-footer">
<button type="button" class="button cancel-edit-password">
<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
<span class="btn-text"><?php
        echo get_locale() == "fr_FR"
            ? "Annuler"
            : "Reset" ?></span>
</button>
<input type="hidden" name="action" value="change_user_password">
<input type="hidden" name="lang" value="<?php echo get_locale(); ?>">

<p class="" style="text-align: right; text-transform: capitalize;">
<input class="bouton-reset-psw" type="submit" value="<?php
        echo get_locale() == "fr_FR"
            ? "Modifier"
            : "Update" ?>">   


</p>     

</div>
</div>
</div>
</div>
</form>