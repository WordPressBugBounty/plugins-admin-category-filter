(function ($) {
    'use strict';

    $(function () {

        var getPlaceholder = function (label) {
            label = (label || 'categories').toString();

            // Prefer new localized object, fallback to old one for safety.
            if (window.pcfPlugin && pcfPlugin.placeholder) {
                return pcfPlugin.placeholder.replace('%s', label);
            }

            if (window.fc_plugin && fc_plugin.placeholder) {
                return fc_plugin.placeholder;
            }

            return 'Filter categories';
        };

        $('.categorydiv').each(function () {
            var $categoryDiv = $(this);

            // Prevent duplicates.
            if ($categoryDiv.data('apcfActive')) {
                return;
            }

            var label = (
                $categoryDiv.closest('.postbox').find('.postbox-header h2').first().text() ||
                $categoryDiv.closest('.postbox').find('h2').first().text() ||
                'categories'
            ).toString().trim().toLowerCase();

            var $input = $('<input/>', {
                type: 'search',
                class: 'apcf-search-field',
                placeholder: getPlaceholder(label),
                style: 'width:100%; margin-bottom:0; margin-top:15px;'
            });

            // Insert at top of the taxonomy box (same behavior as 1.2.2, most robust).
            $categoryDiv.prepend($input);

            $categoryDiv.data('apcfActive', true);
        });

        $(document).on('keyup search', '.apcf-search-field', function (event) {
            var searchTerm = (event.target.value || '').toString();
            var $categoryDiv = $(this).closest('.categorydiv');

            // Prefer standard checklist selector, fallback to any list items.
            var $listItems = $categoryDiv.find('.categorychecklist li');
            if (!$listItems.length) {
                $listItems = $categoryDiv.find('li');
            }

            if ($.trim(searchTerm)) {
                var needle = searchTerm.toLowerCase();
                $listItems.hide().filter(function () {
                    return $(this).text().toLowerCase().indexOf(needle) !== -1;
                }).show();
            } else {
                $listItems.show();
            }
        });

    });

})(window.jQuery || jQuery);