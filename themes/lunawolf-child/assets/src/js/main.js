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

let vh = window.innerHeight * 0.01;

const adaptVh = () => {
  vh = window.innerHeight * 0.01;
  document.documentElement.style.setProperty('--vh', `${vh}px`);
}

document.addEventListener("DOMContentLoaded", function(event) {
  adaptVh();
  window.addEventListener('resize', debounce(adaptVh));

  videos();
  sliders();
  activeTrigger();
  fancybox();
});

// jQuery('.m__pitem__wrapper .m__pitem__box').magnificPopup({
//   delegate: '.m__pitem__wrapper a',
//   closeOnContentClick: false,
//   mainClass: 'mfp-with-zoom mfp-img-mobile',
// });


// jQuery.magnificPopup.open({
//   delegate: '.m__pitem__wrapper a',
//   items: {
//     src: '.m__pitem__wrapper .m__pitem__box'
//   },
//   type: 'inline',
//   closeOnContentClick: false,
//   mainClass: 'mfp-with-zoom mfp-img-mobile',
// });

// jQuery('.m__pitem__wrapper a').magnificPopup({
//   items: {
//     src: this.closest('.m__pitem__wrapper')
//   },
//   type: 'inline',
//   closeOnContentClick: false,
//   mainClass: 'mfp-with-zoom mfp-img-mobile',
//   zoom: {
//     enabled: true,
//     duration: 300, // don't foget to change the duration also in CSS,
//   }
// });

// jQuery('.m__pitem__wrapper a').on('click', function(e) {
//   e.preventDefault();
//   var $this = jQuery(this);

//   // try {
//   $this.magnificPopup({
//       items: {
//         src: $this.closest('.m__pitem__wrapper').find('.m__pitem__box')
//       },
//       type: 'inline',
//       closeOnContentClick: false,
//       mainClass: 'mfp-with-zoom mfp-img-mobile',
//       zoom: {
//         enabled: true,
//         duration: 300, // don't foget to change the duration also in CSS,
//       }
//     });
//   // } catch(e) {
//   //   console.log(e);
//   // }
// })

jQuery( '.m__pitem__wrapper' ).each( function () {
  var $this = jQuery(this);
  $this.find('a').magnificPopup( {
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
  } );
} )

jQuery('.nav-tabs .nav-link').on( 'click', function ( e ) {
  var $link = jQuery( this );

  // if tab is loading, return
  if ( $link.closest( '.nav-tabs' ).hasClass( 'loading' ) ) {
      return;
  }

  // get href
  var href = 'SPAN' == this.tagName ? $link.data( 'href' ) : $link.attr( 'href' );

  // get panel
  var $panel;
  if ( '#' == href ) {
      $panel = $link.closest( '.nav' ).siblings( '.tab-content' ).children( '.tab-pane' ).eq( $link.parent().index() );
  } else {
      $panel = jQuery( ( '#' == href.substring( 0, 1 ) ? '' : '#' ) + href );
  }
  if ( !$panel.length ) {
      return;
  }

  e.preventDefault();

  var $activePanel = $panel.parent().children( '.active' );


  if ( $link.hasClass( "active" ) || !href ) {
      return;
  }
  // change active link
  $link.parent().parent().find( '.active' ).removeClass( 'active' );
  $link.addClass( 'active' );

  // change tab instantly
  _changeTab();

  // Change tab panel
  function _changeTab() {
      // themeAdmin.slider($panel.find('.swiper-wrapper'));
      $activePanel.removeClass( 'in active' );
      $panel.addClass( 'active in' );
  }
} )

function call( fn, delay ) {
  fn();
}

function appear( el, fn, intObsOptions ) {

  var $this = jQuery( el );

  if ( $this.data( 'observer-init' ) ) {
    return;
  }

  var interSectionObserverOptions = {
    rootMargin: '0px 0px 100px 0px',
    threshold: 0,
    alwaysObserve: false
  };

  if ( intObsOptions && Object.keys( intObsOptions ).length ) {
    interSectionObserverOptions = $.extend( interSectionObserverOptions, intObsOptions );
  }

  var observer = new IntersectionObserver( ( function( entries ) {
    for ( var i = 0; i < entries.length; i++ ) {
      var entry = entries[i];

      if ( entry.intersectionRatio > 0 ) {
        if ( typeof fn === 'string' ) {
          var func = Function( 'return ' + functionName )();
        } else {
          var callback = fn;
          callback.call( entry.target );
          // observer.disconnect();
        }
        // Unobserve
        if ( this.alwaysObserve == false ) {
          observer.unobserve( entry.target );
        }
      }
    }
  } ).bind( interSectionObserverOptions ), interSectionObserverOptions );

  observer.observe( el );

  $this.data( 'observer-init', true );

  return this;
}

function parseOptions( options ) {
  return 'string' == typeof options ? JSON.parse( options.replace( /'/g, '"' ).replace( ';', '' ) ) : {};
}

jQuery('.appear-animate').each( function() {
  var el = this;
  appear( el, function() {
    if ( el.classList.contains( 'appear-animate' ) && !el.classList.contains( 'appear-animation-visible' ) ) {
      var settings = parseOptions( el.getAttribute( 'data-settings' ) ),
        duration = 1000;

      if ( el.classList.contains( 'animated-slow' ) ) {
        duration = 2000;
      } else if ( el.classList.contains( 'animated-fast' ) ) {
        duration = 750;
      }

      call( function() {
        el.style['animation-duration'] = duration + 'ms';
        el.style['animation-delay'] = settings._animation_delay + 'ms';
        el.style['transition-property'] = 'visibility, opacity';
        el.style['transition-duration'] = '0s';
        el.style['transition-delay'] = settings._animation_delay + 'ms';

        var animation_name = settings.animation || settings._animation || settings._animation_name;
        animation_name && el.classList.add( animation_name );

        el.classList.add( 'appear-animation-visible' );
        setTimeout(
          function() {
            el.style['transition-property'] = '';
            el.style['transition-duration'] = '';
            el.style['transition-delay'] = '';

            el.classList.add( 'animating' );

            setTimeout( function() {
              el.classList.add( 'animated-done' );
            }, duration );
          },
          settings._animation_delay ? settings._animation_delay + 500 : 500
        );
      } );
    }
  } );
} );

// theme.countTo = function( selector, runAsSoon = false ) {
  if ( jQuery.fn.countTo ) {
    jQuery( '.count-to' ).each( function() {
      var el = this;
      var $this = jQuery( this );
      function runProgress() {
        setTimeout( function() {
          var options = {
            onComplete: function() {
              $this.addClass( 'complete' );
            }
          };
          $this.data( 'duration' ) && ( options.speed = $this.data( 'duration' ) );
          $this.data( 'from-value' ) && ( options.from = $this.data( 'from-value' ) );
          $this.data( 'to-value' ) && ( options.to = $this.data( 'to-value' ) );

          options.decimals = options.to && typeof options.to === 'string' && options.to.indexOf( '.' ) >= 0 ? ( options.to.length - options.to.indexOf( '.' ) - 1 ) : 0;
          $this.countTo( options );
        }, 300 );
      }
      // runAsSoon ? runProgress() : appear( el, runProgress );
      appear( el, runProgress );
    } );
  }
// }

jQuery('.header-top .btn-close').on( 'click', function( e ) {
  e.preventDefault();
  var $this = jQuery(this);
  $this.closest( '.header-top' ).fadeOut( function() {
    jQuery(this).closest('.site').removeClass('--notification');
    jQuery(this).remove();
  } );
  
} );
