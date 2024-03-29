"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/**
* jquery.matchHeight.js v0.5.2
* http://brm.io/jquery-match-height/
* License: MIT
*/
;

(function ($) {
  /*
  *  internal
  */
  var _previousResizeWidth = -1,
      _updateTimeout = -1;
  /*
  *  _rows
  *  utility function returns array of jQuery selections representing each row
  *  (as displayed after float wrapping applied by browser)
  */


  var _rows = function _rows(elements) {
    var tolerance = 1,
        $elements = $(elements),
        lastTop = null,
        rows = []; // group elements by their top position

    $elements.each(function () {
      var $that = $(this),
          top = $that.offset().top - _parse($that.css('margin-top')),
          lastRow = rows.length > 0 ? rows[rows.length - 1] : null;

      if (lastRow === null) {
        // first item on the row, so just push it
        rows.push($that);
      } else {
        // if the row top is the same, add to the row group
        if (Math.floor(Math.abs(lastTop - top)) <= tolerance) {
          rows[rows.length - 1] = lastRow.add($that);
        } else {
          // otherwise start a new row group
          rows.push($that);
        }
      } // keep track of the last row top


      lastTop = top;
    });
    return rows;
  };
  /*
  *  _parse
  *  value parse utility function
  */


  var _parse = function _parse(value) {
    // parse value and convert NaN to 0
    return parseFloat(value) || 0;
  };
  /*
  *  _parseOptions
  *  handle plugin options
  */


  var _parseOptions = function _parseOptions(options) {
    var opts = {
      byRow: true,
      remove: false,
      property: 'height'
    };

    if (_typeof(options) === 'object') {
      return $.extend(opts, options);
    }

    if (typeof options === 'boolean') {
      opts.byRow = options;
    } else if (options === 'remove') {
      opts.remove = true;
    }

    return opts;
  };
  /*
  *  matchHeight
  *  plugin definition
  */


  var matchHeight = $.fn.matchHeight = function (options) {
    var opts = _parseOptions(options); // handle remove


    if (opts.remove) {
      var that = this; // remove fixed height from all selected elements

      this.css(opts.property, ''); // remove selected elements from all groups

      $.each(matchHeight._groups, function (key, group) {
        group.elements = group.elements.not(that);
      }); // TODO: cleanup empty groups

      return this;
    }

    if (this.length <= 1) return this; // keep track of this group so we can re-apply later on load and resize events

    matchHeight._groups.push({
      elements: this,
      options: opts
    }); // match each element's height to the tallest element in the selection


    matchHeight._apply(this, opts);

    return this;
  };
  /*
  *  plugin global options
  */


  matchHeight._groups = [];
  matchHeight._throttle = 80;
  matchHeight._maintainScroll = false;
  matchHeight._beforeUpdate = null;
  matchHeight._afterUpdate = null;
  /*
  *  matchHeight._apply
  *  apply matchHeight to given elements
  */

  matchHeight._apply = function (elements, options) {
    var opts = _parseOptions(options),
        $elements = $(elements),
        rows = [$elements]; // take note of scroll position


    var scrollTop = $(window).scrollTop(),
        htmlHeight = $('html').outerHeight(true); // get hidden parents

    var $hiddenParents = $elements.parents().filter(':hidden'); // cache the original inline style

    $hiddenParents.each(function () {
      var $that = $(this);
      $that.data('style-cache', $that.attr('style'));
    }); // temporarily must force hidden parents visible

    $hiddenParents.css('display', 'block'); // get rows if using byRow, otherwise assume one row

    if (opts.byRow) {
      // must first force an arbitrary equal height so floating elements break evenly
      $elements.each(function () {
        var $that = $(this),
            display = $that.css('display') === 'inline-block' ? 'inline-block' : 'block'; // cache the original inline style

        $that.data('style-cache', $that.attr('style'));
        $that.css({
          'display': display,
          'padding-top': '0',
          'padding-bottom': '0',
          'margin-top': '0',
          'margin-bottom': '0',
          'border-top-width': '0',
          'border-bottom-width': '0',
          'height': '100px'
        });
      }); // get the array of rows (based on element top position)

      rows = _rows($elements); // revert original inline styles

      $elements.each(function () {
        var $that = $(this);
        $that.attr('style', $that.data('style-cache') || '');
      });
    }

    $.each(rows, function (key, row) {
      var $row = $(row),
          maxHeight = 0; // skip apply to rows with only one item

      if (opts.byRow && $row.length <= 1) {
        $row.css(opts.property, '');
        return;
      } // iterate the row and find the max height


      $row.each(function () {
        var $that = $(this),
            display = $that.css('display') === 'inline-block' ? 'inline-block' : 'block'; // ensure we get the correct actual height (and not a previously set height value)

        var css = {
          'display': display
        };
        css[opts.property] = '';
        $that.css(css); // find the max height (including padding, but not margin)

        if ($that.outerHeight(false) > maxHeight) maxHeight = $that.outerHeight(false); // revert display block

        $that.css('display', '');
      }); // iterate the row and apply the height to all elements

      $row.each(function () {
        var $that = $(this),
            verticalPadding = 0; // handle padding and border correctly (required when not using border-box)

        if ($that.css('box-sizing') !== 'border-box') {
          verticalPadding += _parse($that.css('border-top-width')) + _parse($that.css('border-bottom-width'));
          verticalPadding += _parse($that.css('padding-top')) + _parse($that.css('padding-bottom'));
        } // set the height (accounting for padding and border)


        $that.css(opts.property, maxHeight - verticalPadding);
      });
    }); // revert hidden parents

    $hiddenParents.each(function () {
      var $that = $(this);
      $that.attr('style', $that.data('style-cache') || null);
    }); // restore scroll position if enabled

    if (matchHeight._maintainScroll) $(window).scrollTop(scrollTop / htmlHeight * $('html').outerHeight(true));
    return this;
  };
  /*
  *  matchHeight._applyDataApi
  *  applies matchHeight to all elements with a data-match-height attribute
  */


  matchHeight._applyDataApi = function () {
    var groups = {}; // generate groups by their groupId set by elements using data-match-height

    $('[data-match-height], [data-mh]').each(function () {
      var $this = $(this),
          groupId = $this.attr('data-match-height') || $this.attr('data-mh');

      if (groupId in groups) {
        groups[groupId] = groups[groupId].add($this);
      } else {
        groups[groupId] = $this;
      }
    }); // apply matchHeight to each group

    $.each(groups, function () {
      this.matchHeight(true);
    });
  };
  /*
  *  matchHeight._update
  *  updates matchHeight on all current groups with their correct options
  */


  var _update = function _update(event) {
    if (matchHeight._beforeUpdate) matchHeight._beforeUpdate(event, matchHeight._groups);
    $.each(matchHeight._groups, function () {
      matchHeight._apply(this.elements, this.options);
    });
    if (matchHeight._afterUpdate) matchHeight._afterUpdate(event, matchHeight._groups);
  };

  matchHeight._update = function (throttle, event) {
    // prevent update if fired from a resize event
    // where the viewport width hasn't actually changed
    // fixes an event looping bug in IE8
    if (event && event.type === 'resize') {
      var windowWidth = $(window).width();
      if (windowWidth === _previousResizeWidth) return;
      _previousResizeWidth = windowWidth;
    } // throttle updates


    if (!throttle) {
      _update(event);
    } else if (_updateTimeout === -1) {
      _updateTimeout = setTimeout(function () {
        _update(event);

        _updateTimeout = -1;
      }, matchHeight._throttle);
    }
  };
  /*
  *  bind events
  */
  // apply on DOM ready event


  $(matchHeight._applyDataApi); // update heights on load and resize events

  $(window).bind('load', function (event) {
    matchHeight._update(false, event);
  }); // throttled update heights on resize events

  $(window).bind('resize orientationchange', function (event) {
    matchHeight._update(true, event);
  });
})(jQuery);
"use strict";

