(function ($) {
    // Since we generate all the content with JavaScript and set some dimensions dynamically we
    // need to force the elements to have a layout. We do this by showing it and all of its ancestors,
    // do what we need, and then hide them again.
    $.fn.callWithLayout = function (fun) {
        var result;
        var $item = this;
        var props = { position: 'static', visibility: 'hidden', display: 'block' };
        var $hiddenParents = $item.parents().andSelf().not(':visible');

        var oldProps = [];
        $hiddenParents.each(function () {
            var old = {};

            for (var name in props) {
                old[name] = this.style[name];
                this.style[name] = props[name];
            }

            oldProps.push(old);
        });

        result = fun($item);

        $hiddenParents.each(function (i) {
            var old = oldProps[i];
            for (var name in props) {
                this.style[name] = old[name];
            }
        });

        return result;
    }

    var getHiddenDimensions = function (el) {
        var dim = { width: 0, height: 0, innerWidth: 0, innerHeight: 0, outerWidth: 0, outerHeight: 0 };
        dim.width = el.width();
        dim.outerWidth = el.outerWidth(false);
        dim.innerWidth = el.innerWidth();
        dim.height = el.height();
        dim.innerHeight = el.innerHeight();
        dim.outerHeight = el.outerHeight(false);
        return dim;
    };

    $.Selectorize = function (el, options) {
        var base = this;
        base.$el = $(el);
        base.el = el;
        base.$el.data('selectorize', base);
        base.init = function () {
            var dim = base.$el.callWithLayout(getHiddenDimensions);
            base.$el.data('options', $.extend({}, $.Selectorize.defaultOptions, options));

            if (base.$el.attr('multiple') === 'multiple') return base.$el;

            base.$el
                .wrap('<div class="' + base.$el.data('options').containerClass + ' ' + el.className + '" />')
                .before('<div class="' + base.$el.data('options').selectClass + '" />')
                .before('<div class="' + base.$el.data('options').optionsClass + '" />')
                .parent()
                .addClass(base.$el.data('options').additionalContainerClass)
                .width(dim.outerWidth + base.$el.data('options').extraWidth)
                .find('.' + base.$el.data('options').optionsClass)
                .prepend('<ul />');

            var $selected = base.$el.parent().find('.' + base.$el.data('options').selectClass);

            $selected
                .append('<div class="' + base.$el.data('options').selectedClass + '" />')
                .append('<div class="caret"><i></i></div>');


            var $ul = $(el).parent().find('ul');
            $.each(base.$el.find('option'), function (index, item) {
                var $li = $('<li />');
                var $a = $('<a />');
                $li.append($a);
                $ul.append($li);
                $ul.find('a:last')
                    .data('val', $(item).val())
                    .attr('href', '#' + $(item).val())
                    .text($(item).text())
                    .bind('click.selectorize', function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        base.selectAction($(event.currentTarget));
                    });
            });

            base.$el.bind('change.selectorize', function (event) {
                if (base.$el.attr('disabled') !== 'disabled')
                    base.$el.parent().removeClass('disabled');
                else {
                    if (!base.$el.parent().hasClass('disabled')) {
                        base.hideOptions();
                        base.$el.parent().addClass('disabled');
                    }
                }

                var $selectedItem = base.$el.find('option:selected');
                var preSelectedValue = base.$el.parent().closest('.' + base.$el.data('options').containerClass).data('val');
                if (preSelectedValue != $selectedItem.val())
                    base.setSelected($selectedItem.val(), $selectedItem.text());
            });

            $(document)
                .keyup(function (event) {
                    if (event.keyCode == 27) // esc
                        base.hideOptions();
                });

            base.$el
                .parent()
                .find('.' + base.$el.data('options').selectClass)
                .bind('click.selectorize', function (event) {
                    event.stopPropagation();
                    if (base.$el.parent().find('.' + base.$el.data('options').optionsClass).css('display') === 'none')
                        base.showOptions();
                    else
                        base.hideOptions();
                });


            $('html').bind('click.selectorize', function (event) { base.hideOptions(); });


            var $options = base.$el.parent().find('.' + base.$el.data('options').optionsClass);

            if (base.$el.data('options').putOver === false) {
                $options.css('top', base.$el.parent().find('.' + base.$el.data('options').selectClass).callWithLayout(getHiddenDimensions).outerHeight - 1);
            }


            if (base.$el.data('options').useScroll) {
                var $firstLi = base.$el.parent().find('li:first');
                var itemHeight = $firstLi.callWithLayout(getHiddenDimensions).outerHeight;
                base.$el.data('options').maxItems = (base.$el.data('options').maxItems > base.$el.parent().find('li').length) ? base.$el.parent().find('li').length : base.$el.data('options').maxItems;
                var newHeight = itemHeight * base.$el.data('options').maxItems;

                // If the select box is hidden on page load the scrollbar won't show because the plugin thinks it's not needed.
                // Therefore we need to show the element first which we do with the help function callWithLayout.
                var initScrollBar = function (el) {
                    el.mCustomScrollbar({
                        set_width: false, /*optional element width: boolean, pixels, percentage*/
                        set_height: newHeight + 'px', /*optional element height: boolean, pixels, percentage*/
                        horizontalScroll: false, /*scroll horizontally: boolean*/
                        scrollInertia: 150, /*scrolling inertia: integer (milliseconds)*/
                        scrollEasing: "easeOutCirc", /*scrolling easing: string*/
                        mouseWheel: "auto", /*mousewheel support and velocity: boolean, "auto", integer*/
                        autoDraggerLength: true, /*auto-adjust scrollbar dragger length: boolean*/
                        scrollButtons: {
                            /*scroll buttons*/
                            enable: false, /*scroll buttons support: boolean*/
                            scrollType: "continuous", /*scroll buttons scrolling type: "continuous", "pixels"*/
                            scrollSpeed: 20, /*scroll buttons continuous scrolling speed: integer*/
                            scrollAmount: 40 /*scroll buttons pixels scroll amount: integer (pixels)*/
                        },
                        advanced: {
                            updateOnBrowserResize: false, /*update scrollbars on browser resize (for layouts based on percentages): boolean*/
                            updateOnContentResize: false, /*auto-update scrollbars on content resize (for dynamic content): boolean*/
                            autoExpandHorizontalScroll: false /*auto expand width for horizontal scrolling: boolean*/
                        },
                        callbacks: {
                            onScroll: function () {
                            }, /*user custom callback function on scroll event*/
                            onTotalScroll: function () {
                            }, /*user custom callback function on bottom reached event*/
                            onTotalScrollOffset: 0 /*bottom reached offset: integer (pixels)*/
                        }
                    });
                };
                base.$el.parent().find('.' + base.$el.data('options').optionsClass).callWithLayout(initScrollBar);
            }


            $options.hide();


            var $selectedItem = base.$el.find('option:selected');
            base.setSelected($selectedItem.val(), $selectedItem.text());

            if (base.$el.attr('disabled') == 'disabled')
                base.$el.parent().addClass('disabled');


            return base.$el;
        };

        base.isDisabled = function () { return base.$el.parent().hasClass('disabled'); };

        base.showOptions = function () {
            if (base.isDisabled()) {
                return false;
            }

            $('.' + base.$el.data('options').containerClass + ' .' + base.$el.data('options').optionsClass).css('display', 'none');
            base.$el.parent().find('.' + base.$el.data('options').optionsClass).fadeIn('fast');
            return false;
        };

        base.hideOptions = function () {
            if (base.isDisabled()) {
                return false;
            }
            if (!base.$el.data('options')) {
                // This is probably not the best way to solve a problem when options are not set but still used below.
                // There's probably some deeper problem when the options don't exist.
                // One place the problem occurs is when selecting section and category on MyC CuSe message form.
                return false;
            }
            base.$el.parent().find('.' + base.$el.data('options').optionsClass).fadeOut('fast');
            return false;
        };

        base.setSelected = function (value, text) {
            if (base.isDisabled()) { return false; }
            base.$el.parent()
				.data('val', value)
				.find('.' + base.$el.data('options').selectedClass)
				.text(text);
            base.$el.parent().find('.' + base.$el.data('options').optionsClass + ' li').removeClass(base.$el.data('options').activeClass);
            var $matches = base.$el.parent().find('.' + base.$el.data('options').optionsClass + ' li:contains("' + text + '")');
            if ($matches.length == 1)
                $matches.first().addClass(base.$el.data('options').activeClass);
            else {
                $.each($matches, function (index, item) {
                    if ($(item).find('a:first').data('val') === value) {
                        $(item).addClass(base.$el.data('options').activeClass);
                        return false;
                    }
                });
            }
            return false;
        };

        base.selectAction = function ($obj) {
            if (base.isDisabled()) { return false; }
            base.hideOptions();
            var $selectedElement = base.$el.find('[value="' + $obj.data('val') + '"]');
            if ($selectedElement.length == 0)
                $selectedElement = base.$el.find('option:contains("' + $obj.data('val') + '")');
            base.setSelected($obj.data('val'), $obj.text());

            base.$el.find('option').removeAttr('selected');

            $selectedElement
				.attr('selected', 'selected')
				.trigger('change');

            return false;
        };

        base.init();
    };

    $.Selectorize.defaultOptions = {
        containerClass: 'selectorized',
        selectClass: 'select',
        optionsClass: 'options',
        optionClass: 'option',
        selectedClass: 'selected',
        activeClass: 'active',
        maxItems: 6,
        extraWidth: 0,
        useScroll: true,
        additionalContainerClass: 'normal',
        putOver: false
    };

    $.Selectorize.destroy = function ($el) {
        if ($el.css('display') === 'none') {
            $el
                .unbind('.selectorize')
                .parent()
                .find('*').not('select, select option')
                .unbind('.selectorize')
                .remove();
            $el
                .unwrap();
        }
    };

    $.Selectorize.refresh = function ($el) {
        $.Selectorize.destroy($el);
        $el.selectorize();
    };

    $.fn.selectorize = function () {
        var args = arguments;
        return this.each(function (index, item) {
            if (typeof args[0] === 'string') {
                switch (args[0]) {
                    case 'destroy':
                        $.Selectorize.destroy($(item));
                        break;
                    case 'refresh':
                        $.Selectorize.refresh($(item));
                        break;
                    default:
                        
                        break;
                }
                return $(this);
            }
            else if ((!$(item).hasClass('selectorized')) && ($(item).closest('.selectorized').length === 0)) {
                (new $.Selectorize(this, args[0]));
            }
        });
    };

})(jQuery);
