<?php 
function render_status_html(string $statusKey = 'draft', string $lang = 'fr'): string
{
    $statusMap = [
        'draft' => [
            'color' => 'secondary',
            'icon'  => 'fa-circle',
            'label' => $lang === 'fr' ? 'Brouillon' : 'Draft',
        ],
        'archived' => [
            'color' => 'secondary',
            'icon'  => 'fa-circle',
            'label' => $lang === 'fr' ? 'Archivée' : 'Archived',
        ],
        'pending' => [
            'color' => 'warning',
            'icon'  => 'fa-hourglass-half',
            'label' => $lang === 'fr'
                ? 'Envoyée pour publication'
                : 'Awaiting validation',
        ],
        'rejected' => [
            'color' => 'danger',
            'icon'  => 'fa-times-circle',
            'label' => $lang === 'fr' ? 'Rejetée' : 'Rejected',
        ],
        'changes_requested' => [
            'color' => 'info',
            'icon'  => 'fa-edit',
            'label' => $lang === 'fr' ? 'Modifications requises' : 'Returned',
        ],
        'published' => [
            'color' => 'success',
            'icon'  => 'fa-check-circle',
            'label' => $lang === 'fr' ? 'Publiée' : 'Published',
        ],
        'unpublished' => [
            'color' => 'dark',
            'icon'  => 'fa-eye-slash',
            'label' => $lang === 'fr' ? 'Dépubliée' : 'Unpublished',
        ],
        'imported' => [
            'color' => 'primary',
            'icon'  => 'fa-download',
            'label' => $lang === 'fr' ? 'Importée' : 'Imported',
        ],
    ];

    $status = $statusMap[$statusKey] ?? $statusMap['draft'];

    $color = esc_attr($status['color']);
    $icon  = esc_attr($status['icon']);
    $label = esc_html($status['label']);

    return '
      <div class="contentTD">
        <span class="status-icon">
          <i class="fa ' . $icon . ' text-' . $color . '"></i> ' . $label . '
        </span>
      </div>
    ';
}


function generate_publish_switch_inline(array $study): string
{
    if (empty($study['can_publish'])) {
        return '';
    }

    $checked = (!empty($study['published']) && $study['published'] == '1')
        ? 'checked'
        : '';

    $idno  = esc_attr($study['idno'] ?? '');
    $title = esc_attr($study['title'] ?? '');

    return '
        <div class="form-check formNada form-switch p-0">
            <input class="form-check-input publish-study-switch"
                type="checkbox"
                data-study-idno="' . $idno . '"
                data-study-title="' . $title . '"
                ' . $checked . '>
        </div>
    ';
}

function generate_actions_inline(array $study, $lang = 'fr') {
    $html = '';

    $idno  = esc_attr($study['idno'] ?? '');
    $title = esc_attr($study['title'] ?? '');
    $titleAttr = ($lang === 'fr') ? 'Voir' : 'View';
    $titleAttrEdit = ($lang === 'fr') ? 'Modifier' : 'Edit';
    $titleAttrReject = ($lang === 'fr') ? 'Rejeter' : 'Reject';
    $titleAttrDelete = ($lang === 'fr') ? 'Supprimer' : 'Delete';
    $titleAttrReqMod = ($lang === 'fr') ? 'Modifications requises' : 'Required changes';
    $titleAttrCancel = ($lang === 'fr') ? 'Annuler' : 'Cancel';

    // Voir
    $detailUrl = ($lang === 'fr')
        ? "/catalogue-detail/$idno"
        : "/en/study-details/$idno";

    $html .= '<a href="' . esc_url($detailUrl) . '" 
    class="action-icon edit edit-study-btn-nada"
    data-action="info"
    data-study-idno="' . esc_url($idno) . '"
    data-study-title="' . esc_url($title) . '"
    title="' . esc_attr($titleAttr) . '"><i class="fa fa-eye"></i>
    </a>';




    // Editer
    if (!empty($study['can_edit'])) {
        $editUrl = ($lang === 'fr')
            ? "/alimentation-du-catalogue/$idno"
            : "/en/contribute/$idno";

        $html .= '<a href="' . esc_url($editUrl) . '" 
        class="action-icon action-edit"
        data-action="edit"
        data-study-idno="' . esc_url($idno) . '"
        data-study-title="' . esc_url($title) . '"
        title="' . esc_attr($titleAttrEdit) . '">
                    <i class="fa fa-edit"></i>
                  </a>';
    }

    // Rejeter
    if (!empty($study['can_reject'])) {
        $html .= '<span class="action-icon action-reject reject-study-btn-nada red-study-btn-nada"
                    data-action="reject"
                    data-study-idno="' . $idno . '"
                    data-study-title="' . $title . '"
                    title="' . esc_attr($titleAttrReject) . '">
                    <i class="fas fa-backspace"></i>
                  </span>';
    }

    // Supprimer
    if (!empty($study['can_delete'])) {
        $html .= '<span class="action-icon action-delete delete-study-btn-nada red-study-btn-nada"
                    data-action="supprimer"
                    data-study-idno="' . $idno . '"
                    data-study-title="' . $title . '"
                    title="' . esc_attr($titleAttrDelete) . '">
                    <i class="fa fa-trash"></i>
                  </span>';
    }

    // Demander modification
    if (!empty($study['can_require_modifications'])) {
        $html .= '<span class="action-icon action-modify require-study-modifications-btn red-study-btn-nada"
                    data-action="Modifications requises"
                    data-study-idno="' . $idno . '"
                    data-study-title="' . $title . '"
                    title="' . esc_attr($titleAttrReqMod) . '">
                    <i class="fa fa-repeat"></i>
                  </span>';
    }

    // Annuler
    if (!empty($study['can_cancel'])) {
        $html .= '<span class="action-icon action-cancel cancel-study-btn-nada red-study-btn-nada"
                    data-action="Annuler"
                    data-study-idno="' . $idno . '"
                    data-study-title="' . $title . '"
                    title="' . esc_attr($titleAttrCancel) . '">
                    <i class="fas fa-ban"></i>
                  </span>';
    }

    return $html;
}


?>