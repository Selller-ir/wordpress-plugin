jQuery(document).ready(function ($) {

    var frame;

    $('#pfs_upload_image').on('click', function (e) {
        e.preventDefault();

        // اگر قبلا frame ساخته شده، دوباره بازش کن
        if (frame) {
            frame.open();
            return;
        }

        // ساخت frame
        frame = wp.media({
            title: 'Select Product Image',
            button: { text: 'Use this image' },
            multiple: false
        });

        // وقتی تصویر انتخاب شد
        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            console.log(attachment);
            // مقدار hidden input را بروز کن
            $('#pfs_image_id').val(attachment.id);

            // نمایش پیش‌نمایش تصویر
            $('#pfs_image_preview').html(
                '<img src="' + attachment.url + '" style="max-width:150px;"/>'
            );
        });

        frame.open();
    });
});
