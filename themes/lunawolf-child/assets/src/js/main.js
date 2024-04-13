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