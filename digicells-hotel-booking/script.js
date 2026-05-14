// Search functionality (with type)
function performSearch(paged) {
    var title = $('#dghb_search_title').val();
    var location = $('#dghb_search_location').val();
    var type = $('#dghb_search_type').val();
    $.post(dghb_ajax.ajax_url, {
        action: 'dghb_search_hotels',
        title: title,
        location: location,
        type: type,
        paged: paged
    }, function(html) {
        $('#dghb_search_results').html(html);
        attachPaginationEvents();
    });
}