var tele4G = tele4G || {};

(function($){
	DeviceList = function() {
		var element;
		var settings = {
			showMoreTriggerQuery: '.products-column-more',
			columnQuery: '.products-column',
			hiddenItemQuery: '.product-item-hidden'
		};

		function equalizeHeight(elements) {
		    var maxHeight = 0;
		    $(elements).each(function(){
		        var height = $(this).height();
		        if (height > maxHeight) maxHeight = height;
		    });
		    $(elements).height(maxHeight);
		};

		// equalize height of columns
		this.init = function(elementQuery){
			element = $(elementQuery);

			// Equalize height of columns initially
			var columns = element.find(settings.columnQuery);
			equalizeHeight(columns);

			// Equalize height of columns on "Show more" click
			element.on('click', settings.showMoreTriggerQuery, function(event){
				event.preventDefault();

				var trigger = $(this);
				var columns = element.find(settings.columnQuery);
				var currentColumn = trigger.closest(settings.columnQuery);
				var currentColumnHiddenItems = currentColumn.find(settings.hiddenItemQuery);
				currentColumn.css('height', 'auto');
				currentColumnHiddenItems.show();
				equalizeHeight(columns);
				trigger.remove();
			});
		};
	};
    tele4G.deviceList = new DeviceList();
})(jQuery);
