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

