<?php
class Nada_Study_Repository
{
    private string $table;
    public function __construct()
    {
        global $wpdb;
        $this->table = $wpdb->prefix . 'nada_list_studies';
    }

    /**
     * Vérifie si l'email correspond à un PI existant dans nada_list_studies
     * et met à jour pi_id si nécessaire.
     */
    public function assign_nada_studies_to_pi(int $user_id, string $email): void
    {
        global $wpdb;
        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT id FROM {$this->table} WHERE pi_email = %s AND (pi_id IS NULL OR pi_id = 0)", $email)
        );

        if (!empty($rows)) {
            foreach ($rows as $row) {
                $wpdb->update(
                    $this->table,
                    ['pi_id' => $user_id],
                    ['id' => $row->id],
                    ['%d'],
                    ['%d']
                );
            }
        }
    }

    /**
     * Supprime une étude depuis la table nada_list_studies par idno
     */
    public function delete_nada_study_from_wp(string $idno): bool
    {
        global $wpdb;

        // Supprime la ligne
        $deleted = $wpdb->delete(
            $this->table,
            ['nada_study_idno' => $idno],
            ['%s'] // format string
        );

        if ($deleted === false) {
            return false; // Erreur SQL
        }

        return true;
    }

    /**
     * Met à jour le statut d'une étude
     */
    public function update_nada_study_wp(string $idno, array $data): bool
    {
        global $wpdb;

        // Condition WHERE
        $where = ['nada_study_idno' => $idno];

        // Générer dynamiquement les formats
        $formats = [];
        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $formats[] = '%d';
            } elseif (is_float($value)) {
                $formats[] = '%f';
            } else {
                $formats[] = '%s';
            }
        }


        // Exécuter la mise à jour
        $updated = $wpdb->update(
            $this->table,
            $data,
            $where,
            $formats, // formats : int, datetime
            ['%s']        // condition string
        );

        if ($updated === false) {
            return false; // Erreur SQL
        }

        return $updated > 0; // true si au moins 1 ligne modifiée
    }

    /**
     * Récuperer une étude depuis la table nada_list_studies par idno +sttaus optionnel
     */
    public function get_details_study_from_wp(string $base_idno, ?string $status = null)
    {
        global $wpdb;

        try {

            // Base query
            $sql = "SELECT * FROM {$this->table} WHERE nada_study_idno LIKE %s";
            $params = [$base_idno . '%'];

            // Ajouter le filtre status si renseigné
            if (!empty($status)) {
                $sql .= " AND status = %s";
                $params[] = $status;
            }

            // Prépare & exécute
            $study_wp = $wpdb->get_row(
                $wpdb->prepare($sql, ...$params),
                ARRAY_A
            );

            return $study_wp ?: null;
        } catch (Exception $e) {
            error_log('Erreur dans get_details_study_from_wp: ' . $e->getMessage());
            return null;
        }
    }

    /* Générer un nouvel IDNO incrémenté */
    public function generate_next_idno(string $base_idno): string
    {
        global $wpdb;

        // Récupère toutes les variantes existantes : FReSH-19551, FReSH-19551-1, FReSH-19551-2...
        $rows = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT nada_study_idno
             FROM {$this->table}
             WHERE nada_study_idno LIKE %s",
                $base_idno . '%'
            )
        );

        if (empty($rows)) {
            // aucune version existante — on crée la version "-1"
            return $base_idno . '-1';
        }

        $max = 0;

        foreach ($rows as $idno) {

            // Si c’est exactement le base_idno → considérer comme version 0
            if ($idno === $base_idno) {
                continue;
            }

            // Cherche un suffixe numérique : "-1", "-2", "-3"
            if (preg_match('/^' . preg_quote($base_idno, '/') . '\-(\d+)$/', $idno, $m)) {
                $num = intval($m[1]);
                if ($num > $max) {
                    $max = $num;
                }
            }
        }

        // Incrémente
        $next = $max + 1;

        return $base_idno . '-' . $next;
    }

}
