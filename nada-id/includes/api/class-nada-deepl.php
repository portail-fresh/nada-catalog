<?php

/**
 * Fichier: includes/class-nada-deepl.php
 * Classe pour gérer les traductions avec DeepL API
 */

if (!defined('ABSPATH')) {
    exit; // Sécurité : empêcher l'accès direct
}

class NADA_DeepL
{

    private $apiUrl;
    private $apiKey;

    public function __construct()
    {
        // Définir l'URL de l'API
        $this->apiUrl = get_nada_deepl_url();
        // Récupérer la clé API
        $this->apiKey = get_nada_deepl_api_key();
    }

    /**
     * Vérifie si l'API est configurée
     */
    public function isConfigured()
    {
        return !empty($this->apiKey);
    }

    /**
     * Traduit un tableau de texte
     * @param array $texts Textes à traduire
     * @param string $source_lang Langue source
     * @param string $target_lang Langue cible
     * @return array|false Textes traduits ou false en cas d'erreur
     */
    public function translate($texts, $source_lang, $target_lang): array|bool
    {
        try {
            if (!$this->isConfigured()) {
                error_log('NADA DeepL: API key not configured');
                return false;
            }

            // Filtrer les textes vides
            $texts_to_translate = array_values(array_filter($texts, function ($t) {
                return !empty(trim($t));
            }));

            if (empty($texts_to_translate)) {
                return $texts;
            }

            // Découper chaque texte en lignes individuelles
            $all_lines = [];
            $line_map = []; // index de chaque ligne vers son texte d'origine

            foreach ($texts_to_translate as $index => $text) {
                $lines = preg_split('/\r\n|\r|\n/', $text);
                foreach ($lines as $line) {
                    $all_lines[] = trim($line);
                    $line_map[] = $index;
                }
            }

            $payload = [
                "text" => $all_lines,
                "source_lang" => strtoupper($source_lang),
                "target_lang" => strtoupper($target_lang),
                "formality" => "default",
                "split_sentences" => "0",
                "preserve_formatting" => true,
                "model_type" => "quality_optimized"
            ];

            $args = [
                'method' => 'POST',
                'headers' => [
                    'Authorization' => 'DeepL-Auth-Key ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($payload),
                'timeout' => 30,
            ];

            $response = wp_remote_post($this->apiUrl, $args);

            if (is_wp_error($response)) {
                error_log('DeepL API error: ' . $response->get_error_message());
                return false;
            }

            $status_code = wp_remote_retrieve_response_code($response);
            $body        = wp_remote_retrieve_body($response);

            if ($status_code !== 200) {
                error_log("DeepL returned HTTP $status_code: $body");
                return false;
            }

            $data = json_decode($body, true);

            if (!isset($data['translations'])) {
                error_log("DeepL: invalid response: " . $body);
                return false;
            }

            $translated_lines = array_column($data['translations'], 'text');

            $result = [];
            $current_text_index = -1;
            $current_text_lines = [];
            // Regrouper les lignes traduites par texte d'origine
            foreach ($translated_lines as $i => $translated_line) {
                $text_index = $line_map[$i];

                if ($text_index !== $current_text_index) {
                    if ($current_text_index !== -1) {
                        $result[$current_text_index] = implode("\r\n", $current_text_lines);
                    }
                    $current_text_index = $text_index;
                    $current_text_lines = [];
                }

                $current_text_lines[] = $translated_line;
            }
            if ($current_text_index !== -1) {
                $result[$current_text_index] = implode("\r\n", $current_text_lines);
            }

            return $result;
        } catch (Exception $e) {
            error_log("DeepL exception caught: " . $e->getMessage());
            return false;
        }
    }
}
