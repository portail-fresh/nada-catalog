<?php

/**
 * Classe Nada_Loader
 *
 * Cette classe centralise et enregistre les hooks (actions et filtres) du plugin.
 * Elle sert d'intermédiaire entre les classes du plugin et le cœur de WordPress.
 */
class Nada_Loader
{

    /** @var array Liste des actions à enregistrer */
    protected array $actions = [];

    /** @var array Liste des filtres à enregistrer */
    protected array $filters = [];

    /** @var Nada_Admin|null Instance de la partie admin */
    private ?Nada_Admin $plugin_admin = null;

    /** @var Nada_Front|null Instance de la partie front */
    private ?Nada_Front $plugin_front = null;

    /**
     * Constructeur
     * Charge les dépendances nécessaires au plugin.
     */
    public function __construct()
    {
        $required_files = [
            'includes/class-nada-admin.php',
            'includes/class-nada-front.php',
            'includes/functions-nada.php', // fonctions globales
            'includes/shortcodes.php'
        ];

        foreach ($required_files as $file) {
            $path = NADA_ID_PLUGIN_DIR . $file;
            if (file_exists($path)) {
                require_once $path;
            } else {
                error_log("[NADA Plugin] Fichier manquant : $path");
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
