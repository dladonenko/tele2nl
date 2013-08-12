(function($){
	var defaultOptions = {
		useOverlay: true,
		autoShow: false
	};
	var options;
	var methods = {
		init: function(opt) {
			options = $.extend({}, defaultOptions, opt);

			var self = this;
			var data = $(this).data('popup');
			if (!data) {
				$(this).data('popup', true);
				$(window).on('resize.popup', function(){
					methods.refresh.apply(self, []);
				});
			}
			if (options.autoShow) {
				methods.show.apply(this, []);
			}
			return this;
		},

		show: function() {
			if (options.useOverlay) {
				$('body').find('.popup-overlay').remove();
				$('body').append('<div class="popup-overlay"></div>')

			}
			$('body').append(this);
			this.show();
			methods.refresh.apply(this, []);
			return this;
		},

		hide: function() {
			if (options.useOverlay) {
				$('body').find('.popup-overlay').remove();
			}
			$('body').append(this);
			this.hide();
			return this;
		},

		refresh: function() {
			this.css({
				left: ($(window).width() - this.outerWidth()) / 2,
				top: ($(window).height() - this.outerHeight()) / 2
			});
			return this;
		}
	};
	$.fn.popup = function(method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' +  method + ' is not applicable for jQuery.popup');
		}
	}
})(jQuery);