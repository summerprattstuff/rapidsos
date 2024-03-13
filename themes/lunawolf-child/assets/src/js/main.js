// Import scripts
// import {Fancybox} from '@fancyapps/ui'; // eslint-disable-line
// import './vendor/slick.min';
import AOS from 'aos';
// Styles
import '../sass/main.scss';

// Import asset images
// import './images';

// Import javascript
// import debounce from './helpers/debounce';

// import siteFocus from './partials/siteFocus';
import sliders from './components/sliders';
import animations from './components/animations';

document.addEventListener("DOMContentLoaded", function(event) {
	sliders();
	animations();
	AOS.init({
		once: true,
		duration: 600
	});
});