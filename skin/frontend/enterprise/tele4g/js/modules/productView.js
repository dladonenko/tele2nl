var tele4g = tele4g || {};

(function($){
	/* ---------------------------------------------------------------
	Stock level
	----------------------------------------------------------------*/
	var stockLevelDefaultOptions = {
		msgDays: 'days',
		msgWeeks: 'weeks',
		msgNotAvailable: 'N/A',
		weeksOffset: 1,
		daysOffset: 2,

        queryText: '.stock-expected',
        queryHiddenInput: ':input[name=expectedDeliveryTime]',

		expectedDays: 0,
		expectedWeeks: 0
	};
	var StockLevel = function(){
		var self = this;
		var options = {};
		var elements = {
			container: undefined,
			expectedTime: undefined,
			hiddenInput: undefined
		};

		// Private refresh method
		var refresh = function() {
			var text = '';

			if (options.expectedDays === '') options.expectedDays = 0;
			if (options.expectedWeeks === '') options.expectedWeeks = 0;

			if (!(
				$.isNumeric(options.expectedDays) &&
				$.isNumeric(options.expectedWeeks) &&
				options.expectedDays >= 0 &&
				options.expectedWeeks >= 0
			)) {
				text = options.msgNotAvailable;
				setText(text);
				return;
			}

			if (options.expectedDays === 0) {
				text = options.msgNotAvailable;
			} else {
				if (options.expectedWeeks > 0) {
					text = options.expectedWeeks + '-' + (parseInt(options.expectedWeeks) + options.weeksOffset) + ' ' + options.msgWeeks;
				} else {
					text = options.expectedDays + '-' + (parseInt(options.expectedDays) + options.daysOffset) + ' ' + options.msgDays;
				}
			}
			setText(text);
		}

		// Private method that sets text to the interface and into the hidden input
		var setText = function(text) {
			elements.expectedTime.text(text);
			elements.hiddenInput.val(text);
		}

		// Init
		this.init = function(element, opt, reset) {
			if ((typeof reset === 'boolean' && reset) || (typeof reset === 'undefined' && typeof opt === 'boolean' && opt)) {
				options = $.extend({}, stockLevelDefaultOptions, opt);
			} else {
				options = $.extend({}, stockLevelDefaultOptions, options, opt);
			}
			elements.container = $(element);
			if (elements.container.length == 0) {
				return;
			}
			elements.expectedTime = elements.container.find(options.queryText);
			elements.hiddenInput = elements.container.find(options.queryHiddenInput);

			// Subscribe for variantChanged event
			tele4g.eventManager.unbindAllNs('stockLevel');
			tele4g.eventManager.bind('variantChanged', function(event) {
				self.updateExpectedDeliveryTime(event.data.expectedDays, event.data.expectedWeeks);
			}, 'stockLevel');

			refresh();
		}

		this.setOptions = function(opt) {
			options = $.extend(stockLevelDefaultOptions, options, opt);
		}

		// Public method for update
		this.updateExpectedDeliveryTime = function(expDays, expWeeks) {
			options.expectedDays = expDays;
			options.expectedWeeks = expWeeks;
			refresh();
		}
	}
	tele4g.stockLevel = new StockLevel();


	/* ---------------------------------------------------------------
	Variant selector
	----------------------------------------------------------------*/
	var varianSelectorDefaultOptions = {
		queryListItem: '.color',
		queryAnchor: '.color > a',
		queryHiddenSelectOptions: 'select.super-attribute-select option'
	};

	var VariantSelector = function(){
		var self = this;
		var options = {};
		var elements = {
			container: undefined
		};
		var variants = [];

		// Variant onClick handler
		var variantClicked = function(event) {
			var clickedListItem = $(this).closest(options.queryListItem);
			var currentVariantData;
			for (var i = 0; i < variants.length; i++) {
				if (clickedListItem.data('color-id') == variants[i].colorId) {
					currentVariantData = variants[i];
					break;
				}
			}

			// Some magento magic
			try {
		        options.optionsPrice.changePrice('config', {'price': currentVariantData.price, 'oldPrice': currentVariantData.oldPrice});
	    	    options.optionsPrice.reload();
    		}
    		catch (err) {};

	        // set selected color in hidden <select>
	        currentVariantData.elementHiddenOption
		        .attr('selected', 'selected')
		        .siblings().removeAttr('selected');

	        // switch colorpicker
	        currentVariantData.elementListItem
	        	.addClass('active')
	        	.siblings().removeClass('active');

	        tele4g.eventManager.trigger('variantChanged', {
	            expectedDays: currentVariantData.expectedDays,
	            expectedWeeks: currentVariantData.expectedWeeks
	        });

        	// TODO: move it to gallery module. Use events instead
								        // show images only for selected variant
								        var prodId = clickedListItem.data('prod-id');
								        jQuery('.variant-'+prodId).siblings().hide();
								        jQuery('.variant-'+prodId).show();

								        // switch main image
								        jQuery('#main-image img').attr('src', clickedListItem.data('main-image-src'));

								        // switch gallery to the first visible
								        jQuery('.more-views li:visible:first > a').trigger('click');

								        // initiate binding period change to show correct price
								        jQuery('#radioBindingPeriodPost :checked').trigger('click');

		}

		this.init = function(element, opt) {
			options = $.extend(varianSelectorDefaultOptions, options, opt);
			elements.container = $(element);
			if (elements.container.length == 0) {
				return;
			}

			var listItems = elements.container.find(options.queryListItem);
			elements.container.find(options.queryHiddenSelectOptions).each(function(){
				if ($(this).val() !== '' && this.config !== undefined) {
					variants.push({
						colorId: this.config.id,
						colorName: this.config.label.toLowerCase(),
						productId: listItems.filter('[data-color-id=' + this.config.id + ']').data('prod-id'),
						price: this.config.price,
						oldPrice: this.config.oldPrice,
						expectedDays: listItems.filter('[data-color-id=' + this.config.id + ']').data('expected-days'),
						expectedWeeks: listItems.filter('[data-color-id=' + this.config.id + ']').data('expected-weeks'),

						elementHiddenOption: $(this),
						elementListItem: listItems.filter('[data-color-id=' + this.config.id + ']')
					});
				}
			});
			elements.container.find(options.queryAnchor).unbind('click.variantSelector');
			elements.container.find(options.queryAnchor).on('click.variantSelector', variantClicked);

			this.setDefaultVariant();
		}

		this.setOptions = function(opt) {
			options = $.extend(varianSelectorDefaultOptions, options, opt);
		}

		this.setDefaultVariant = function(){
			var defaultVariant;

			defaultVariant = elements.container.find(options.queryListItem).filter('[data-master-variant]');

		    var hashUrl = window.location.hash.replace("#","");
		    if (hashUrl) {
		    	for (var i = 0; i < variants.length; i++) {
		    		if (variants[i].colorName == hashUrl) {
		    			defaultVariant = variants[i].elementListItem;
		    		}
		    	}
		    }

		    if (defaultVariant && defaultVariant.length == 1) {
		    	defaultVariant.find('a').trigger('click');
		    } else {
                var firstVariant = elements.container.find(options.queryListItem).eq(0);
                if (firstVariant && firstVariant.length == 1) {
                    firstVariant.find('a').trigger('click');
                }
            }
		}
	}
	tele4g.variantSelector = new VariantSelector();


	/* ---------------------------------------------------------------
	Subscription type selector
	----------------------------------------------------------------*/
	var SubscriptionTypeSelector = function(){
		this.init = function() {
			// Trigger subscriptionTypeChanged event on tab change
			$('a[data-toggle="tab"][href="#prepaid"]').on('shown', function(){
				tele4g.eventManager.trigger('subscriptionTypeChanged', 'prepaid');
                $('.wrap-citiesToGo').hide();
			});
			$('a[data-toggle="tab"][href="#postpaid"]').on('shown', function(){
				tele4g.eventManager.trigger('subscriptionTypeChanged', 'postpaid');
                $('.wrap-citiesToGo').show();
			});

			// Initially trigger event for preselected subscription type
			$('.subscription-selector > ul > li.active > a').trigger('shown');
		}
	}
	tele4g.subscriptionTypeSelector = new SubscriptionTypeSelector();

	// Init modules.
	// Order is essential.
	$(function() {
        tele4g.stockLevel.init('.stock-status');
		tele4g.variantSelector.init('#product_addtocart_form .color-chooser');
		tele4g.subscriptionTypeSelector.init();
	});
})(jQuery);






/*
var subscriptions = {
	pre: '',
	post: {
		'22': {
			'0': {
				'price': 0,
				'valueId': 2410,
				'ltc': 985,
				'monthlyPrice': 245
			}
		}
	}
}


var subscriptions = {
	pre: '',
	post: [
		{
			id: 22,
			bindingData: [
				{
					bindingTime: 0,
					price: 0,
					valueId: 2410,
					ltc: 985,
					monthlyPrice: 245
				}
			]
		}
	]
}
*/