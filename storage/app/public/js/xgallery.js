(function (window, jQuery) {
    let xgallery = {};

    xgallery.lazyload = {
        lazyloadInstance: false,

        init: function () {
            lazyLoadInstance = new LazyLoad({
                elements_selector: ".lazy"
                // ... more custom settings?
            });

            lazyLoadInstance.update();
        },

        update: function () {
            lazyLoadInstance.update();
        }
    };

    xgallery.toast = {
        show: function (html) {
            let date = new Date();
            let timestamp = date.getTime();
            let id = 'id' + timestamp;
            let $html = jQuery(html).attr('id', id);
            jQuery('.toast-container').append($html);
            let toast = jQuery('#' + id);
            toast.toast({delay: 5000});
            toast.toast('show');
        }
    };

    xgallery.ajax = {
        request: function (data) {
            ajaxUrl = data.ajaxUrl;
            delete data.ajaxUrl;

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: ajaxUrl,
                data: data,
                method: "POST",
                beforeSend: function () {
                    jQuery('#overlay').show();
                }
            })
                .done(function (data) {
                    jQuery(this).attr('disabled', true);
                    xgallery.toast.show(data.html);
                    jQuery('#overlay').hide();
                })
                .fail(function () {
                    jQuery('#overlay').hide();
                })
        },
        init: function () {
            jQuery('body').on('click', '.ajax-pool', function () {
                xgallery.ajax.request(jQuery(this).data());
            });

            jQuery('body').on('submit', '.ajax-form', function (e) {
                e.preventDefault();
                xgallery.ajax.request({ 'url': jQuery(this).find('input[name="url"]').val(), 'ajaxUrl': jQuery(this).attr('action') });
            })
        }
    }

    window.xgallery = xgallery;
})(window, jQuery);
