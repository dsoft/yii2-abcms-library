/**
 * ABCMS InlineFormGridView widget.
 *
 */
(function ($) {

    $.fn.abcmsInlineFormGridView = function (options) {

        var settings = $.extend({
            // These are the defaults.
            submitUrl: yii.getCurrentUrl()
        }, options);

        var container = this;
        
        if (settings.submitUrl !== null) {
            // Add events
            this.find('button').on('click', function () {
                submitForm();
            });
            this.find('input[type="text"]').on('keyup', function (e) {
                if (e.keyCode === 13) {
                    submitForm();
                }
        });
        }

        /**
         * Create new form and submit the data using POST
         */
        var submitForm = function () {
            var form = $('<form/>', {
                action: settings.submitUrl,
                method: 'post',
                style: 'display:none'
            });
            container.find('input').each(function () {
                form.append($(this).clone());
            });
            container.find('select').each(function () {
                form.append($('<input/>', {name: $(this).attr('name'), value: $(this).val(), type: 'hidden'}));
            });
            var csrfParam = yii.getCsrfParam();
            if (csrfParam) {
                form.append($('<input/>', {name: csrfParam, value: yii.getCsrfToken(), type: 'hidden'}));
            }
            form.appendTo('body').submit();
        };

    };

}(jQuery));