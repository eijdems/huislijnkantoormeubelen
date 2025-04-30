require(['jquery'], function ($) {
    $(document).ready(function () {
        // Handle sidebar filter click
        $('#custom-filters').on('click', 'a', function (e) {
            e.preventDefault();

            var $clickedLi = $(this).closest('li');
            var filter = $(this).data('filter');

            // Remove 'active' from all <li> and add to the clicked one
            $('#custom-filters li').removeClass('active');

            $clickedLi.addClass('active');

            $.ajax({
                url: '/customdownload/index/ajax',
                type: 'POST',
                data: { filter: filter },
                success: function (response) {
                    $('#filter-result').html(response);
                },
                error: function () {
                    $('#filter-result').html('<p>Error loading content.</p>');
                }
            });
        });
    });
});
