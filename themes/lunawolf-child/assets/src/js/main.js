// Import scripts
// import {Fancybox} from '@fancyapps/ui'; // eslint-disable-line
// import './vendor/slick.min';
// Styles
import '../sass/main.scss';

// Import asset images
// import './images';

// Import javascript
import debounce from './helpers/debounce';

// import siteFocus from './partials/siteFocus';
import sliders from './components/sliders';
import fancybox from './components/fancybox';
import videos from './components/videos';
import activeTrigger from "./components/utilities/activeTrigger";

// GSAP Integration
// import { gsap } from 'gsap';
// import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Rellax from 'rellax';

// gsap.registerPlugin(ScrollTrigger);

var rellax = new Rellax('.rellax');

let vh = window.innerHeight * 0.01;

const adaptVh = () => {
  vh = window.innerHeight * 0.01;
  document.documentElement.style.setProperty('--vh', `${vh}px`);
}

// const updateScrollTrigger = (self, top) => {
//   const scrollY = self.scroll(),
//     s = top >= 132 ? top - 132 : 0;

//   // console.log("s:", s)
//   // console.log("scrollY: ", scrollY)

//   if (scrollY < s + 100) {
//     console.log(1);
//     gsap.to('p.m__featured__video__label', { opacity: 1, height: 'auto' });
//     gsap.to(['h2.m__featured__video__title', 'h3.m__featured__video__subtitle', 'div.m__featured__video__btn', 'div.m__featured__video__wrapper'], { opacity: 0, height: 'auto' });
//   } else if (scrollY < s + 200) {
//     console.log(2);
//     gsap.to('p.m__featured__video__label', { opacity: 1, height: 'auto' });
//     gsap.to(['h2.m__featured__video__title', 'h3.m__featured__video__subtitle', 'div.m__featured__video__btn'], { opacity: 1, height: 'auto', marginTop: '30px' });
//     gsap.to('div.m__featured__video__wrapper', { opacity: 0, height: 'auto' });
//   } else if (scrollY < s + 300) {
//     console.log(3);
//     gsap.to(['p.m__featured__video__label', 'h3.m__featured__video__subtitle'], { opacity: 0, height: 0, margin: 0 });
//     gsap.to(['h2.m__featured__video__title', 'div.m__featured__video__btn'], { opacity: 1, height: 'auto', marginTop: '30px' });
//     gsap.to('div.m__featured__video__wrapper', { opacity: 0, height: 'auto' });
//   } else if (scrollY < s + 400) {
//     console.log(4);
//     gsap.to('div.m__featured__video__wrapper', { opacity: 1, height: 'auto' });
//     gsap.to(['h2.m__featured__video__title', 'div.m__featured__video__btn'], { opacity: 1, height: 'auto', marginTop: '30px' });
//   } else {
//     console.log(5);
//     gsap.to('div.m__featured__video__wrapper', { opacity: 1, height: 'auto' });
//     gsap.to(['p.m__featured__video__label', 'h2.m__featured__video__title', 'h3.m__featured__video__subtitle', 'div.m__featured__video__btn'], { opacity: 0, height: 0, margin: 0 });
//   }
// };

document.addEventListener("DOMContentLoaded", function (event) {
  adaptVh();
  window.addEventListener('resize', debounce(adaptVh));

  videos();
  sliders();
  activeTrigger();
  fancybox();
  // // GSAP ScrollTrigger configuration
  // ScrollTrigger.create({
  //   start: top >= 132 ? top - 132 : 0,
  //   end: top + 668,

  //   onUpdate: (self) => updateScrollTrigger(self, top),
  //   onRefresh: (self) => updateScrollTrigger(self, top)
  // });

  var $wrapper = document.querySelector('.m__featured__video__inner__wrapper');
  if (!$wrapper) return;
  
  let h = $wrapper.offsetHeight;
      // top = parseInt($wrapper.getBoundingClientRect().top) + window.scrollY;

  document.querySelector('.m__featured__video').style.height = `${ h * 1.2 }px`;


});

