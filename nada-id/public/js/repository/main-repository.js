(function ($) {
  "use strict";

  function loadTable(wrapper, paged) {
    const searchTerm = wrapper.find(".dt-search-input").val();
    const contentDiv = wrapper.find(".nada-table-content");
    const perPage = wrapper.find(".dt-per-page-select").val();

    contentDiv.css("opacity", "0.5");

    $.ajax({
      url: management_repository_vars.ajax_url,
      type: "POST",
      data: {
        action: "nada_fetch_table_ref",
        term: searchTerm,
        paged: paged,
        per_page: perPage,
      },
      success: function (response) {
        contentDiv.html(response);
        contentDiv.css("opacity", "1");
        contentDiv.find(".search-box").remove();
      },
      error: function (xhr, status, error) {
        contentDiv.css("opacity", "1");
      },
    });
  }

  $(document).ready(function () {
    $(document).on("click", ".repository-search-button", function (e) {
      e.preventDefault();
      const wrapper = $(this).closest(".repository-wrapper");
      loadTable(wrapper, 1);
    });

    $(document).on("keypress", ".dt-search-input", function (e) {
      if (e.which === 13 || e.keyCode === 13) {
        e.preventDefault();
        const wrapper = $(this).closest(".repository-wrapper");
        loadTable(wrapper, 1);
      }
    });

    $(document).on("change", ".dt-per-page-select", function (e) {
      const wrapper = $(this).closest(".repository-wrapper");
      loadTable(wrapper, 1);
    });

    $(document).on(
      "click",
      ".repository-wrapper .pagination-links a",
      function (e) {
        e.preventDefault();
        const wrapper = $(this).closest(".repository-wrapper");
        const url = $(this).attr("href");
        let paged = 1;
        const match = url.match(/paged=(\d+)/);

        if (match && match[1]) {
          paged = parseInt(match[1], 10);
        }
        loadTable(wrapper, paged);
      },
    );
  });
})(jQuery);
