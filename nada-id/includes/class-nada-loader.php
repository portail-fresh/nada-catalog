<?php

/**
 * Classe Nada_Loader
 *
 * Cette classe centralise et enregistre les hooks (actions et filtres) du plugin.
 * Elle sert d'intermédiaire entre les classes du plugin et le cœur de WordPress.
 */
class Nada_Loader
{

    /**Liste des actions à enregistrer */
    protected array $actions = [];

    /** Liste des filtres à enregistrer */
    protected array $filters = [];

    /** Instance de la partie admin */
    private ?Nada_Admin $plugin_admin = null;

    /** Instance de la partie front */
    private ?Nada_Front $plugin_front = null;

    /**
     * Constructeur
     * Charge les dépendances nécessaires au plugin.
     */
    public function __construct()
    {
        $paths = [
            // fonctions globales
            'includes/functions-nada.php',

            // Dossiers
            'includes/services/',
            'includes/repositories/',
            'includes/controllers/',

            // Fichiers
            'includes/class-nada-container.php',
            'includes/class-nada-admin.php',
            'includes/class-nada-front.php',
            'includes/functions-email.php',
            'includes/shortcodes.php',

        ];

        foreach ($paths as $relative_path) {
            $path = NADA_ID_PLUGIN_DIR . $relative_path;

            // Fichier
            if (is_file($path)) {
                require_once $path;

                // Dossier
            } elseif (is_dir($path)) {
                $files = glob($path . '*.php');

                foreach ($files as $file) {
                    require_once $file;
                }
            } else {
                error_log("[NADA ID] Fichier manquant : $path");
            }
        }
    }

    /**
     * Ajoute une action à la liste
     */
    public function add_action(string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1): void
    {
        $this->actions[] = compact('hook', 'component', 'callback', 'priority', 'accepted_args');
    }

    /**
     * Ajoute un filtre à la liste
     */
    public function add_filter(string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1): void
    {
        $this->filters[] = compact('hook', 'component', 'callback', 'priority', 'accepted_args');
    }

    /**
     *  Enregistre toutes les actions et filtres auprés de wordpress.
     */
    public function run(): void
    {
        // Partie Front (toujours chargée)
        $this->plugin_front = new Nada_Front();

        // Partie Admin
        if (is_admin()) {
            $this->plugin_admin = new Nada_Admin();
        }

        // Actions
        foreach ($this->actions as $hook) {
            add_action($hook['hook'], [$hook['component'], $hook['callback']], $hook['priority'], $hook['accepted_args']);
        }

        // Filtres
        foreach ($this->filters as $hook) {
            add_filter($hook['hook'], [$hook['component'], $hook['callback']], $hook['priority'], $hook['accepted_args']);
        }
    }
}