jQuery(document).ready(function () {
  jQuery('.m__pitem__wrapper').each(function () {
    var $this = jQuery(this);
    $this.find('a').magnificPopup({
      items: {
        src: $this.find('.m__pitem__box')
      },
      type: 'inline',
      closeOnContentClick: false,
      mainClass: 'mfp-with-zoom mfp-img-mobile',
      zoom: {
        enabled: true,
        duration: 300, // don't foget to change the duration also in CSS,
      }
    });
  })

  jQuery('.btn-popup-wrapper').each(function () {
    var $this = jQuery(this);
    $this.find('.btn-popup-link').magnificPopup({
      items: {
        src: $this.find('.btn-popup-content')
      },
      type: 'inline',
      closeOnContentClick: false,
      mainClass: 'mfp-with-zoom mfp-img-mobile',
      zoom: {
        enabled: true,
        duration: 300, // don't foget to change the duration also in CSS,
      }
    });
  })

  /**
	 * Set cookie
	 * 
	 * @since 1.0
	 * @param {string} name Cookie name
	 * @param {string} value Cookie value
	 * @param {number} exdays Expire period
	 * @return {void}
	 */
	 function setCookie( name, value, exdays ) {
		var date = new Date();
		date.setTime( date.getTime() + ( exdays * 24 * 60 * 60 * 1000 ) );
		document.cookie = name + "=" + value + ";expires=" + date.toUTCString() + ";path=/";
	}

  /**
	 * Get cookie
	 *
	 * @since 1.0
	 * @param {string} name Cookie name
	 * @return {string} Cookie value
	 */
	 function getCookie( name ) {
		var n = name + "=";
		var ca = document.cookie.split( ';' );
		for ( var i = 0; i < ca.length; ++i ) {
			var c = ca[i];
			while ( c.charAt( 0 ) == ' ' ) {
				c = c.substring( 1 );
			}
			if ( c.indexOf( n ) == 0 ) {
				return c.substring( n.length, c.length );
			}
		}
		return "";
	}

  const popupPresets = {
    firstpopup: {
			type: 'inline',
			mainClass: 'mfp-popup-template mfp-newsletter-popup mfp-flip-popup',
			closeOnContentClick: false,
      zoom: {
        enabled: true,
        duration: 300, // don't foget to change the duration also in CSS,
      },
      callbacks: {
				beforeClose: function() {
					// if "do not show" is checked
					setCookie( 'hideNewsletterPopup', true, 7 );
				}
			}
		},
  }

  const popupVar = {
		// fixedContentPos: true,
		// removalDelay: 350,
		// callbacks: {
		// 	beforeOpen: function() {
		// 		if ( this.fixedContentPos ) {
		// 			var scrollBarWidth = window.innerWidth - document.body.clientWidth;
		// 			jQuery( '.sticky-content.fixed' ).css( 'padding-right', scrollBarWidth );
		// 			jQuery( '.mfp-wrap' ).css( 'overflow', 'hidden auto' );
		// 		}
		// 	},
		// 	close: function() {
		// 		if ( this.fixedContentPos ) {
		// 			jQuery( '.mfp-wrap' ).css( 'overflow', '' );
		// 			jQuery( '.sticky-content.fixed' ).css( 'padding-right', '' );
		// 		}
		// 	}
		// },
	}

    /**
	 * Open magnific popup
	 *
	 * @since 1.0
	 * @param {Object} options
	 * @param {string} preset
	 * @return {void}
	 */
	 function popup( options, preset ) {
		var mpInstance = jQuery.magnificPopup.instance;
		// if something is already opened, retry after 5seconds
		if ( mpInstance.isOpen ) {
			if ( mpInstance.content ) {
				setTimeout( function() {
					popup( options, preset );
				}, 5000 );
			} else {
				jQuery.magnificPopup.close();
			}
		} else {
			// if nothing is opened, open new
			jQuery.magnificPopup.open(
				jQuery.extend( true, {},
					popupVar,
					preset ? popupPresets[preset] : {},
					options
				)
			);
		}
	}

  function parseOptions( options ) {
		return 'string' == typeof options ? JSON.parse( options.replace( /'/g, '"' ).replace( ';', '' ) ) : {};
	}

  /**
		 * Open first popup
		 * 
		 * @since 1.0
		 */
  function openFirstPopup( $this ) {
    // var options = parseOptions( $this.attr( 'data-popup-options' ) );
    setTimeout( function() {
      if ( getCookie( 'hideNewsletterPopup' ) ) {
        return;
      }
      popup( {
        // mainClass: 'mfp-fade mfp-alpha mfp-alpha-' + options.popup_id,
        mainClass: 'mfp-with-zoom mfp-img-mobile',
        items: {
          src: $this.get( 0 )
        },
        
      }, 'firstpopup' );
      // $this.magnificPopup({
      //   items: {
      //     src: $this.get(0)
      //   },
      //   type: 'inline',
      //   closeOnContentClick: false,
      //   mainClass: 'mfp-with-zoom mfp-img-mobile',
      //   // zoom: {
      //   //   enabled: true,
      //   //   duration: 300, // don't foget to change the duration also in CSS,
      //   // }
      // });
    }, 7000 );
  }

  // Open first popup
  jQuery( 'body > .newsletter' ).each( function( e ) {
    var $this = jQuery( this );
    // if ( $this.attr( 'data-popup-options' ) ) {
      openFirstPopup( $this );
    // }
  } );

  setTimeout(function() {
    jQuery('body > .newsletter form').on('submit', function(e) {
    // e.preventDefault();
    var $this = jQuery(this);
    $this.closest('.newsletter').addClass('-submit');
    // $this.submit();
  })}, 1000);

  jQuery('.nav-tabs .nav-link').on('click', function (e) {
    var $link = jQuery(this);

    // if tab is loading, return
    if ($link.closest('.nav-tabs').hasClass('loading')) {
      return;
    }

    // get href
    var href = 'SPAN' == this.tagName ? $link.data('href') : $link.attr('href');

    // get panel
    var $panel;
    if ('#' == href) {
      $panel = $link.closest('.nav').siblings('.tab-content').children('.tab-pane').eq($link.parent().index());
    } else {
      $panel = jQuery(('#' == href.substring(0, 1) ? '' : '#') + href);
    }
    if (!$panel.length) {
      return;
    }

    e.preventDefault();

    var $activePanel = $panel.parent().children('.active');


    if ($link.hasClass("active") || !href) {
      return;
    }
    // change active link
    $link.parent().parent().find('.active').removeClass('active');
    $link.addClass('active');

    // change tab instantly
    _changeTab();

    // Change tab panel
    function _changeTab() {
      // themeAdmin.slider($panel.find('.swiper-wrapper'));
      $activePanel.removeClass('in active');
      $panel.addClass('active in');
    }
  })

  function call(fn, delay) {
    fn();
  }

  function appear(el, fn, intObsOptions) {

    var $this = jQuery(el);

    if ($this.data('observer-init')) {
      return;
    }

    var interSectionObserverOptions = {
      rootMargin: '0px 0px 200px 0px',
      threshold: [0, 0.25, 0.5, 0.75, 1],
      alwaysObserve: false
    };

    if (intObsOptions && Object.keys(intObsOptions).length) {
      interSectionObserverOptions = jQuery.extend(interSectionObserverOptions, intObsOptions);
    }

    var observer = new IntersectionObserver((function (entries) {
      for (var i = 0; i < entries.length; i++) {
        var entry = entries[i];

        if (entry.intersectionRatio > 0) {
          if (typeof fn === 'string') {
            var func = Function('return ' + functionName)();
          } else {
            var callback = fn;
            callback.call(entry.target);
            // observer.disconnect();
          }
          // Unobserve
          if (this.alwaysObserve == false) {
            observer.unobserve(entry.target);
          }
        }
      }
    }).bind(interSectionObserverOptions), interSectionObserverOptions);

    observer.observe(el);

    $this.data('observer-init', true);

    return this;
  }

  function parseOptions(options) {
    return 'string' == typeof options ? JSON.parse(options.replace(/'/g, '"').replace(';', '')) : {};
  }

  jQuery('.appear-animate').each(function () {
    var el = this;
    appear(el, function () {
      if (el.classList.contains('appear-animate') && !el.classList.contains('appear-animation-visible')) {
        var settings = parseOptions(el.getAttribute('data-settings')),
          duration = 1000;

        if (el.classList.contains('animated-slow')) {
          duration = 2000;
        } else if (el.classList.contains('animated-fast')) {
          duration = 750;
        }

        call(function () {
          el.style['animation-duration'] = duration + 'ms';
          el.style['animation-delay'] = settings._animation_delay + 'ms';
          el.style['transition-property'] = 'visibility, opacity';
          el.style['transition-duration'] = '0s';
          el.style['transition-delay'] = settings._animation_delay + 'ms';

          var animation_name = settings.animation || settings._animation || settings._animation_name;
          animation_name && el.classList.add(animation_name);

          el.classList.add('appear-animation-visible');
          setTimeout(
            function () {
              el.style['transition-property'] = '';
              el.style['transition-duration'] = '';
              el.style['transition-delay'] = '';

              el.classList.add('animating');

              setTimeout(function () {
                el.classList.add('animated-done');
              }, duration);
            },
            settings._animation_delay ? settings._animation_delay + 500 : 500
          );
        });
      }
    });
  });

  jQuery('.count-to').each(function () {
    var el = this;
    var $this = jQuery(this),
      $to = parseInt($this.data('to')),
      duration = parseInt($this.data('duration'));

    function runProgress() {
      animateValue(el, 0, $to, duration);
    }

    function animateValue(obj, start, end, duration) {
      let startTimestamp = null;
      const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        obj.innerHTML = Math.floor(progress * (end - start) + start);
        if (progress < 1) {
          window.requestAnimationFrame(step);
        }
      };
      window.requestAnimationFrame(step);
    }
    // runAsSoon ? runProgress() : appear( el, runProgress );
    appear(el, runProgress);
  });

  // theme.countTo = function( selector, runAsSoon = false ) {
  // if ( jQuery.fn.countTo ) {
  //   jQuery( '.count-to' ).each( function() {
  //     var el = this;
  //     var $this = jQuery( this );
  //     function runProgress() {
  //       setTimeout( function() {
  //         var options = {
  //           onComplete: function() {
  //             $this.addClass( 'complete' );
  //           }
  //         };
  //         $this.data( 'duration' ) && ( options.speed = $this.data( 'duration' ) );
  //         $this.data( 'from-value' ) && ( options.from = $this.data( 'from-value' ) );
  //         $this.data( 'to-value' ) && ( options.to = $this.data( 'to-value' ) );

  //         options.decimals = options.to && typeof options.to === 'string' && options.to.indexOf( '.' ) >= 0 ? ( options.to.length - options.to.indexOf( '.' ) - 1 ) : 0;
  //         $this.countTo( options );
  //       }, 300 );
  //     }
  //     // runAsSoon ? runProgress() : appear( el, runProgress );
  //     appear( el, runProgress );
  //   } );
  // }
  // }

  jQuery('.header-top .btn-close').on('click', function (e) {
    e.preventDefault();
    var $this = jQuery(this);
    $this.closest('.header-top').fadeOut(function () {
      jQuery(this).closest('.site').removeClass('--notification');
      jQuery(this).remove();
    });

  });

  function addUrlParam(href, name, value) {
    var url = document.createElement('a'), s, r;
    href = decodeURIComponent(decodeURI(href));
    url.href = href;
    s = url.search;
    if (0 <= s.indexOf(name + '=')) {
      r = s.replace(new RegExp(name + '=[^&]*'), name + '=' + value);
    } else {
      r = (s.length && 0 <= s.indexOf('?')) ? s : '?';
      r.endsWith('?') || (r += '&');
      r += name + '=' + value;
    }
    return encodeURI(href.replace(s, '') + r.replace(/&+/, '&'));
  }

  /**
               * Load more ajax content 
               * 
               * @since 1.0
               * @param {object} params
               * @return {boolean}
               */
  function loadmore(params) {
    if (!params.wrapper ||
      1 != params.wrapper.length) {
      return false;
    }

    // Get wrapper
    var $wrapper = params.wrapper;

    // Get options
    var options = JSON.parse($wrapper.attr('data-load')),
      originalOptions = options;
    options.args = options.args || {};
    if (!options.args.paged) {
      options.args.paged = 1;

      // Get correct page number at first in archive pages
      if ($wrapper.closest('.product-archive').length) {
        var match = location.pathname.match(/\/page\/(\d*)/);
        if (match && match[1]) {
          options.args.paged = parseInt(match[1]);
        }
      }
    }

    if ('filter' == params.type) {
      options.args.paged = 1;
      if (params.category) {
        options.args.category = params.category; // filter category
      } else if (options.args.category) {
        delete options.args.category; // do not filter category
      }
    } else if ('+1' === params.page) {
      ++options.args.paged;
    } else if ('-1' === params.page) {
      --options.args.paged;
    } else {
      options.args.paged = parseInt(params.page);
    }

    // Get ajax url
    var url = wp_params.ajaxurl;
    if ($wrapper.closest('.product-archive').length) { // shop or blog archive
      if (!$wrapper.hasClass('products') && 'filter' == params.type && params.url) {
        url = addUrlParam(params.url, 'only_posts', 1);
      } else {
        var pathname = location.pathname,
          page_uri = '/page/' + options.args.paged;
        if (pathname.endsWith('/')) {
          pathname = pathname.slice(0, pathname.length - 1);
        }
        if (pathname.indexOf('/page/') >= 0) {
          pathname = pathname.replace(/\/page\/\d*/, page_uri);
        } else {
          pathname += page_uri;
        }
        url = addUrlParam(location.origin + pathname + location.search, 'only_posts', 1);
      }
    }

    // Get ajax data
    var data = {
      action: 'loadmore',
      nonce: wp_params.nonce,
      props: options.props,
      args: options.args,
      loadmore: params.type,
      cpt: options.cpt ? options.cpt : 'post',
      taxonomy: options.taxonomy ? options.taxonomy : '',
      terms: options.terms ? options.terms : [],
    }

    // Before start loading
    params.onStart && params.onStart($wrapper, options);

    // Do ajax
    jQuery.ajax({
      type: 'POST',
      url: wp_params.ajaxurl,
      data: data,
      success: function (res) {
        if (res) {
          $wrapper.append(res.data.html);
          originalOptions.args.paged = options.args.paged;
          originalOptions.cpt = options.cpt;

          $wrapper.attr('data-load', JSON.stringify(originalOptions));

          if (res.data.end) {
            $wrapper.closest('.m__resourceModule').find('.btn-load').css('display', 'none');
          }
        }
      }
    })


    return true;
  }

  jQuery('.m__resourceModule .btn-load').on('click', function (e) {
    e.preventDefault();
    var $btn = jQuery(this);

    loadmore({
      wrapper: $btn.closest('.product-archive').find('.archive_wrapper'),
      page: '+1',
      type: 'button',
      onStart: function () {
        $btn.data('text', $btn.html())
          .addClass('loading').blur();
        // .html( alpha_vars.texts.loading );
      },
      onFail: function () {
        // $btn.text( alpha_vars.texts.loadmore_error ).addClass( 'disabled' );
      }
    });
  });

  jQuery('.site__navigation ul.menu:not(.-submenu) > li').on('click', function(e) {
    // e.preventDefault();
    if(window.innerWidth < 1281) {
      var $this = jQuery(this),
        $dropdown = $this.find('>.menu');

        $dropdown.slideToggle();
    }
  })

  window.addEventListener('scroll', function () {
    jQuery('.site__header').each(function () {
      var $this = jQuery(this),
        height = $this.innerHeight(),
        enableTran = $this.hasClass('-megatype'),
        topHeight = $this.find('.header-top').length ? ($this.find('.header-top').innerHeight()) : 0, 
        $middle_wrapper = $this.find('.header-middle-wrapper'),
        wrapHeight = $middle_wrapper.innerHeight(),
        $middle = $this.find('.header-middle'),
        // $temp = $this.parent().find('.header-temp'),
        // $content = $this.parent().find('.site__content'),
        scrollY = parseFloat(jQuery(window).scrollTop());
        // offsetY = parseFloat($label.offset().top);
      
      if (scrollY > topHeight) {
        // $content.css('paddingTop', wrapHeight + 'px');
        $middle.css('height', wrapHeight + 'px');
        if(enableTran) {
          $middle_wrapper.hasClass('-mega') && $middle_wrapper.removeClass('-mega');
          !$middle_wrapper.hasClass('fade') && $middle_wrapper.addClass('fade');
        }
        !$middle_wrapper.hasClass('fixed') && $middle_wrapper.addClass('fixed');
        // $middle_wrapper.slideDown();
      } else {
        // $content.css('paddingTop', '0px');
        $middle_wrapper.hasClass('fixed') && $middle_wrapper.removeClass('fixed');
        if(enableTran) {
          !$middle_wrapper.hasClass('fade') && $middle_wrapper.removeClass('fade');
          !$middle_wrapper.hasClass('-mega') && $middle_wrapper.addClass('-mega');
        }
        
        // $temp.css('height', scrollY + 'px');
        $middle.removeAttr('style');
        // $this.slideUp();
      }
    })
  })

  function handleWindowResize() {
    var $submenus = jQuery('.menu.-submegamenu');
    $submenus.each(function() {
      var $this = jQuery(this);
      $this.css('margin-left', '0px');

      var left = $this.offset().left,
          right = parseFloat(left) + parseFloat($this.outerWidth()) - parseFloat(window.innerWidth);
          
          console.log('Right: ', right);

          if(left < 0) {
            $this.css('margin-left', left * -1 + 'px');
          }

          if(right > 0) {
            $this.css('margin-left', right * -1 + 'px');
          }
      
    })
  }

  window.addEventListener('resize', handleWindowResize);
  jQuery('.megamenu>.menu-item').on('mouseenter', handleWindowResize);
  
})


