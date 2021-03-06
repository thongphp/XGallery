(function (window, jQuery) {
    let xgallery = {};

    xgallery.datepicker = {
        init: function () {
            jQuery(".input-group.input-daterange").datepicker({
                format: "yyyy/mm/dd",
                clearBtn: true,
                todayHighlight: true,
                toggleActive: true
            });
        }
    }

    xgallery.lazyload = {
        lazyloadInstance: null,

        init: function () {
            xgallery.lazyload.lazyloadInstance = new LazyLoad({
                elements_selector: ".lazy",
                unobserve_entered: true
            });

            xgallery.lazyload.lazyloadInstance.update();
        },

        update: function () {
            xgallery.lazyload.lazyloadInstance.update();
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

    xgallery.modal = {
        init: function () {
            jQuery('.btn-modal').on('click', function (e) {
                e.preventDefault();
                const $this = jQuery(this);
                xgallery.modal.show(
                    $this.data('modal-title'),
                    jQuery($this.data('modal-content')).html(),
                    jQuery($this.data('modal-footer')).html() || null
                );
            });
        },
        show: function (title, bodyHtml, footerHtml) {
            const $modal = jQuery("#xgallery-modal");
            $modal.find('.modal-header h4').html(title);
            $modal.find('.modal-body').html(bodyHtml);

            if (footerHtml) {
                $modal.find('.modal-footer').removeClass('hidden').html(footerHtml);
            }

            $modal.modal({show: true});
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
            const $body = jQuery('body');

            $body.on('click', '.ajax-pool', function (e) {
                e.preventDefault();
                const ajaxData = { ...{}, ...jQuery(this).data()};
                xgallery.ajax.request(ajaxData);
            });

            $body.on('submit', '.ajax-form', function (e) {
                e.preventDefault();
                xgallery.ajax.request({
                    'url': jQuery(this).find('input[name="url"]').val(),
                    'ajaxUrl': jQuery(this).attr('action')
                });
            })
        }
    };

    window.xgallery = xgallery;
})(window, jQuery);

jQuery(document).ready(function () {
    xgallery.ajax.init();
    xgallery.datepicker.init();
    xgallery.modal.init();
    xgallery.lazyload.init();
})
