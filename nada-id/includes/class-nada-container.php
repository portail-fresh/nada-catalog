<?php

/**
 * ------------------------------------------------------
 * Classe Nada_Container
 * ------------------------------------------------------
 *
 * Ce fichier centralise la création et l'instanciation
 * de tous les services, repositories et controllers
 * du plugin NADA.
 *
 * Il sert de "container" pour gérer proprement les
 * dépendances entre les différentes classes du plugin.
 *
 * Exemple d'utilisation :
 *   $studyController = Nada_Container::study_controller();
 *
 * Cela permet :
 *   - de centraliser l'instanciation des objets
 *   - d'éviter les dépendances circulaires
 *   - de rendre le code plus testable et maintenable
 * */

class Nada_Container
{
    // Singleton pour les instances partagées
    private static ?Nada_Api $api = null;
    private static ?NADA_DeepL $deepl_api = null;
    private static ?Nada_Study_Repository $study_repository = null;

    // Retourne l'instance unique de l'API NADA
    public static function api(): Nada_Api
    {
        if (self::$api === null) {
            self::$api = new Nada_Api();
        }
        return self::$api;
    }

    // Retourne l'instance unique de l'API DEEPL
    public static function deepl_api(): NADA_DeepL
    {
        if (self::$deepl_api === null) {
            self::$deepl_api = new NADA_DeepL();
        }
        return self::$deepl_api;
    }

    // Retourne l'instance unique du repository d'étude
    public static function study_repository(): Nada_Study_Repository
    {
        if (self::$study_repository === null) {
            self::$study_repository = new Nada_Study_Repository();
        }
        return self::$study_repository;
    }

    // Controller pour les études
    public static function study_controller(): Nada_Study_Controller
    {
        $deepl_service = new Nada_Deepl_Service(
            self::deepl_api()
        );
        $study_parser = new Nada_Study_Input_Parser();
        $study_mapper = new Nada_Study_Api_Mapper();
        $study_service = new Nada_Study_Service(
            self::api(),
            self::study_repository(),
            $deepl_service,
            $study_parser,
            $study_mapper
        );

        return new Nada_Study_Controller($study_service);
    }

    // Controller pour l'utilisateur
    public static function user_controller(): Nada_User_Controller
    {
        $user_service = new Nada_User_Service(self::api()); // WP-aware
        $repository   = self::study_repository(); // réutilisation
        return new Nada_User_Controller($user_service, $repository);
    }
}
