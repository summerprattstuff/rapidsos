import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';
export default () => {
	(() => {
		const sliders = new Swiper('.js-testimonialSlider', {
			modules: [Navigation],
			slidesPerView: 1,
			breakpoints: {
				768: {
					slidesPerView: 2
				},

				992: {
					slidesPerView: 3
				},
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
		})
	})();

	(() => {
		const logoSlider = new Swiper('.js-logoSlider', {
			modules: [Pagination],
			slidesPerView: 'auto',
		})
	})();
}