/*!
	Colorbox v1.4.33 - 2013-10-31
	jQuery lightbox and modal window plugin
	(c) 2013 Jack Moore - http://www.jacklmoore.com/colorbox
	license: http://www.opensource.org/licenses/mit-license.php
*/
(function ($, document, window) {
  var // Default settings object.
  // See http://jacklmoore.com/colorbox for details.
  defaults = {
    // data sources
    html: false,
    photo: false,
    iframe: false,
    inline: false,
    // behavior and appearance
    transition: "elastic",
    speed: 300,
    fadeOut: 300,
    width: false,
    initialWidth: "600",
    innerWidth: false,
    maxWidth: false,
    height: false,
    initialHeight: "450",
    innerHeight: false,
    maxHeight: false,
    scalePhotos: true,
    scrolling: true,
    href: false,
    title: false,
    rel: false,
    opacity: 0.9,
    preloading: true,
    className: false,
    overlayClose: true,
    escKey: true,
    arrowKey: true,
    top: false,
    bottom: false,
    left: false,
    right: false,
    fixed: false,
    data: undefined,
    closeButton: true,
    fastIframe: true,
    open: false,
    reposition: true,
    loop: true,
    slideshow: false,
    slideshowAuto: true,
    slideshowSpeed: 2500,
    slideshowStart: "start slideshow",
    slideshowStop: "stop slideshow",
    photoRegex: /\.(gif|png|jp(e|g|eg)|bmp|ico|webp)((#|\?).*)?$/i,
    // alternate image paths for high-res displays
    retinaImage: false,
    retinaUrl: false,
    retinaSuffix: '@2x.$1',
    // internationalization
    current: "Company {current} of {total}",
    previous: "previous",
    next: "next",
    close: "close",
    xhrError: "This content failed to load.",
    imgError: "This image failed to load.",
    // accessbility
    returnFocus: true,
    trapFocus: true,
    // callbacks
    onOpen: false,
    onLoad: false,
    onComplete: false,
    onCleanup: false,
    onClosed: false
  },
      // Abstracting the HTML and event identifiers for easy rebranding
  colorbox = 'colorbox',
      prefix = 'cbox',
      boxElement = prefix + 'Element',
      // Events
  event_open = prefix + '_open',
      event_load = prefix + '_load',
      event_complete = prefix + '_complete',
      event_cleanup = prefix + '_cleanup',
      event_closed = prefix + '_closed',
      event_purge = prefix + '_purge',
      // Cached jQuery Object Variables
  $overlay,
      $box,
      $wrap,
      $content,
      $topBorder,
      $leftBorder,
      $rightBorder,
      $bottomBorder,
      $related,
      $window,
      $loaded,
      $loadingBay,
      $loadingOverlay,
      $title,
      $current,
      $slideshow,
      $next,
      $prev,
      $close,
      $groupControls,
      $events = $('<a/>'),
      // $([]) would be prefered, but there is an issue with jQuery 1.4.2
  // Variables for cached values or use across multiple functions
  settings,
      interfaceHeight,
      interfaceWidth,
      loadedHeight,
      loadedWidth,
      element,
      index,
      photo,
      open,
      active,
      closing,
      loadingTimer,
      publicMethod,
      div = "div",
      className,
      requests = 0,
      previousCSS = {},
      init; // ****************
  // HELPER FUNCTIONS
  // ****************
  // Convenience function for creating new jQuery objects

  function $tag(tag, id, css) {
    var element = document.createElement(tag);

    if (id) {
      element.id = prefix + id;
    }

    if (css) {
      element.style.cssText = css;
    }

    return $(element);
  } // Get the window height using innerHeight when available to avoid an issue with iOS
  // http://bugs.jquery.com/ticket/6724


  function winheight() {
    return window.innerHeight ? window.innerHeight : $(window).height();
  } // Determine the next and previous members in a group.


  function getIndex(increment) {
    var max = $related.length,
        newIndex = (index + increment) % max;
    return newIndex < 0 ? max + newIndex : newIndex;
  } // Convert '%' and 'px' values to integers


  function setSize(size, dimension) {
    return Math.round((/%/.test(size) ? (dimension === 'x' ? $window.width() : winheight()) / 100 : 1) * parseInt(size, 10));
  } // Checks an href to see if it is a photo.
  // There is a force photo option (photo: true) for hrefs that cannot be matched by the regex.


  function isImage(settings, url) {
    return settings.photo || settings.photoRegex.test(url);
  }

  function retinaUrl(settings, url) {
    return settings.retinaUrl && window.devicePixelRatio > 1 ? url.replace(settings.photoRegex, settings.retinaSuffix) : url;
  }

  function trapFocus(e) {
    if ('contains' in $box[0] && !$box[0].contains(e.target)) {
      e.stopPropagation();
      $box.focus();
    }
  } // Assigns function results to their respective properties


  function makeSettings() {
    var i,
        data = $.data(element, colorbox);

    if (data == null) {
      settings = $.extend({}, defaults);

      if (console && console.log) {
        console.log('Error: cboxElement missing settings object');
      }
    } else {
      settings = $.extend({}, data);
    }

    for (i in settings) {
      if ($.isFunction(settings[i]) && i.slice(0, 2) !== 'on') {
        // checks to make sure the function isn't one of the callbacks, they will be handled at the appropriate time.
        settings[i] = settings[i].call(element);
      }
    }

    settings.rel = settings.rel || element.rel || $(element).data('rel') || 'nofollow';
    settings.href = settings.href || $(element).attr('href');
    settings.title = settings.title || element.title;

    if (typeof settings.href === "string") {
      settings.href = $.trim(settings.href);
    }
  }

  function trigger(event, callback) {
    // for external use
    $(document).trigger(event); // for internal use

    $events.triggerHandler(event);

    if ($.isFunction(callback)) {
      callback.call(element);
    }
  }

  var slideshow = function () {
    var active,
        className = prefix + "Slideshow_",
        click = "click." + prefix,
        timeOut;

    function clear() {
      clearTimeout(timeOut);
    }

    function set() {
      if (settings.loop || $related[index + 1]) {
        clear();
        timeOut = setTimeout(publicMethod.next, settings.slideshowSpeed);
      }
    }

    function start() {
      $slideshow.html(settings.slideshowStop).unbind(click).one(click, stop);
      $events.bind(event_complete, set).bind(event_load, clear);
      $box.removeClass(className + "off").addClass(className + "on");
    }

    function stop() {
      clear();
      $events.unbind(event_complete, set).unbind(event_load, clear);
      $slideshow.html(settings.slideshowStart).unbind(click).one(click, function () {
        publicMethod.next();
        start();
      });
      $box.removeClass(className + "on").addClass(className + "off");
    }

    function reset() {
      active = false;
      $slideshow.hide();
      clear();
      $events.unbind(event_complete, set).unbind(event_load, clear);
      $box.removeClass(className + "off " + className + "on");
    }

    return function () {
      if (active) {
        if (!settings.slideshow) {
          $events.unbind(event_cleanup, reset);
          reset();
        }
      } else {
        if (settings.slideshow && $related[1]) {
          active = true;
          $events.one(event_cleanup, reset);

          if (settings.slideshowAuto) {
            start();
          } else {
            stop();
          }

          $slideshow.show();
        }
      }
    };
  }();

  function launch(target) {
    if (!closing) {
      element = target;
      makeSettings();
      $related = $(element);
      index = 0;

      if (settings.rel !== 'nofollow') {
        $related = $('.' + boxElement).filter(function () {
          var data = $.data(this, colorbox),
              relRelated;

          if (data) {
            relRelated = $(this).data('rel') || data.rel || this.rel;
          }

          return relRelated === settings.rel;
        });
        index = $related.index(element); // Check direct calls to Colorbox.

        if (index === -1) {
          $related = $related.add(element);
          index = $related.length - 1;
        }
      }

      $overlay.css({
        opacity: parseFloat(settings.opacity),
        cursor: settings.overlayClose ? "pointer" : "auto",
        visibility: 'visible'
      }).show();

      if (className) {
        $box.add($overlay).removeClass(className);
      }

      if (settings.className) {
        $box.add($overlay).addClass(settings.className);
      }

      className = settings.className;

      if (settings.closeButton) {
        $close.html(settings.close).appendTo($content);
      } else {
        $close.appendTo('<div/>');
      }

      if (!open) {
        open = active = true; // Prevents the page-change action from queuing up if the visitor holds down the left or right keys.
        // Show colorbox so the sizes can be calculated in older versions of jQuery

        $box.css({
          visibility: 'hidden',
          display: 'block'
        });
        $loaded = $tag(div, 'LoadedContent', 'width:0; height:0; overflow:hidden');
        $content.css({
          width: '',
          height: ''
        }).append($loaded); // Cache values needed for size calculations

        interfaceHeight = $topBorder.height() + $bottomBorder.height() + $content.outerHeight(true) - $content.height();
        interfaceWidth = $leftBorder.width() + $rightBorder.width() + $content.outerWidth(true) - $content.width();
        loadedHeight = $loaded.outerHeight(true);
        loadedWidth = $loaded.outerWidth(true); // Opens inital empty Colorbox prior to content being loaded.

        settings.w = setSize(settings.initialWidth, 'x');
        settings.h = setSize(settings.initialHeight, 'y');
        $loaded.css({
          width: '',
          height: settings.h
        });
        publicMethod.position();
        trigger(event_open, settings.onOpen);
        $groupControls.add($title).hide();
        $box.focus();

        if (settings.trapFocus) {
          // Confine focus to the modal
          // Uses event capturing that is not supported in IE8-
          if (document.addEventListener) {
            document.addEventListener('focus', trapFocus, true);
            $events.one(event_closed, function () {
              document.removeEventListener('focus', trapFocus, true);
            });
          }
        } // Return focus on closing


        if (settings.returnFocus) {
          $events.one(event_closed, function () {
            $(element).focus();
          });
        }
      }

      load();
    }
  } // Colorbox's markup needs to be added to the DOM prior to being called
  // so that the browser will go ahead and load the CSS background images.


  function appendHTML() {
    if (!$box && document.body) {
      init = false;
      $window = $(window);
      $box = $tag(div).attr({
        id: colorbox,
        'class': $.support.opacity === false ? prefix + 'IE' : '',
        // class for optional IE8 & lower targeted CSS.
        role: 'dialog',
        tabindex: '-1'
      }).hide();
      $overlay = $tag(div, "Overlay").hide();
      $loadingOverlay = $([$tag(div, "LoadingOverlay")[0], $tag(div, "LoadingGraphic")[0]]);
      $wrap = $tag(div, "Wrapper");
      $content = $tag(div, "Content").append($title = $tag(div, "Title"), $current = $tag(div, "Current"), $prev = $('<button type="button"/>').attr({
        id: prefix + 'Previous'
      }), $next = $('<button type="button"/>').attr({
        id: prefix + 'Next'
      }), $slideshow = $tag('button', "Slideshow"), $loadingOverlay);
      $close = $('<button type="button"/>').attr({
        id: prefix + 'Close'
      });
      $wrap.append( // The 3x3 Grid that makes up Colorbox
      $tag(div).append($tag(div, "TopLeft"), $topBorder = $tag(div, "TopCenter"), $tag(div, "TopRight")), $tag(div, false, 'clear:left').append($leftBorder = $tag(div, "MiddleLeft"), $content, $rightBorder = $tag(div, "MiddleRight")), $tag(div, false, 'clear:left').append($tag(div, "BottomLeft"), $bottomBorder = $tag(div, "BottomCenter"), $tag(div, "BottomRight"))).find('div div').css({
        'float': 'left'
      });
      $loadingBay = $tag(div, false, 'position:absolute; width:9999px; visibility:hidden; display:none; max-width:none;');
      $groupControls = $next.add($prev).add($current).add($slideshow);
      $(document.body).append($overlay, $box.append($wrap, $loadingBay));
    }
  } // Add Colorbox's event bindings


  function addBindings() {
    function clickHandler(e) {
      // ignore non-left-mouse-clicks and clicks modified with ctrl / command, shift, or alt.
      // See: http://jacklmoore.com/notes/click-events/
      if (!(e.which > 1 || e.shiftKey || e.altKey || e.metaKey || e.ctrlKey)) {
        e.preventDefault();
        launch(this);
      }
    }

    if ($box) {
      if (!init) {
        init = true; // Anonymous functions here keep the public method from being cached, thereby allowing them to be redefined on the fly.

        $next.click(function () {
          publicMethod.next();
        });
        $prev.click(function () {
          publicMethod.prev();
        });
        $close.click(function () {
          publicMethod.close();
        });
        $overlay.click(function () {
          if (settings.overlayClose) {
            publicMethod.close();
          }
        }); // Key Bindings

        $(document).bind('keydown.' + prefix, function (e) {
          var key = e.keyCode;

          if (open && settings.escKey && key === 27) {
            e.preventDefault();
            publicMethod.close();
          }

          if (open && settings.arrowKey && $related[1] && !e.altKey) {
            if (key === 37) {
              e.preventDefault();
              $prev.click();
            } else if (key === 39) {
              e.preventDefault();
              $next.click();
            }
          }
        });

        if ($.isFunction($.fn.on)) {
          // For jQuery 1.7+
          $(document).on('click.' + prefix, '.' + boxElement, clickHandler);
        } else {
          // For jQuery 1.3.x -> 1.6.x
          // This code is never reached in jQuery 1.9, so do not contact me about 'live' being removed.
          // This is not here for jQuery 1.9, it's here for legacy users.
          $('.' + boxElement).live('click.' + prefix, clickHandler);
        }
      }

      return true;
    }

    return false;
  } // Don't do anything if Colorbox already exists.


  if ($.colorbox) {
    return;
  } // Append the HTML when the DOM loads


  $(appendHTML); // ****************
  // PUBLIC FUNCTIONS
  // Usage format: $.colorbox.close();
  // Usage from within an iframe: parent.jQuery.colorbox.close();
  // ****************

  publicMethod = $.fn[colorbox] = $[colorbox] = function (options, callback) {
    var $this = this;
    options = options || {};
    appendHTML();

    if (addBindings()) {
      if ($.isFunction($this)) {
        // assume a call to $.colorbox
        $this = $('<a/>');
        options.open = true;
      } else if (!$this[0]) {
        // colorbox being applied to empty collection
        return $this;
      }

      if (callback) {
        options.onComplete = callback;
      }

      $this.each(function () {
        $.data(this, colorbox, $.extend({}, $.data(this, colorbox) || defaults, options));
      }).addClass(boxElement);

      if ($.isFunction(options.open) && options.open.call($this) || options.open) {
        launch($this[0]);
      }
    }

    return $this;
  };

  publicMethod.position = function (speed, loadedCallback) {
    var css,
        top = 0,
        left = 0,
        offset = $box.offset(),
        scrollTop,
        scrollLeft;
    $window.unbind('resize.' + prefix); // remove the modal so that it doesn't influence the document width/height

    $box.css({
      top: -9e4,
      left: -9e4
    });
    scrollTop = $window.scrollTop();
    scrollLeft = $window.scrollLeft();

    if (settings.fixed) {
      offset.top -= scrollTop;
      offset.left -= scrollLeft;
      $box.css({
        position: 'fixed'
      });
    } else {
      top = scrollTop;
      left = scrollLeft;
      $box.css({
        position: 'absolute'
      });
    } // keeps the top and left positions within the browser's viewport.


    if (settings.right !== false) {
      left += Math.max($window.width() - settings.w - loadedWidth - interfaceWidth - setSize(settings.right, 'x'), 0);
    } else if (settings.left !== false) {
      left += setSize(settings.left, 'x');
    } else {
      left += Math.round(Math.max($window.width() - settings.w - loadedWidth - interfaceWidth, 0) / 2);
    }

    if (settings.bottom !== false) {
      top += Math.max(winheight() - settings.h - loadedHeight - interfaceHeight - setSize(settings.bottom, 'y'), 0);
    } else if (settings.top !== false) {
      top += setSize(settings.top, 'y');
    } else {
      top += Math.round(Math.max(winheight() - settings.h - loadedHeight - interfaceHeight, 0) / 2);
    }

    $box.css({
      top: offset.top,
      left: offset.left,
      visibility: 'visible'
    }); // this gives the wrapper plenty of breathing room so it's floated contents can move around smoothly,
    // but it has to be shrank down around the size of div#colorbox when it's done.  If not,
    // it can invoke an obscure IE bug when using iframes.

    $wrap[0].style.width = $wrap[0].style.height = "9999px";

    function modalDimensions() {
      $topBorder[0].style.width = $bottomBorder[0].style.width = $content[0].style.width = parseInt($box[0].style.width, 10) - interfaceWidth + 'px';
      $content[0].style.height = $leftBorder[0].style.height = $rightBorder[0].style.height = parseInt($box[0].style.height, 10) - interfaceHeight + 'px';
    }

    css = {
      width: settings.w + loadedWidth + interfaceWidth,
      height: settings.h + loadedHeight + interfaceHeight,
      top: top,
      left: left
    }; // setting the speed to 0 if the content hasn't changed size or position

    if (speed) {
      var tempSpeed = 0;
      $.each(css, function (i) {
        if (css[i] !== previousCSS[i]) {
          tempSpeed = speed;
          return;
        }
      });
      speed = tempSpeed;
    }

    previousCSS = css;

    if (!speed) {
      $box.css(css);
    }

    $box.dequeue().animate(css, {
      duration: speed || 0,
      complete: function complete() {
        modalDimensions();
        active = false; // shrink the wrapper down to exactly the size of colorbox to avoid a bug in IE's iframe implementation.

        $wrap[0].style.width = settings.w + loadedWidth + interfaceWidth + "px";
        $wrap[0].style.height = settings.h + loadedHeight + interfaceHeight + "px";

        if (settings.reposition) {
          setTimeout(function () {
            // small delay before binding onresize due to an IE8 bug.
            $window.bind('resize.' + prefix, publicMethod.position);
          }, 1);
        }

        if (loadedCallback) {
          loadedCallback();
        }
      },
      step: modalDimensions
    });
  };

  publicMethod.resize = function (options) {
    var scrolltop;

    if (open) {
      options = options || {};

      if (options.width) {
        settings.w = setSize(options.width, 'x') - loadedWidth - interfaceWidth;
      }

      if (options.innerWidth) {
        settings.w = setSize(options.innerWidth, 'x');
      }

      $loaded.css({
        width: settings.w
      });

      if (options.height) {
        settings.h = setSize(options.height, 'y') - loadedHeight - interfaceHeight;
      }

      if (options.innerHeight) {
        settings.h = setSize(options.innerHeight, 'y');
      }

      if (!options.innerHeight && !options.height) {
        scrolltop = $loaded.scrollTop();
        $loaded.css({
          height: "auto"
        });
        settings.h = $loaded.height();
      }

      $loaded.css({
        height: settings.h
      });

      if (scrolltop) {
        $loaded.scrollTop(scrolltop);
      }

      publicMethod.position(settings.transition === "none" ? 0 : settings.speed);
    }
  };

  publicMethod.prep = function (object) {
    if (!open) {
      return;
    }

    var callback,
        speed = settings.transition === "none" ? 0 : settings.speed;
    $loaded.empty().remove(); // Using empty first may prevent some IE7 issues.

    $loaded = $tag(div, 'LoadedContent').append(object);

    function getWidth() {
      settings.w = settings.w || $loaded.width();
      settings.w = settings.mw && settings.mw < settings.w ? settings.mw : settings.w;
      return settings.w;
    }

    function getHeight() {
      settings.h = settings.h || $loaded.height();
      settings.h = settings.mh && settings.mh < settings.h ? settings.mh : settings.h;
      return settings.h;
    }

    $loaded.hide().appendTo($loadingBay.show()) // content has to be appended to the DOM for accurate size calculations.
    .css({
      width: getWidth(),
      overflow: settings.scrolling ? 'auto' : 'hidden'
    }).css({
      height: getHeight()
    }) // sets the height independently from the width in case the new width influences the value of height.
    .prependTo($content);
    $loadingBay.hide(); // floating the IMG removes the bottom line-height and fixed a problem where IE miscalculates the width of the parent element as 100% of the document width.

    $(photo).css({
      'float': 'none'
    });

    callback = function callback() {
      var total = $related.length,
          iframe,
          frameBorder = 'frameBorder',
          allowTransparency = 'allowTransparency',
          complete;

      if (!open) {
        return;
      }

      function removeFilter() {
        // Needed for IE7 & IE8 in versions of jQuery prior to 1.7.2
        if ($.support.opacity === false) {
          $box[0].style.removeAttribute('filter');
        }
      }

      complete = function complete() {
        clearTimeout(loadingTimer);
        $loadingOverlay.hide();
        trigger(event_complete, settings.onComplete);
      };

      $title.html(settings.title).add($loaded).show();

      if (total > 1) {
        // handle grouping
        if (typeof settings.current === "string") {
          $current.html(settings.current.replace('{current}', index + 1).replace('{total}', total)).show();
        }

        $next[settings.loop || index < total - 1 ? "show" : "hide"]().html(settings.next);
        $prev[settings.loop || index ? "show" : "hide"]().html(settings.previous);
        slideshow(); // Preloads images within a rel group

        if (settings.preloading) {
          $.each([getIndex(-1), getIndex(1)], function () {
            var src,
                img,
                i = $related[this],
                data = $.data(i, colorbox);

            if (data && data.href) {
              src = data.href;

              if ($.isFunction(src)) {
                src = src.call(i);
              }
            } else {
              src = $(i).attr('href');
            }

            if (src && isImage(data, src)) {
              src = retinaUrl(data, src);
              img = document.createElement('img');
              img.src = src;
            }
          });
        }
      } else {
        $groupControls.hide();
      }

      if (settings.iframe) {
        iframe = $tag('iframe')[0];

        if (frameBorder in iframe) {
          iframe[frameBorder] = 0;
        }

        if (allowTransparency in iframe) {
          iframe[allowTransparency] = "true";
        }

        if (!settings.scrolling) {
          iframe.scrolling = "no";
        }

        $(iframe).attr({
          src: settings.href,
          name: new Date().getTime(),
          // give the iframe a unique name to prevent caching
          'class': prefix + 'Iframe',
          allowFullScreen: true,
          // allow HTML5 video to go fullscreen
          webkitAllowFullScreen: true,
          mozallowfullscreen: true
        }).one('load', complete).appendTo($loaded);
        $events.one(event_purge, function () {
          iframe.src = "//about:blank";
        });

        if (settings.fastIframe) {
          $(iframe).trigger('load');
        }
      } else {
        complete();
      }

      if (settings.transition === 'fade') {
        $box.fadeTo(speed, 1, removeFilter);
      } else {
        removeFilter();
      }
    };

    if (settings.transition === 'fade') {
      $box.fadeTo(speed, 0, function () {
        publicMethod.position(0, callback);
      });
    } else {
      publicMethod.position(speed, callback);
    }
  };

  function load() {
    var href,
        setResize,
        prep = publicMethod.prep,
        $inline,
        request = ++requests;
    active = true;
    photo = false;
    element = $related[index];
    makeSettings();
    trigger(event_purge);
    trigger(event_load, settings.onLoad);
    settings.h = settings.height ? setSize(settings.height, 'y') - loadedHeight - interfaceHeight : settings.innerHeight && setSize(settings.innerHeight, 'y');
    settings.w = settings.width ? setSize(settings.width, 'x') - loadedWidth - interfaceWidth : settings.innerWidth && setSize(settings.innerWidth, 'x'); // Sets the minimum dimensions for use in image scaling

    settings.mw = settings.w;
    settings.mh = settings.h; // Re-evaluate the minimum width and height based on maxWidth and maxHeight values.
    // If the width or height exceed the maxWidth or maxHeight, use the maximum values instead.

    if (settings.maxWidth) {
      settings.mw = setSize(settings.maxWidth, 'x') - loadedWidth - interfaceWidth;
      settings.mw = settings.w && settings.w < settings.mw ? settings.w : settings.mw;
    }

    if (settings.maxHeight) {
      settings.mh = setSize(settings.maxHeight, 'y') - loadedHeight - interfaceHeight;
      settings.mh = settings.h && settings.h < settings.mh ? settings.h : settings.mh;
    }

    href = settings.href;
    loadingTimer = setTimeout(function () {
      $loadingOverlay.show();
    }, 100);

    if (settings.inline) {
      // Inserts an empty placeholder where inline content is being pulled from.
      // An event is bound to put inline content back when Colorbox closes or loads new content.
      $inline = $tag(div).hide().insertBefore($(href)[0]);
      $events.one(event_purge, function () {
        $inline.replaceWith($loaded.children());
      });
      prep($(href));
    } else if (settings.iframe) {
      // IFrame element won't be added to the DOM until it is ready to be displayed,
      // to avoid problems with DOM-ready JS that might be trying to run in that iframe.
      prep(" ");
    } else if (settings.html) {
      prep(settings.html);
    } else if (isImage(settings, href)) {
      href = retinaUrl(settings, href);
      photo = document.createElement('img');
      $(photo).addClass(prefix + 'Photo').bind('error', function () {
        settings.title = false;
        prep($tag(div, 'Error').html(settings.imgError));
      }).one('load', function () {
        var percent;

        if (request !== requests) {
          return;
        }

        $.each(['alt', 'longdesc', 'aria-describedby'], function (i, val) {
          var attr = $(element).attr(val) || $(element).attr('data-' + val);

          if (attr) {
            photo.setAttribute(val, attr);
          }
        });

        if (settings.retinaImage && window.devicePixelRatio > 1) {
          photo.height = photo.height / window.devicePixelRatio;
          photo.width = photo.width / window.devicePixelRatio;
        }

        if (settings.scalePhotos) {
          setResize = function setResize() {
            photo.height -= photo.height * percent;
            photo.width -= photo.width * percent;
          };

          if (settings.mw && photo.width > settings.mw) {
            percent = (photo.width - settings.mw) / photo.width;
            setResize();
          }

          if (settings.mh && photo.height > settings.mh) {
            percent = (photo.height - settings.mh) / photo.height;
            setResize();
          }
        }

        if (settings.h) {
          photo.style.marginTop = Math.max(settings.mh - photo.height, 0) / 2 + 'px';
        }

        if ($related[1] && (settings.loop || $related[index + 1])) {
          photo.style.cursor = 'pointer';

          photo.onclick = function () {
            publicMethod.next();
          };
        }

        photo.style.width = photo.width + 'px';
        photo.style.height = photo.height + 'px';
        setTimeout(function () {
          // A pause because Chrome will sometimes report a 0 by 0 size otherwise.
          prep(photo);
        }, 1);
      });
      setTimeout(function () {
        // A pause because Opera 10.6+ will sometimes not run the onload function otherwise.
        photo.src = href;
      }, 1);
    } else if (href) {
      $loadingBay.load(href, settings.data, function (data, status) {
        if (request === requests) {
          prep(status === 'error' ? $tag(div, 'Error').html(settings.xhrError) : $(this).contents());
        }
      });
    }
  } // Navigates to the next page/image in a set.


  publicMethod.next = function () {
    if (!active && $related[1] && (settings.loop || $related[index + 1])) {
      index = getIndex(1);
      launch($related[index]);
    }
  };

  publicMethod.prev = function () {
    if (!active && $related[1] && (settings.loop || index)) {
      index = getIndex(-1);
      launch($related[index]);
    }
  }; // Note: to use this within an iframe use the following format: parent.jQuery.colorbox.close();


  publicMethod.close = function () {
    if (open && !closing) {
      closing = true;
      open = false;
      trigger(event_cleanup, settings.onCleanup);
      $window.unbind('.' + prefix);
      $overlay.fadeTo(settings.fadeOut || 0, 0);
      $box.stop().fadeTo(settings.fadeOut || 0, 0, function () {
        $box.add($overlay).css({
          'opacity': 1,
          cursor: 'auto'
        }).hide();
        trigger(event_purge);
        $loaded.empty().remove(); // Using empty first may prevent some IE7 issues.

        setTimeout(function () {
          closing = false;
          trigger(event_closed, settings.onClosed);
        }, 1);
      });
    }
  }; // Removes changes Colorbox made to the document, but does not remove the plugin.


  publicMethod.remove = function () {
    if (!$box) {
      return;
    }

    $box.stop();
    $.colorbox.close();
    $box.stop().remove();
    $overlay.remove();
    closing = false;
    $box = null;
    $('.' + boxElement).removeData(colorbox).removeClass(boxElement);
    $(document).unbind('click.' + prefix);
  }; // A method for fetching the current element Colorbox is referencing.
  // returns a jQuery object.


  publicMethod.element = function () {
    return $(element);
  };

  publicMethod.settings = defaults;
})(jQuery, document, window);
"use strict";

/**
 * customizer.js
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */
(function ($) {
  // Site title and description.
  wp.customize('blogname', function (value) {
    value.bind(function (to) {
      $('.site-title a').text(to);
    });
  });
  wp.customize('blogdescription', function (value) {
    value.bind(function (to) {
      $('.site-description').text(to);
    });
  }); // Header text color.

  wp.customize('header_textcolor', function (value) {
    value.bind(function (to) {
      if ('blank' === to) {
        $('.site-title a, .site-description').css({
          'clip': 'rect(1px, 1px, 1px, 1px)',
          'position': 'absolute'
        });
      } else {
        $('.site-title a, .site-description').css({
          'clip': 'auto',
          'position': 'relative'
        });
        $('.site-title a, .site-description').css({
          'color': to
        });
      }
    });
  });
})(jQuery);
"use strict";

/**
 * skip-link-focus-fix.js
 *
 * Helps with accessibility for keyboard only users.
 *
 * Learn more: https://git.io/vWdr2
 */
(function () {
  var is_webkit = navigator.userAgent.toLowerCase().indexOf('webkit') > -1,
      is_opera = navigator.userAgent.toLowerCase().indexOf('opera') > -1,
      is_ie = navigator.userAgent.toLowerCase().indexOf('msie') > -1;

  if ((is_webkit || is_opera || is_ie) && document.getElementById && window.addEventListener) {
    window.addEventListener('hashchange', function () {
      var id = location.hash.substring(1),
          element;

      if (!/^[A-z0-9_-]+$/.test(id)) {
        return;
      }

      element = document.getElementById(id);

      if (element) {
        if (!/^(?:a|select|input|button|textarea)$/i.test(element.tagName)) {
          element.tabIndex = -1;
        }

        element.focus();
      }
    }, false);
  }
})();
"use strict";

/*! WOW - v1.0.2 - 2014-10-28
* Copyright (c) 2014 Matthieu Aussaguel; Licensed MIT */
(function () {
  var a,
      b,
      c,
      d,
      e,
      f = function f(a, b) {
    return function () {
      return a.apply(b, arguments);
    };
  },
      g = [].indexOf || function (a) {
    for (var b = 0, c = this.length; c > b; b++) {
      if (b in this && this[b] === a) return b;
    }

    return -1;
  };

  b = function () {
    function a() {}

    return a.prototype.extend = function (a, b) {
      var c, d;

      for (c in b) {
        d = b[c], null == a[c] && (a[c] = d);
      }

      return a;
    }, a.prototype.isMobile = function (a) {
      return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(a);
    }, a.prototype.addEvent = function (a, b, c) {
      return null != a.addEventListener ? a.addEventListener(b, c, !1) : null != a.attachEvent ? a.attachEvent("on" + b, c) : a[b] = c;
    }, a.prototype.removeEvent = function (a, b, c) {
      return null != a.removeEventListener ? a.removeEventListener(b, c, !1) : null != a.detachEvent ? a.detachEvent("on" + b, c) : delete a[b];
    }, a.prototype.innerHeight = function () {
      return "innerHeight" in window ? window.innerHeight : document.documentElement.clientHeight;
    }, a;
  }(), c = this.WeakMap || this.MozWeakMap || (c = function () {
    function a() {
      this.keys = [], this.values = [];
    }

    return a.prototype.get = function (a) {
      var b, c, d, e, f;

      for (f = this.keys, b = d = 0, e = f.length; e > d; b = ++d) {
        if (c = f[b], c === a) return this.values[b];
      }
    }, a.prototype.set = function (a, b) {
      var c, d, e, f, g;

      for (g = this.keys, c = e = 0, f = g.length; f > e; c = ++e) {
        if (d = g[c], d === a) return void (this.values[c] = b);
      }

      return this.keys.push(a), this.values.push(b);
    }, a;
  }()), a = this.MutationObserver || this.WebkitMutationObserver || this.MozMutationObserver || (a = function () {
    function a() {
      "undefined" != typeof console && null !== console && console.warn("MutationObserver is not supported by your browser."), "undefined" != typeof console && null !== console && console.warn("WOW.js cannot detect dom mutations, please call .sync() after loading new content.");
    }

    return a.notSupported = !0, a.prototype.observe = function () {}, a;
  }()), d = this.getComputedStyle || function (a) {
    return this.getPropertyValue = function (b) {
      var c;
      return "float" === b && (b = "styleFloat"), e.test(b) && b.replace(e, function (a, b) {
        return b.toUpperCase();
      }), (null != (c = a.currentStyle) ? c[b] : void 0) || null;
    }, this;
  }, e = /(\-([a-z]){1})/g, this.WOW = function () {
    function e(a) {
      null == a && (a = {}), this.scrollCallback = f(this.scrollCallback, this), this.scrollHandler = f(this.scrollHandler, this), this.start = f(this.start, this), this.scrolled = !0, this.config = this.util().extend(a, this.defaults), this.animationNameCache = new c();
    }

    return e.prototype.defaults = {
      boxClass: "wow",
      animateClass: "animated",
      offset: 0,
      mobile: !0,
      live: !0
    }, e.prototype.init = function () {
      var a;
      return this.element = window.document.documentElement, "interactive" === (a = document.readyState) || "complete" === a ? this.start() : this.util().addEvent(document, "DOMContentLoaded", this.start), this.finished = [];
    }, e.prototype.start = function () {
      var b, c, d, e;
      if (this.stopped = !1, this.boxes = function () {
        var a, c, d, e;

        for (d = this.element.querySelectorAll("." + this.config.boxClass), e = [], a = 0, c = d.length; c > a; a++) {
          b = d[a], e.push(b);
        }

        return e;
      }.call(this), this.all = function () {
        var a, c, d, e;

        for (d = this.boxes, e = [], a = 0, c = d.length; c > a; a++) {
          b = d[a], e.push(b);
        }

        return e;
      }.call(this), this.boxes.length) if (this.disabled()) this.resetStyle();else for (e = this.boxes, c = 0, d = e.length; d > c; c++) {
        b = e[c], this.applyStyle(b, !0);
      }
      return this.disabled() || (this.util().addEvent(window, "scroll", this.scrollHandler), this.util().addEvent(window, "resize", this.scrollHandler), this.interval = setInterval(this.scrollCallback, 50)), this.config.live ? new a(function (a) {
        return function (b) {
          var c, d, e, f, g;

          for (g = [], e = 0, f = b.length; f > e; e++) {
            d = b[e], g.push(function () {
              var a, b, e, f;

              for (e = d.addedNodes || [], f = [], a = 0, b = e.length; b > a; a++) {
                c = e[a], f.push(this.doSync(c));
              }

              return f;
            }.call(a));
          }

          return g;
        };
      }(this)).observe(document.body, {
        childList: !0,
        subtree: !0
      }) : void 0;
    }, e.prototype.stop = function () {
      return this.stopped = !0, this.util().removeEvent(window, "scroll", this.scrollHandler), this.util().removeEvent(window, "resize", this.scrollHandler), null != this.interval ? clearInterval(this.interval) : void 0;
    }, e.prototype.sync = function () {
      return a.notSupported ? this.doSync(this.element) : void 0;
    }, e.prototype.doSync = function (a) {
      var b, c, d, e, f;

      if (null == a && (a = this.element), 1 === a.nodeType) {
        for (a = a.parentNode || a, e = a.querySelectorAll("." + this.config.boxClass), f = [], c = 0, d = e.length; d > c; c++) {
          b = e[c], g.call(this.all, b) < 0 ? (this.boxes.push(b), this.all.push(b), this.stopped || this.disabled() ? this.resetStyle() : this.applyStyle(b, !0), f.push(this.scrolled = !0)) : f.push(void 0);
        }

        return f;
      }
    }, e.prototype.show = function (a) {
      return this.applyStyle(a), a.className = "" + a.className + " " + this.config.animateClass;
    }, e.prototype.applyStyle = function (a, b) {
      var c, d, e;
      return d = a.getAttribute("data-wow-duration"), c = a.getAttribute("data-wow-delay"), e = a.getAttribute("data-wow-iteration"), this.animate(function (f) {
        return function () {
          return f.customStyle(a, b, d, c, e);
        };
      }(this));
    }, e.prototype.animate = function () {
      return "requestAnimationFrame" in window ? function (a) {
        return window.requestAnimationFrame(a);
      } : function (a) {
        return a();
      };
    }(), e.prototype.resetStyle = function () {
      var a, b, c, d, e;

      for (d = this.boxes, e = [], b = 0, c = d.length; c > b; b++) {
        a = d[b], e.push(a.style.visibility = "visible");
      }

      return e;
    }, e.prototype.customStyle = function (a, b, c, d, e) {
      return b && this.cacheAnimationName(a), a.style.visibility = b ? "hidden" : "visible", c && this.vendorSet(a.style, {
        animationDuration: c
      }), d && this.vendorSet(a.style, {
        animationDelay: d
      }), e && this.vendorSet(a.style, {
        animationIterationCount: e
      }), this.vendorSet(a.style, {
        animationName: b ? "none" : this.cachedAnimationName(a)
      }), a;
    }, e.prototype.vendors = ["moz", "webkit"], e.prototype.vendorSet = function (a, b) {
      var c, d, e, f;
      f = [];

      for (c in b) {
        d = b[c], a["" + c] = d, f.push(function () {
          var b, f, g, h;

          for (g = this.vendors, h = [], b = 0, f = g.length; f > b; b++) {
            e = g[b], h.push(a["" + e + c.charAt(0).toUpperCase() + c.substr(1)] = d);
          }

          return h;
        }.call(this));
      }

      return f;
    }, e.prototype.vendorCSS = function (a, b) {
      var c, e, f, g, h, i;

      for (e = d(a), c = e.getPropertyCSSValue(b), i = this.vendors, g = 0, h = i.length; h > g; g++) {
        f = i[g], c = c || e.getPropertyCSSValue("-" + f + "-" + b);
      }

      return c;
    }, e.prototype.animationName = function (a) {
      var b;

      try {
        b = this.vendorCSS(a, "animation-name").cssText;
      } catch (c) {
        b = d(a).getPropertyValue("animation-name");
      }

      return "none" === b ? "" : b;
    }, e.prototype.cacheAnimationName = function (a) {
      return this.animationNameCache.set(a, this.animationName(a));
    }, e.prototype.cachedAnimationName = function (a) {
      return this.animationNameCache.get(a);
    }, e.prototype.scrollHandler = function () {
      return this.scrolled = !0;
    }, e.prototype.scrollCallback = function () {
      var a;
      return !this.scrolled || (this.scrolled = !1, this.boxes = function () {
        var b, c, d, e;

        for (d = this.boxes, e = [], b = 0, c = d.length; c > b; b++) {
          a = d[b], a && (this.isVisible(a) ? this.show(a) : e.push(a));
        }

        return e;
      }.call(this), this.boxes.length || this.config.live) ? void 0 : this.stop();
    }, e.prototype.offsetTop = function (a) {
      for (var b; void 0 === a.offsetTop;) {
        a = a.parentNode;
      }

      for (b = a.offsetTop; a = a.offsetParent;) {
        b += a.offsetTop;
      }

      return b;
    }, e.prototype.isVisible = function (a) {
      var b, c, d, e, f;
      return c = a.getAttribute("data-wow-offset") || this.config.offset, f = window.pageYOffset, e = f + Math.min(this.element.clientHeight, this.util().innerHeight()) - c, d = this.offsetTop(a), b = d + a.clientHeight, e >= d && b >= f;
    }, e.prototype.util = function () {
      return null != this._util ? this._util : this._util = new b();
    }, e.prototype.disabled = function () {
      return !this.config.mobile && this.util().isMobile(navigator.userAgent);
    }, e;
  }();
}).call(void 0);