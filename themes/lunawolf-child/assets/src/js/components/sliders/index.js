import Splide from '@splidejs/splide';

export default () => {
	(() => {
		const splide = new Splide('.js-testimonialSplide', {
			arrows: false,
			pagination: false,
			autoHeight: true,
			// autoplay: true,
			// rewind: true
		}).mount();
	})();
	
}