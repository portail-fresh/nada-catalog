jQuery(document).ready(function ($) {
    $(".autocomplete-container").each(function () {
        let $container = $(this);
        let $input = $container.find("input.autocomplete");
        let data = JSON.parse($container.find(".dataList").html());
        let labels = data.map(item => item.label);

        $input.autocomplete({
            minLength: 0,
            source: function (request, response) {
                let results = $.ui.autocomplete.filter(labels, request.term);
                response(results);
            },

            select: function (event, ui) {
                $input.val(ui.item.value);
                return false;
            }
        });

        $input.on("focus", function () {
            $(this).autocomplete("search", $(this).val());
        });

        $input.data("ui-autocomplete")._resizeMenu = function () {
            this.menu.element.outerWidth(this.element.outerWidth());
        };
    });
});