
(function ($) {
    $.fn.extend({
        limiter: function (limit, elem) {
            $(this).on("keyup focus", function () {
                setCount(this, elem);
            });
            function setCount(src, elem) {
                var chars = src.value.length;
                if (chars > limit) {
                    src.value = src.value.substr(0, limit);
                    chars = limit;
                }
                elem.html(limit - chars);
            }
            setCount($(this)[0], elem);
        }
    });

    $(document).ready(function ($) {

        var which_one = 0;
        var conntainer = document.getElementsByClassName('cmbd_metabox_settings_container')[0];
        if (conntainer !== undefined && conntainer !== null) {
            var cmbd_image = document.getElementById('cmbd_image');
            var width = document.getElementsByClassName('cmbd_metabox_settings_container')[0].offsetWidth - (120 + 300 + 40); //120 label, 300 input 20 margin and padding
            if (width > 320) {
                width = 320;
            }

            if (cmbd_image !== undefined && cmbd_image !== null) {
                cmbd_image.style.width = width + "px";
                cmbd_image.style.height = width + "px";
            }
        }

        $('#image').click(function () {
            which_one = 2;
            tb_show('Upload an image', 'media-upload.php?referer=cmbd_add_new_business&amp;type=image&amp;TB_iframe=true&amp;post_id=0', false);
            return false;
        });

        window.send_to_editor = function (selectedImg) {
            var startIndex = selectedImg.indexOf('"') + 1;
            var endIndex = selectedImg.indexOf('"', startIndex);
            var image_url = selectedImg.substring(startIndex, endIndex);
            if (which_one == 1) {
                $('#thumbnail_adr').val(image_url);
            } else {
                $('#image_adr').val(image_url);
            }
            tb_remove();
        };

        $('#date').datepicker({showAnim: 'fadeIn'});
        $('input[type="color"]').wpColorPicker();

//        $('input[type="text"]').each(function (i, obj) {
//            var displayLimit = $(obj).after('<span class="cmbd-display-limit"></span>').next('.cmbd-display-limit');
//            $(obj).limiter(100, displayLimit);
//        });

        // Business gallery file uploads
        var business_gallery_frame;
        var $cmbd_image_gallery_ids = $('#cmbd_business_gallery_id');
        var $business_images = $('div#cmbd_business_images_container ul');
        var cmbd_image = document.getElementById('cmbd_add_business_image');
        if (cmbd_image !== undefined && cmbd_image !== null) { //check if it's edit post page
            $('#cmbd_add_business_image').on('click', function (event) {

                var $el = $(this);
                var attachment_ids = $cmbd_image_gallery_ids.val();

                event.preventDefault();

                // If the media frame already exists, reopen it.
                if (business_gallery_frame) {
                    business_gallery_frame.open();
                    return;
                }

                // Create the media frame.
                business_gallery_frame = wp.media.frames.business_gallery = wp.media({
                    // Set the title of the modal.
                    title: $el.data('choose'),
                    button: {
                        text: $el.data('update'),
                    },
                    states: [
                        new wp.media.controller.Library({
                            title: $el.data('choose'),
                            filterable: 'all',
                        })
                    ]
                });

                // When an image is selected, run a callback.
                business_gallery_frame.on('select', function () {

                    var selection = business_gallery_frame.state().get('selection');

                    selection.map(function (attachment) {

                        attachment = attachment.toJSON();

                        if (typeof attachment.id != "undefined") {
                            attachment_ids = attachment.id;
                            $business_images.find('li').remove();
                            var width = document.getElementsByClassName('cmbd_metabox_settings_container')[0].offsetWidth - (120 + 300 + 40); //120 label, 300 input 20 margin and padding
                            if (width > 320) {
                                width = 320;
                            }
                            $business_images.append('\
                    <li class="image" data-attachment_id="' + attachment.id + '">\
                <img width="' + width + '" height="' + width + '" src="' + attachment.url + '" />\
            <ul class="cmbd_actions">\
        <li><a href="#" class="delete" title="Delete"><strong>[x]</strong></a></li>\
        </ul>\
        </li>');
                        }

                    });

                    $cmbd_image_gallery_ids.val(attachment_ids);
                });

                // Finally, open the modal.
                business_gallery_frame.open();
            });

            // Remove images
            $('#cmbd_business_images_container').on('click', 'a.delete', function () {
                $(this).closest('li.image').remove();

                var attachment_ids = '';

                $('#cmbd_business_images_container ul li.image').css('cursor', 'default').each(function () {
                    var attachment_id = $(this).attr('data-attachment_id');
                    attachment_ids = attachment_ids + attachment_id + ',';
                });

                $cmbd_image_gallery_ids.val(attachment_ids);

                return false;
            });

            $(".cmbd_business_images").sortable({
                start: function (e, o) {
                },
                drag: function (e) {
                },
                stop: function (e, o) {
                    reorder();
                }
            });

            $(".cmbd_business_images").disableSelection();
        }
    });
    $(window).resize(function () {

        var conntainer = document.getElementsByClassName('cmbd_metabox_settings_container')[0];
        if (conntainer !== undefined && conntainer !== null) {
            var cmbd_image = document.getElementById('cmbd_image');
            var width = conntainer.offsetWidth - (120 + 300 + 40); //120 label, 300 input 20 margin and padding
            if (width > 320) {
                width = 320;
            }
            if (cmbd_image !== undefined && cmbd_image !== null) {
                cmbd_image.style.width = width + "px";
                cmbd_image.style.height = width + "px";
            }
        }
    });

    var reorder = function () {
        var attachment_ids = "";
        $(".cmbd_business_images li").each(function () {
            if (typeof $(this).attr('data-attachment_id') != 'undefined') {
                attachment_ids += ((attachment_ids.length > 0) ? ',' : '') + $(this).attr('data-attachment_id');
            }
        });

        $('#cmbd_business_gallery_id').val(attachment_ids);
    };

})(jQuery);