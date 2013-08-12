jQuery(function ($) {
    $('body').on('click', '.toggle-button', function (evt) {
        evt.preventDefault();

        var target = $($(this).data('toggle-query'));
        var effect = $(this).data('toggle-effect');
        var destroyAfterQuery = $(this).data('destroy-after-query');
        if (typeof effect !== 'string') {
            effect = '';
        }
        if (effect === 'fade') {
            target.fadeToggle();
        }
        else if (effect === 'slide') {
            target.slideToggle();
        }
        else if (!effect) {
            target.toggle();
        }
        if (destroyAfterQuery) {
            $(destroyAfterQuery).remove();
        }
    });

    $('body').on('click', 'button.close[data-close], a.close[data-close]', function (evt) {
        var target = $('#' + $(this).data('close'));
        var effect = $(this).data('close-effect');
        if (typeof effect !== 'string') {
            effect = '';
        }
        if (effect === 'fade') {
            target.fadeOut();
        }
        else if (effect === 'slide') {
            target.slideUp();
        }
        else if (!effect) {
            target.hide();
        }
        evt.preventDefault();
    });
	
	/* step 2 scripts*/
	$('.show-more-var').on('click', function() {
		var $varWrap = $(this).parents('.var-wrap');
		$varWrap.addClass('full-view');
		$(this).parent('.row').hide();
		return false;
	});
	
	/* postpaid_fixed_price scripts */
	
	$('select.select-default-width').selectorize({extraWidth: 0});
}(jQuery));