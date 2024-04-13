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
      loop: true,
      spaceBetween: 63,

      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },

      on: {
        lock: function (e) {
          e.el.classList.add('locked');
        },
        unlock: function (e) {
          e.el.classList.remove('locked');
        },
      }
    })
  })();

  (() => {
    const imgSlider = new Swiper('.js-imgSlider', {
      modules: [Navigation, Pagination],
      loop: true,
      spaceBetween: 20,
      slidesPerView: 1,
      breakpoints: {
        576: {
          slidesPerView: 2
        },

        992: {
          slidesPerView: 3
        },

        1200: {
          slidesPerView: 4
        },
      },

      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },

      pagination: {
        el: '.swiper-pagination',
        clickable: true,
        dynamicBullets: true,
        dynamicMainBullets: 4,
      },

      on: {
        lock: function (e) {
          e.el.classList.add('locked');
        },
        unlock: function (e) {
          e.el.classList.remove('locked');
        },
      }
    })
  })();
}