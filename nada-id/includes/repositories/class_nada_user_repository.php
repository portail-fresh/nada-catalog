<?php
class Nada_User_Repository
{
    // Récuperer wp user id a partir nada user_id
    public function __construct()
    {
        global $wpdb;
    }

    function fetch_wp_user_id($nada_user_id)
    {
        global $wpdb;
        $wp_user_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT user_id
         FROM {$wpdb->usermeta}
         WHERE meta_key = %s
           AND meta_value = %s
         LIMIT 1",
                'nada_user_id',
                $nada_user_id
            )
        );

        return $wp_user_id;
    }
}
