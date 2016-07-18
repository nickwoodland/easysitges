(function($){

"use strict";


/* Tab Panel
------------------------------------------------------------------- */
var tabModule = {
  el: {},

  setupElements: function() {
    this.el.$tabNav = $('.block-tab-nav');
  },

  eventBinding: function() {
    this.el.$tabNav.on('click', 'a', $.proxy(this.tabClickEvent, this));
  },

  tabClickEvent: function(e) {
    e.preventDefault();
    var $link = $(e.currentTarget),
        target = $link.attr('href');

    this.openTab( $link, target, $(target) );
  },

  openTab: function( $link, target, $target ) {
    if( $target.length > 0 ) {
      // Show Panel
      $target
        .fadeIn(250)
        .siblings('.tab-panel').hide();

      // Active link
      $link
        .addClass('active')
        .parent().siblings('li').find('a').removeClass('active');

      // Change Hash
      if( !$('body').hasClass('home') ) {
        $target.attr('id', '');
        window.location.hash = target;
        setTimeout(function(){
          $target.attr('id', target.replace('#', ''));
        }, 200);
      }
    }
  },

  hashChange: function() {
    var hash = window.location.hash,
        $target = $(hash),
        $link;

    if( $target.length > 0 ) {
      $link = $('a[href="'+ hash +'"]');
      this.openTab($link, hash, $target);
    } else {
      this.openFirstTab();
    }
  },

  openFirstTab: function() {
    this.el.$tabNav.find('li:first-child a').trigger('click');
  },

  init: function() {
    this.setupElements();
    this.eventBinding();

    // Special case for order filter on dashboard page
    if( $('body').hasClass('page-template-template-dashboard-php') ) {
      var url = window.location.href;
      if( url.split('?').length > 1 ) {
        window.location.hash = '#order-list';
      }
    }

    // if( $(window.location.hash).length > 0 ) {
      this.hashChange();
    // } else {
    //   // Click the first tab panel
    //   this.openFirstTab();
    // }

  }
};


// Alert close button
$('.alert .close').click(function(e){
	e.preventDefault();
	$(this).parent().slideUp();
	$.cookie('hide_announcement', 'true', { expires: 2 });
});

// Footer widgets
$('.footer-widgets .widget:nth-child(4n+5)').addClass('alpha');

// Add class button for comment submit button
$('#comments input[type="submit"]').addClass('button button-bold button-upper');

// Collapsible Top Nav
$('.collapse-button').click(function(e){
  e.preventDefault();
  var $el = $(this),
      $navCollapse = $('.nav-collapse');

  // If collapsed
  if( !$el.hasClass('collapsed') ) {
    $navCollapse.animate({height: $navCollapse.children().outerHeight(true) }, 200);
    $el.addClass('collapsed');
  } else {
    $navCollapse.animate({height: 0 }, 200);
    $el.removeClass('collapsed');
  }
});


/* -------------------------------------------------------------------
	Fix iOS Scaling bug
	// Rewritten version
	// By @mathias, @cheeaun and @jdalton
------------------------------------------------------------------- */
(function(doc) {

	var addEvent = 'addEventListener',
	    type = 'gesturestart',
	    qsa = 'querySelectorAll',
	    scales = [1, 1],
	    meta = qsa in doc ? doc[qsa]('meta[name=viewport]') : [];

	function fix() {
		meta.content = 'width=device-width,minimum-scale=' + scales[0] + ',maximum-scale=' + scales[1];
		doc.removeEventListener(type, fix, true);
	}

	if ((meta = meta[meta.length - 1]) && addEvent in doc) {
		fix();
		scales = [.25, 1.6];
		doc[addEvent](type, fix, true);
	}

}(document));


/* -------------------------------------------------------------------
	Select Box Replacement
------------------------------------------------------------------- */
$('.property-ordering select, .advance-search select, .property-submission select, .dsidx-search select')
	.addClass('custom-select')
	.each(function(i){
		$(this).attr('tabindex', i+1);
	});

$('.property-ordering .custom-select, .property-submission .custom-select, .dsidx-search select').dropkick({
	change: function(value, label) {
	  $(this).trigger('change');
	}
});
$('.advance-search .custom-select').dropkick({
	theme: 'metro',
	change: function(value, label) {
	  $(this).trigger('change');
	}
});

/* -------------------------------------------------------------------
	Radio Button Replacement
------------------------------------------------------------------- */
$('.advance-search .input-radio label, .property-submission .colabs_input_radio_desc').each(function(){
	var $input = $('input', this),
			$this = $(this);
	$input.hide();
	$('<span class="radio-button"></span>').prependTo($this);
	if( $input.is(":checked") ) {
		$this.addClass('selected');
	}
});

// Radio button on change event
$('.advance-search .input-radio input, .property-submission .colabs_input_radio_desc input').change(function(e){
	$(this).parent().addClass('selected').siblings().removeClass('selected');
});

// Location field
$('[name="property_location"]').change(function(){
	var $this = $(this);

	if( $this.val() == -1 ) {
		$this.next().show();
	} else {
		$this.next().hide();
	}
});


/* -------------------------------------------------------------------
	Input File Replacement
------------------------------------------------------------------- */
$('.property-submission .input-file').each(function(){
	var $this = $(this),
			html = [
				'<div class="input-file-block" id="'+ $this.attr('name') +'">',
					'<div class="input-file-value">No file choosen</div>',
					'<a class="button">Choose File</a>',
				'</div>'
			];

	// Hide input file
	$this.find('input[type="file"]').hide();

	// Append the replacement
	$( html.join('') ).appendTo( $this );
});

$('.input-file-block .button').click(function(e){
	$(this).parent().prev('input').trigger('click');
});

$('.property-submission .input-file input').change(function(){
	var $this = $(this),
			fullPath = $this.val();

	if( fullPath ) {
		var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
		var filename = fullPath.substring(startIndex);
		if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
			filename = filename.substring(1);
		}
		$this.next('.input-file-block').find('.input-file-value').text( filename );
	}

});



/* -------------------------------------------------------------------
	Price Range Slider
------------------------------------------------------------------- */
(function priceSlider(){
	var $price_slider_wrapper = $('.price-slider-wrapper');

	// Loop each price slider
	$price_slider_wrapper.each(function( index ){
		var $this         = $(this),
				$price_label  = $this.find('.price-slider-label'),
				$price_slider = $this.find('.price-slider'),
				$min_price    = $this.find('.min-price'),
				$max_price    = $this.find('.max-price'),
				min_price     = parseInt( $min_price.data('min') ),
				max_price     = parseInt( $max_price.data('max') ),
				min_price_val = parseInt( $min_price.val() ? $min_price.val() : min_price ),
				max_price_val = parseInt( $max_price.val() ? $max_price.val() : max_price ),
				step					= 100;

		// Ger markup ready for slider
		$min_price.add($max_price).hide();
		$price_label.add($price_slider).show();

		// Change step
		if( $this.parent().hasClass('property-size') ) {
			step = 1;
		}

		// Create the slider
		$price_slider.slider({
			range: true,
			animate: true,
			min: min_price,
			max: max_price,
			step: step,
			values: [min_price_val, max_price_val],
			create: function( event, ui) {
				$min_price.val( min_price_val );
				$max_price.val( max_price_val );

				$this.find('.from').html( min_price_val );
				$this.find('.to').html( max_price_val );
			},
			slide: function( event, ui ) {
				$min_price.val( ui.values[0] );
				$max_price.val( ui.values[1] );

				$this.find('.from').html( ui.values[0] );
				$this.find('.to').html( ui.values[1] );
			},
			change: function( event, ui ) {
				$('body').trigger( 'price-slider-change', [ui.values[0], ui.values[1]] );
			}
		});

	});// end each

})();

/* -------------------------------------------------------------------
	Show Advance Search
------------------------------------------------------------------- */
$('.advance-search-button a').click(function(e){
	e.preventDefault();
	var $this = $(this),
			$search_extra = $('.advance-search-extra'),
			$form = $this.closest('form'),
			$identifier = $('.price-panel-identifier');

	// Show
	if( $this.hasClass('show') ) {
		$search_extra.fadeTo(250,1);
		$form.addClass('active');
		$('.advance-search-button .hide').fadeTo(250,1);
		$identifier.val('true');
	}

	// Hide
	else if ( $this.hasClass('hide') ) {
		$form.removeClass('active');
		$search_extra.fadeTo(250, 0, function(){ $(this).hide() });
		$this.fadeTo(250,0);
		$identifier.val('false');
	}

});


/* -------------------------------------------------------------------
	Post Grid Masonry
------------------------------------------------------------------- */
var postGrid = {
	$postgrid: $('.post-grid'),
	masonryOpts: {
		itemSelector: '.entry-post',
		isResizable: true,
		gutterWidth: 27,
		isFitWidth: true
	},

	eventBinding: function() {
		$(window).bind('load resize', $.proxy(this.changeWidth, this));
	},

	runMasonry: function() {
		var _self = this;
		_self.$postgrid.imagesLoaded(function(){
			$(this).masonry( _self.masonryOpts );
			_self.$postgrid.find('.entry-post').fadeTo(300, 1);
			_self.$postgrid.removeClass('loading');
			_self.eventBinding();
		});
	},

	changeWidth: function(event) {
		var $this = $(event.currentTarget);
		this.masonryOpts.gutterWidth = 27;

		if( $this.width() < 767 && $this.width() > 321 ) {
			this.masonryOpts.gutterWidth = 10;
		}
		this.$postgrid.masonry('option', this.masonryOpts).masonry('reload');
	},

	init: function() {
		this.$postgrid.addClass('loading').find('.entry-post').css('opacity', 0);
		this.runMasonry();
		// this.eventBinding();
	}
}
postGrid.init();


/* -------------------------------------------------------------------
	Single Property Gallery
------------------------------------------------------------------- */
var propGallery = {
	$galleryWrapper: $('.property-gallery-thumb-wrapper'),
	$galleryLarge: $('.property-gallery-large'),
	$gallery: $('.property-gallery-thumb'),
	$galleryNav: null,

	eventBinding: function() {
		this.$gallery.on('click', 'a', $.proxy(this.showImage, this));
	},

	showImage: function(e) {
		e.preventDefault();
		var _self = this,
				$this = $(e.currentTarget),
				full_url = $this.attr('href');

        console.log('fill_url' + full_url);

		// Stop if user click same thumbnail
		if( full_url == this.$galleryLarge.find('img').attr('src') ) {
			return;
		}

		// Add active class
		_self.$gallery.find('a').removeClass('active');
		$this.addClass('active');

		// Hide large image
		this.$galleryLarge.find('img').fadeTo(300, 0);
		clearTimeout( changeAttr );

		// Make a delay before changing image src
		var changeAttr = setTimeout(function(){
			_self.$galleryLarge.find('img').attr('src', full_url);
		}, 300);

		// Show image when it has been loaded
		this.$galleryLarge.imagesLoaded(function(){
			_self.$galleryLarge.find('img').fadeTo(300, 1);
		});
	},

	createSliderNav: function() {
		var nav = [
			'<div class="property-gallery-nav">',
				'<a class="prev" href="#"><i class="icon-chevron-left"></i></a>',
				'<a class="next" href="#"><i class="icon-chevron-right"></i></a>',
			'</div>'
		];
		$(nav.join('')).appendTo( this.$galleryWrapper );
		this.$galleryNav = $('.property-gallery-nav');
	},

	createSlider: function() {
		var _self = this;
		_self.createSliderNav();

		_self.$gallery.imagesLoaded(function(){
			$(this).carouFredSel({
				auto: false,
				circular: false,
				infinite: false,
				width: '100%',
				align: 'center',
				prev: _self.$galleryNav.find('.prev'),
				next: _self.$galleryNav.find('.next'),
				items: {
					width: 76,
					height: 76,
					visible: {
						min: 2,
						max: 7
					}
				}
			});

			// Trigger click the first thumbnail
			_self.$gallery.find('a:first-child').trigger('click');
		});
	},

	createFirstImage: function() {
		var url = this.$gallery.find('a:first').attr('href'),
				$html_img = $('<img src="'+ url +'">');

		$html_img.appendTo( this.$galleryLarge );
		this.$gallery.find('a:first').addClass('active');
	},

	init: function() {
		if( this.$gallery.length > 0 ) {
			this.createFirstImage();
			this.createSlider();
			this.eventBinding();
		}
	}
}
propGallery.init();


/* -------------------------------------------------------------------
	Superfish Dropdown Menu
------------------------------------------------------------------- */
$('.top-menu ul:first, .main-menu ul:first')
	.addClass('sf-menu')
	.superfish({
		delay: 300,
		animation: { opacity: 'show' },
		speed: 'fast',
		dropShadows: false,
		disableHI: true,
		onInit: function() {
			var $el = $(this);
			if( $el.attr('id') == 'menu-main-menu' ) {
				$el.find('.sf-sub-indicator').html('<i class="icon-chevron-down"></i>');
				$el.find('ul .sf-sub-indicator').html('<i class="icon-chevron-right"></i>');
			} else {
				$el.find('.sf-sub-indicator').html('<i class="icon-caret-down"></i>');
				$el.find('ul .sf-sub-indicator').html('<i class="icon-caret-right"></i>');
			}
		}
	});

// Mobile Menu
$('.main-menu .sf-menu').mobileMenu();
$('.main-menu .select-menu').selectbox();

/* -------------------------------------------------------------------
	Single Property Tabs
------------------------------------------------------------------- */
(function propertyTabs(){
	var $tabs = $('.property-info-tabs, .property-details-tabs, .search-tabs'),
			$panel = $('.property-info-panel, .property-details-panel');

	// Hide all panel except the first panel
	$('.property-info-panel:not(:first)').add('.property-details-panel:not(:first)').hide();

	// Add class active for first tab nav
	$tabs.find('li:first-child a').addClass('active');

	$tabs.find('a').click(function(e){
		e.preventDefault();
		var $this = $(this),
				target = $this.attr('href');

		// Add 'active' class
		$this.parent('li').siblings().children().removeClass('active');
		$this.addClass('active');

		// Change Panel
		$(target).siblings('div').hide().end().fadeIn().trigger('tabsshow');
	});

})();


/* -------------------------------------------------------------------
	Infinite Loading
------------------------------------------------------------------- */

$('.post-grid').infinitescroll({
	navSelector: '.navigation',
	nextSelector: '.navigation a:first',
	itemSelector: '.post-grid .entry-post',
	loadmoreButton: '.post-loader a',
	behavior:      'manual_trigger'
},
// trigger Masonry as a callback
function( newElements, opts, url ) {
	var $newElems = $(newElements).css({ opacity: 0 });
	// Ensure image is loaded before
	$newElems.imagesLoaded(function(){
		$newElems.animate({ opacity: 1 });
		$('.post-grid').masonry( 'appended', $newElems, true );
		$(opts.loadmoreButton).fadeTo('fast', 1);
	});
});

if( !$('.navigation').length ) {
	$('.post-loader').hide();
}


/* -------------------------------------------------------------------
	Fancybox modal on Single Property page
------------------------------------------------------------------- */
if( typeof $.fn.fancybox !== 'undefined' ) {
	$('.property-author-info a[href="#contactagent"], .property-bookmark a[href="#emailthis"]').fancybox();

	$('.popup-modal-inner').submit(function(e){
		e.preventDefault();
		var hasError = false,
				$form = $(this);
		$form.find('.error').remove();

		// Check required field
		$form.find('.requiredField').each(function() {
			var $this = $(this);

			// Check if field is empty
			if( $.trim( $this.val() ) == '') {
				var labelText = $this.prev('label').text();
				$this.parent().append('<span class="error">You forgot to enter your '+labelText+'.</span>');
				$this.addClass('inputError');
				hasError = true;

			// Check if field is email
			} else if( $this.hasClass('email') ) {
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				if( !emailReg.test( $.trim( $this.val() )) ) {
					var labelText = $this.prev('label').text();
					$this.parent().append('<br/><label></label><span class="error">You entered invalid '+labelText+'.</span>');
					$this.addClass('inputError');
					hasError = true;
				}
			}

		});

		// If form validation success
		if( !hasError ) {
			$.ajax({
				type: 'POST',
				url: $form.attr('action'),
				data: $form.serialize(),
				beforeSend: function() {
					$form.find('.buttons .button').fadeTo(300, 0.2);
				},
				success: function(data) {
					$form.before('<p class="alert alert-success"><strong>Thanks!</strong> Your email was successfully sent.</p>');
					$form.find('.buttons .button').fadeTo(300, 1);
				}
			});
		}

	});
}

$('#top-slide-menu').mmenu({
	isMenu: true,
  zposition: 'front',
  panelNodeType: "nav, div, ul, ol"
});


/* Payment Methods Radio Button
------------------------------------------------------------------- */
$('.payment_methods :radio').on('click', function(){
  var $radio = $(this),
      $list = $radio.parent('li'),
      $desc = $radio.siblings('.payment_box');

  $desc.slideDown();
  $list.siblings('li').find('.payment_box').slideUp();
});


/* When User choose Property Status as Rent, show the rent periode
------------------------------------------------------------------- */
$('#property_status').on('change', function(e){
  checkPropertyStatusVal( this );
});

function checkPropertyStatusVal( el ) {
  if( el != null ) {
    var value = el.value,
        $rentPeriode = $('#property_price_periode').closest('.form-builder-input');

    if( formbuilder_string.rent_term_id == value ) {
      $rentPeriode.show();
    } else {
      $rentPeriode.hide();
    }
  }
}
checkPropertyStatusVal( document.getElementById('property_status') );


$(document).ready(function(){
	// Tab Module
	tabModule.init();
});

})(jQuery);
