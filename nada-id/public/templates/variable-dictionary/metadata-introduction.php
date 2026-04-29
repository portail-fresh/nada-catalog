<?php
if (!defined('ABSPATH')) exit;
?>
<article class="technical-documentation-article">
    <header class="technical-documentation-article-header">
        <h1><?php echo $lang === 'fr' ? 'Schéma de métadonnées FReSH' : 'FReSH Metadata Schema'; ?></h1>
    </header>

    <div class="technical-documentation-content">
        <?php if ($lang === 'fr'): ?>
            <h2>Présentation</h2>
            <p>Le schéma de métadonnées FReSH définit la structure et les contraintes des métadonnées utilisées par le catalogue de métadonnées FReSH.</p>

            <p>Le schéma spécifie :</p>
            <ul>
                <li>Les champs de métadonnées et leur sémantique</li>
                <li>Les types de données et les valeurs autorisées</li>
                <li>La cardinalité et les contraintes obligatoires/facultatives</li>
            </ul>

            <h2>Structure du schéma</h2>
            <p>Le schéma de métadonnées FReSH est organisé en une série de sections thématiques.<br>
                Chaque section regroupe les champs de métadonnées qui décrivent un aspect spécifique d'une ressource dans le catalogue FReSH et est documentée dans un fichier dédié.</p>

            <h3>Sections</h3>
            <ol>
                <li>
                    <strong class="titre-no-glossaire">Métadonnées techniques</strong><br>
                    Définit les caractéristiques techniques de l'enregistrement de métadonnées lui-même, y compris les identifiants, les versions du schéma et les propriétés lisibles par machine nécessaires au traitement et à la validation.
                </li>
                <li>
                    <strong>Informations relatives à l'étude</strong><br>
                    Décrit l'étude ou la ressource à un niveau conceptuel, y compris les titres, les résumés, les mots-clés, les classifications thématiques et les informations contextuelles de haut niveau.
                </li>
                <li>
                    <strong>Renseignements administratifs</strong><br>
                    Couvre les aspects liés à la gouvernance et à la responsabilité, tels que les créateurs, les éditeurs, les contacts, les rôles et les métadonnées administratives nécessaires à la gestion du catalogue.
                </li>
                <li>
                    <strong>Méthodologie de l'étude</strong><br>
                    Documente les aspects méthodologiques de l'étude, y compris la conception, les méthodes, les instruments et autres informations nécessaires pour comprendre comment les données ont été produites.
                </li>
                <li>
                    <strong>Collecte et accès aux données</strong><br>
                    Décrit comment les données ont été collectées, stockées et mises à disposition, y compris les conditions d'accès, les formats, les distributions et les informations relatives aux licences.
                </li>
            </ol>

            <h2>Cardinalité et types de données</h2>
            <p>La cardinalité est exprimée à l'aide de conventions standard :</p>
            <ul>
                <li><code>1</code> – exactement une valeur (obligatoire)</li>
                <li><code>0..1</code> – zéro ou une valeur (facultatif)</li>
                <li><code>0..n</code> – zéro ou plusieurs valeurs</li>
                <li><code>1..n</code> – une ou plusieurs valeurs</li>
            </ul>
            <p>Les types de données suivent les définitions couramment adoptées (par exemple <code>string</code>, <code>boolean</code>, <code>date</code>, <code>URI</code>) et peuvent faire référence à des normes externes si nécessaire.</p>


        <?php else: ?>
            <h2>Overview</h2>
            <p>The <strong>FReSH metadata schema</strong> defines the structure and constraints of metadata used by the <strong>FReSH metadata catalogue</strong>.</p>

            <p>The schema specifies:</p>
            <ul>
                <li>Metadata fields and their semantics</li>
                <li>Data types and allowed values</li>
                <li>Cardinality and mandatory/optional constraints</li>
            </ul>

            <h2>Schema Structure</h2>
            <p>The FReSH metadata schema is organized into a series of thematic sections.<br>
                Each section groups metadata fields that describe a specific aspect of a resource in the FReSH catalogue and is documented in a dedicated file.</p>

            <h3>Sections</h3>
            <ol>
                <li>
                    <strong>Technical metadata</strong><br>
                    Defines the technical characteristics of the metadata record itself, including identifiers, schema versions and machine-readable properties necessary for processing and validation.
                </li>
                <li>
                    <strong>Study related information</strong><br>
                    Describes the study or resource at a conceptual level, including titles, abstracts, keywords, thematic classifications and high-level contextual information.
                </li>
                <li>
                    <strong>Administrative information</strong><br>
                    Covers aspects related to governance and accountability, such as creators, publishers, contacts, roles and administrative metadata necessary for catalogue management.
                </li>
                <li>
                    <strong>Study methodology</strong><br>
                    Documents the methodological aspects of the study, including design, methods, instruments and other information necessary to understand how the data was produced.
                </li>
                <li>
                    <strong>Data collection and access</strong><br>
                    Describes how data was collected, stored and made available, including access conditions, formats, distributions and licensing information.
                </li>
            </ol>

            <h2>Cardinality and Data Types</h2>
            <p>Cardinality is expressed using standard conventions:</p>
            <ul>
                <li><code>1</code> – exactly one value (mandatory)</li>
                <li><code>0..1</code> – zero or one value (optional)</li>
                <li><code>0..n</code> – zero or more values</li>
                <li><code>1..n</code> – one or more values</li>
            </ul>
            <p>Data types follow commonly adopted definitions (e.g. <code>string</code>, <code>boolean</code>, <code>date</code>, <code>URI</code>) and may reference external standards if necessary.</p>


        <?php endif; ?>
    </div>
</article>