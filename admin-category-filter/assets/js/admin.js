(function($){
    'use strict';

	$(document).ready(function(){
		var generateRandomString = function (length) {
			const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
			let result = "";
			for (let i = 0; i < length; i++) {
				result += chars.charAt(Math.floor(Math.random() * chars.length));
			}
			return result;
		},
		
		$categoryTaxonomies = $('.categorydiv > div > ul');
		
		$categoryTaxonomies.each(function(){
			var $container = $(this),
				$inside = $container.closest('.inside'),
				$id = 'apcf-search-field-'+generateRandomString(16);
			
			if ($inside.data('apcf-active')) return;
			
			var label = ($inside.closest('.postbox').find('.postbox-header h2').first().text().toLowerCase() || 'categories'),
				placeholder = ((window.pcfPlugin && pcfPlugin.placeholder)
					? pcfPlugin.placeholder.replace('%s', label || 'categories')
					: 'Filter categories'),
				$input = $('<input/>', {
					type: 'search',
					class: 'apcf-search-field',
					placeholder: placeholder,
					style: 'width:100%; margin-bottom:0; margin-top:15px;',
					id: $id
				});

			$container.before($input);
			$inside.attr('apcf-active', '1');
			
			$container.addClass($id);
		}).promise().done();
		
		$(document).on('keyup search', '.apcf-search-field', function (event) {

            var $this = $(this),
				searchTerm = $this.val(),
                $listItems = $('.' + $this.attr('id') + ' li');

            if ($.trim(searchTerm)) {
                $listItems.hide().filter(function () {
                    return $(this).text().toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1;
                }).show();
            } else {
                $listItems.show();
            }

        });
	});

})(window.jQuery || jQuery);