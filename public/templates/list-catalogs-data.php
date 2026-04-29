<?php
$current_language = pll_current_language();
$per_page = isset($per_page) ? max(1, intval($per_page)) : 10;
$total = isset($total) ? intval($total) : (is_array($data) ? count($data) : 0);
$found = isset($found) ? intval($found) : (is_array($data) ? count($data) : 0);
$initial_page = 1;
?>
<!doctype html>
<html lang="fr">
<head>
<style>
    .pagination_footer { display:flex; justify-content:space-between; align-items:center; margin-top:18px; color:#666; }
    .pagination { list-style:none; margin:0; padding:0; display:flex; gap:6px; align-items:center; }
    .pagination a, .pagination span { display:inline-block; padding:6px 10px; border-radius: unset !important; text-decoration:none; color:#337ab7; border:1px solid transparent; cursor:pointer; }
    .pagination .current { background:#f5f5f5; color:#111; border:1px solid #ddd; pointer-events:none; }
    .pagination a:hover { background:#eef6ff; border:1px solid #cce0ff; }
    .pagination .disabled { color:#aaa; pointer-events:none; }    
</style>
</head>
<body>

<div class="pagination_header" role="navigation" aria-label="Pagination widget">
    <div class="info" id="paginationInfo">
        <?php echo $current_language === 'en' ? 'Showing 0 to 0 of 0 studies' : 'Affichage de 0 à 0 sur 0 études'; ?>
    </div>
</div>
<!-- Container des cards -->
<div id="cardsContainer">
    <!-- Cards sont generer avec JS -->
</div>

<!-- Pagination -->
<div class="pagination_footer" role="navigation" aria-label="Pagination widget">
    <div></div>
    <div class="pager">
        <ul class="pagination" id="paginationControls" role="list"></ul>
    </div>
</div>

<!-- JSON data exported to JS -->
<script>
window.ALL_DATA = <?php
    // ensure $data is an array:
    $export = (is_array($data)) ? $data : [];
    echo json_encode($export, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>;

window.PAGINATION_SETTINGS = {
    perPage: <?php echo json_encode($per_page); ?>,
    total: <?php echo json_encode($total); ?>,
    initialPage: <?php echo json_encode($initial_page); ?>,
    language: <?php echo json_encode($current_language); ?>
};
</script>

<script>
    
(function() {
    const data = Array.isArray(window.ALL_DATA) ? window.ALL_DATA : [];
    const perPage = (window.PAGINATION_SETTINGS && window.PAGINATION_SETTINGS.perPage) || 10;
    const total = (window.PAGINATION_SETTINGS && window.PAGINATION_SETTINGS.total) || data.length;
    const lang = (window.PAGINATION_SETTINGS && window.PAGINATION_SETTINGS.language) || 'fr';
    const container = document.getElementById('cardsContainer');
    const infoEl = document.getElementById('paginationInfo');
    const controlsEl = document.getElementById('paginationControls');

    // Translations
    const translations = {
        fr: {
            start: 'Début',
            previous: 'Précédente',
            next: 'Suivante',
            end: 'Fin',
            noStudies: 'Aucune études.',
            showing: (start, end, total) => `Affichage de ${start} à ${end} sur ${total} études`,
            noData: 'Aucune donnée trouvée.',
            collection: 'Collection',
            id: 'ID',
            lastModified: 'Dernière modification',
            views: 'Vues'
        },
        en: {
            start: 'Start',
            previous: 'Previous',
            next: 'Next',
            end: 'End',
            noStudies: 'No studies.',
            showing: (start, end, total) => `Showing ${start} to ${end} of ${total} studies`,
            noData: 'No data found.',
            collection: 'Collection',
            id: 'ID',
            lastModified: 'Last modified',
            views: 'Views'
        }
    };

    const t = translations[lang] || translations.fr;

    // escape for safe innerHTML
    function escapeHtml(s) {
        if (s === null || s === undefined) return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Render a single card HTML from item
    function renderCard(item) {
        const title = escapeHtml(item.title || '');
        const idno = escapeHtml(item.idno || '');
        const nation = escapeHtml(item.nation || '');
        const ys = escapeHtml(item.year_start || '');
        const ye = escapeHtml(item.year_end || '');
        const authoring = escapeHtml(item.authoring_entity || '');
        const repositoryid = escapeHtml(item.repositoryid || '');
        const changed = formatDate(escapeHtml(item.changed || ''));
        const total_views = escapeHtml(item.total_views || '0');

        // return HTML for the card
        return `
            <a href="/catalogue-detail/${idno}">
                <div class="nada-id-card">
                    <h3 class="nada-id-title">
                        <span><i class="fa fa-database fa-nada-icon wb-title-icon" aria-hidden="true"></i></span>
                        ${title}
                        <span class="title-country">${nation}${(ys || ye) ? ', ' + ys + '-' + ye : ''}</span>
                    </h3>
                    <p class="nada-id-description">${authoring}</p>

                    <div class="nada-list noBorder">
                        <span class="grey-color">${t.collection} : </span>
                        <span>${repositoryid}</span>
                    </div>

                    <div class="d-flex nada-meta" style="margin-top:8px;">
                        <div class="nada-list">
                            <span class="grey-color">${t.id} : </span>
                            <span>${idno}</span>
                        </div>
                        <div class="nada-list">
                            <span class="grey-color">${t.lastModified} : </span>
                            <span>${changed}</span>
                        </div>
                        <div class="nada-list">
                            <span class="grey-color">${t.views} : </span>
                            <span>${total_views}</span>
                        </div>
                    </div>
                </div>
            </a>
        `;
    }

    // Build an array of page numbers + '...' using logic similar to server version
    function buildPageArray(current, totalPages, maxVisible = 5) {
        const pages = [];
        if (totalPages <= maxVisible + 2) {
            for (let i = 1; i <= totalPages; i++) pages.push(i);
            return pages;
        }
        const half = Math.floor(maxVisible / 2);
        let start = Math.max(2, current - half);
        let end = Math.min(totalPages - 1, current + half);

        if (current - 1 < half) {
            end = 1 + maxVisible;
        }
        if (totalPages - current < half) {
            start = totalPages - maxVisible;
        }

        pages.push(1);
        if (start > 2) pages.push('...');
        for (let i = start; i <= end; i++) pages.push(i);
        if (end < totalPages - 1) pages.push('...');
        pages.push(totalPages);
        return pages;
    }

    // render page content (cards) for pageIndex
    function renderPage(pageIndex) {
        const totalItems = total;
        const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
        const page = Math.min(Math.max(1, pageIndex), totalPages);
        const startIndex = (page - 1) * perPage;
        // items to show: slice from data (use actual data length as limit)
        const pageItems = data.slice(startIndex, startIndex + perPage);

        // render cards
        const html = pageItems.map(renderCard).join('');
        container.innerHTML = html || `<div class="nada-id-empty">${t.noData}</div>`;

        // update info text
        const start = totalItems === 0 ? 0 : (startIndex + 1);
        const end = totalItems === 0 ? 0 : (Math.min(startIndex + pageItems.length, totalItems));
        infoEl.textContent = (totalItems === 0) ? t.noStudies : t.showing(start, end, totalItems);

        // render pagination controls
        renderPaginationControls(page, totalPages);
    }

    function renderPaginationControls(currentPage, totalPages) {
        // clear
        controlsEl.innerHTML = '';

        // Début button
        const debutLi = document.createElement('li');
        if (currentPage > 1) {
            const a = document.createElement('a');
            a.textContent = t.start;
            a.href = '#';
            a.addEventListener('click', function(e) { e.preventDefault(); renderPage(1); window.scrollTo({top:0, behavior:'smooth'}); });
            debutLi.appendChild(a);
        } else {
            const span = document.createElement('span');
            span.className = 'disabled';
            span.textContent = t.start;
            debutLi.appendChild(span);
        }
        controlsEl.appendChild(debutLi);

        // Previous
        const prevLi = document.createElement('li');
        if (currentPage > 1) {
            const a = document.createElement('a');
            a.textContent = t.previous;
            a.href = '#';
            a.addEventListener('click', function(e) { e.preventDefault(); renderPage(currentPage - 1); window.scrollTo({top:0, behavior:'smooth'}); });
            prevLi.appendChild(a);
        } else {
            const span = document.createElement('span');
            span.className = 'disabled';
            span.textContent = t.previous;
            prevLi.appendChild(span);
        }
        controlsEl.appendChild(prevLi);

        // Page numbers
        if (totalPages > 1) {
            const pages = buildPageArray(currentPage, totalPages, 5);
            pages.forEach(function(p) {
                const li = document.createElement('li');
                if (p === '...') {
                    const span = document.createElement('span');
                    span.textContent = '…';
                    li.appendChild(span);
                } else if (p === currentPage) {
                    const span = document.createElement('span');
                    span.className = 'current';
                    span.textContent = p;
                    span.setAttribute('aria-current','page');
                    li.appendChild(span);
                } else {
                    const a = document.createElement('a');
                    a.href = '#';
                    a.textContent = p;
                    a.addEventListener('click', function(e) { e.preventDefault(); renderPage(p); window.scrollTo({top:0, behavior:'smooth'}); });
                    li.appendChild(a);
                }
                controlsEl.appendChild(li);
            });
        }

        // Next
        const nextLi = document.createElement('li');
        if (currentPage < totalPages) {
            const a = document.createElement('a');
            a.textContent = t.next;
            a.href = '#';
            a.addEventListener('click', function(e) { e.preventDefault(); renderPage(currentPage + 1); window.scrollTo({top:0, behavior:'smooth'}); });
            nextLi.appendChild(a);
        } else {
            const span = document.createElement('span');
            span.className = 'disabled';
            span.textContent = t.next;
            nextLi.appendChild(span);
        }
        controlsEl.appendChild(nextLi);

        // Fin button
        const finLi = document.createElement('li');
        if (currentPage < totalPages) {
            const a = document.createElement('a');
            a.textContent = t.end;
            a.href = '#';
            a.addEventListener('click', function(e) { e.preventDefault(); renderPage(totalPages); window.scrollTo({top:0, behavior:'smooth'}); });
            finLi.appendChild(a);
        } else {
            const span = document.createElement('span');
            span.className = 'disabled';
            span.textContent = t.end;
            finLi.appendChild(span);
        }
        controlsEl.appendChild(finLi);
    }

    function formatDate(isoString) {
        if (!isoString) return '';
        const d = new Date(isoString);
        if (isNaN(d)) return '';

        // Use language for formatting
        const locale = lang === 'en' ? 'en-US' : 'fr-FR';
        const timeZone = lang === 'en' ? 'UTC' : 'Europe/Paris';

        // date part
        const datePart = new Intl.DateTimeFormat(locale, {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            timeZone: timeZone
        }).format(d);

        // time part
        const timeFmt = new Intl.DateTimeFormat(locale, {
            hour: '2-digit',
            minute: '2-digit',
            hour12: lang === 'en',
            timeZone: timeZone
        });
        
        if (lang === 'en') {
            const timePart = timeFmt.format(d);
            return `${datePart} at ${timePart}`;
        } else {
            const parts = timeFmt.formatToParts(d);
            const hour = (parts.find(p => p.type === 'hour') || {value: ''}).value.padStart(2, '0');
            const minute = (parts.find(p => p.type === 'minute') || {value: ''}).value.padStart(2, '0');
            return `${datePart} à ${hour}h${minute}`;
        }
    }

    // init
    const initial = (window.PAGINATION_SETTINGS && window.PAGINATION_SETTINGS.initialPage) || 1;
    renderPage(initial);

    // Expose functions if you want to use them elsewhere
    window.__clientPagination = {
        renderPage: renderPage,
        perPage: perPage,
        total: total
    };
})();
 
    
</script>
</body>
</html>