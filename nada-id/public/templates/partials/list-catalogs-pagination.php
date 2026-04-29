
<?php
// On s'assure que les variables sont définies avec des valeurs par défaut pour éviter les erreurs.
$current_language = function_exists('pll_current_language') ? pll_current_language() : 'fr'; // Utiliser pll_current_language si disponible, sinon 'fr'
$page = isset($page) ? max(1, intval($page)) : 1;
$limit = isset($limit) ? max(1, intval($limit)) : 10;
$found = isset($found) ? intval($found) : 0;

// Calcul des totaux et de la page courante
$totalPages = max(1, ceil($found / $limit));
$currentPage = min(max(1, $page), $totalPages);
$startIndex = ($currentPage - 1) * $limit;
$endIndex = min($startIndex + $limit, $found);

// Traductions (doit correspondre à celles de votre JS)
$t = [
    'fr' => [
        'start' => 'Début',
        'previous' => 'Précédent',
        'next' => 'Suivant',
        'end' => 'Fin',
        'noStudies' => 'Aucune études.',
        'showing' => fn($start, $end, $total) => "Affichage de {$start} à {$end} sur {$total} études"
    ],
    'en' => [
        'start' => 'Start',
        'previous' => 'Previous',
        'next' => 'Next',
        'end' => 'End',
        'noStudies' => 'No studies.',
        'showing' => fn($start, $end, $total) => "Showing {$start} to {$end} of {$total} studies"
    ]
];

$translations = $t[$current_language] ?? $t['fr'];

// --- Fonction utilitaire pour générer le tableau des numéros de page  ---
function buildPageArray($current, $totalPages, $maxVisible = 5)
{
    $pages = [];
    if ($totalPages <= $maxVisible + 2) {
        for ($i = 1; $i <= $totalPages; $i++) $pages[] = $i;
        return $pages;
    }
    $half = floor($maxVisible / 2);
    $start = max(2, $current - $half);
    $end = min($totalPages - 1, $current + $half);

    if ($current - 1 < $half) {
        $end = 1 + $maxVisible;
    }
    if ($totalPages - $current < $half) {
        $start = $totalPages - $maxVisible;
    }

    $pages[] = 1;
    if ($start > 2) $pages[] = '...';
    for ($i = $start; $i <= $end; $i++) $pages[] = $i;
    if ($end < $totalPages - 1) $pages[] = '...';
    $pages[] = $totalPages;
    return array_unique($pages); // Assure l'unicité des numéros de page
}

// Génération du tableau de pages pour l'affichage des contrôles
$pageArray = buildPageArray($currentPage, $totalPages, 5);
?>

<div class="pagination-content">
    <div class="info" id="paginationInfo">
        <?php
        if ($found === 0) {
            echo $translations['noStudies'];
        } else {
            // Le texte d'information
            echo $translations['showing']($startIndex + 1, $endIndex, $found);
        }
        ?>
    </div>

    <div class="pager">
        <ul class="nada-pagination" role="list">

            <?php
            // 1. Bouton "Début"
            $debutClass = ($currentPage <= 1) ? 'disabled' : '';
            $debutTargetPage = 1;
            ?>
            <li class="<?php echo $debutClass; ?>">
                <?php if ($currentPage > 1) : ?>
                    <a href="#" data-page="<?php echo $debutTargetPage; ?>"><?php echo $translations['start']; ?></a>
                <?php else : ?>
                    <span class="disabled"><?php echo $translations['start']; ?></span>
                <?php endif; ?>
            </li>

            <?php
            // 2. Bouton "Précédente"
            $prevClass = ($currentPage <= 1) ? 'disabled' : '';
            $prevTargetPage = max(1, $currentPage - 1);
            ?>
            <li class="<?php echo $prevClass; ?>">
                <?php if ($currentPage > 1) : ?>
                    <a href="#" data-page="<?php echo $prevTargetPage; ?>"><?php echo $translations['previous']; ?></a>
                <?php else : ?>
                    <span class="disabled"><?php echo $translations['previous']; ?></span>
                <?php endif; ?>
            </li>

            <?php
            // 3. Numéros de page
            foreach ($pageArray as $p) :
                if ($p === '...') :
            ?>
                    <li><span>…</span></li>
                <?php
                elseif ($p == $currentPage) :
                ?>
                    <li>
                        <span class="current" aria-current="page"><?php echo $p; ?></span>
                    </li>
                <?php
                else :
                ?>
                    <li>
                        <a href="#" data-page="<?php echo $p; ?>"><?php echo $p; ?></a>
                    </li>
            <?php
                endif;
            endforeach;
            ?>

            <?php
            // 4. Bouton "Suivante"
            $nextClass = ($currentPage >= $totalPages) ? 'disabled' : '';
            $nextTargetPage = min($totalPages, $currentPage + 1);
            ?>
            <li class="<?php echo $nextClass; ?>">
                <?php if ($currentPage < $totalPages) : ?>
                    <a href="#" data-page="<?php echo $nextTargetPage; ?>"><?php echo $translations['next']; ?></a>
                <?php else : ?>
                    <span class="disabled"><?php echo $translations['next']; ?></span>
                <?php endif; ?>
            </li>

            <?php
            // 5. Bouton "Fin"
            $endClass = ($currentPage >= $totalPages) ? 'disabled' : '';
            $endTargetPage = $totalPages;
            ?>
            <li class="<?php echo $endClass; ?>">
                <?php if ($currentPage < $totalPages) : ?>
                    <a href="#" data-page="<?php echo $endTargetPage; ?>"><?php echo $translations['end']; ?></a>
                <?php else : ?>
                    <span class="disabled"><?php echo $translations['end']; ?></span>
                <?php endif; ?>
            </li>

        </ul>
    </div>
</div>