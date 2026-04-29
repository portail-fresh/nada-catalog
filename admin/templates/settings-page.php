<div class="wrap">
    <h1>Réglages du plugin NADA</h1>
    <p>Bienvenue dans la page d’administration du plugin.</p>

    <form method="post" action="options.php">
        <?php 
            settings_fields('nada_settings_group');
            do_settings_sections('nada_settings');
            submit_button('Enregistrer');
        ?>
    </form>
</div>
