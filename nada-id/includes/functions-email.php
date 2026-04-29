<?php
if (!defined('ABSPATH')) exit;

/**
 * Envoyer un e-mail formaté en utilisant un template.
 *
 * @param string $to Adresse e-mail du destinataire.
 * @param string $subject Sujet de l'e-mail.
 * @param string $body_template Chemin vers le fichier template du corps de l'e-mail.
 * @param array $data Données à insérer dans le template (variables).
 * @return bool True si l'e-mail a été envoyé avec succès, false sinon.
 */
function nada_send_email($to, $subject, $body_template, $data = [])
{
    // Charger le corps du message
    if (!file_exists($body_template)) {
        error_log("Template email introuvable : " . $body_template);
        return false;
    }
    $body = file_get_contents($body_template);

    // Remplacer les variables dans le contenu principal
    foreach ($data as $key => $value) {
        $body = str_replace("{" . $key . "}", $value, $body);
        $subject = str_replace("{" . $key . "}", $value, $subject);
    }

    // Charger le template global
    $default_template = NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-default.php';
    if (file_exists($default_template)) {
        $wrapper = file_get_contents($default_template);
        $body = str_replace("{email_content}", $body, $wrapper);
    }

    // Remplacer aussi les variables dans le template global
    foreach ($data as $key => $value) {
        $body = str_replace("{" . $key . "}", $value, $body);
    }


    // Variables globales (communes à tous les e-mails)
    //$body = str_replace("{year}", date('Y'), $body);
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    return wp_mail($to, $subject, $body, $headers);
}
