jQuery(function($) {
    // Gallery uploader
    var frame;
    $('.dghb-upload-gallery').on('click', function(e) {
        e.preventDefault();
        if (frame) { frame.open(); return; }
        frame = wp.media({ title: 'Select Gallery Images', button: { text: 'Add to Gallery' }, multiple: true });
        frame.on('select', function() {
            var selection = frame.state().get('selection');
            var ids = [];
            var current = $('#dghb_gallery').val() ? $('#dghb_gallery').val().split(',') : [];
            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                ids.push(attachment.id);
                $('.dghb-gallery-preview').append('<div class="dghb-gallery-item" data-id="'+attachment.id+'">'+attachment.sizes.thumbnail.url+'<button type="button" class="button dghb-remove-gallery">Remove</button></div>');
            });
            var allIds = current.concat(ids);
            $('#dghb_gallery').val(allIds.join(','));
        });
        frame.open();
    });
    $(document).on('click', '.dghb-remove-gallery', function() {
        var item = $(this).closest('.dghb-gallery-item');
        var id = item.data('id');
        var current = $('#dghb_gallery').val().split(',');
        var newIds = current.filter(function(i) { return i != id; });
        $('#dghb_gallery').val(newIds.join(','));
        item.remove();
    });
});