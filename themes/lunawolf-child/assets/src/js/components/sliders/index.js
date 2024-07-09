import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, Thumbs, EffectFade } from 'swiper/modules';

export default () => {
  (() => {
    const sliders = new Swiper('.js-testimonialSlider', {
      modules: [Navigation, Pagination],
      slidesPerView: 1,
      breakpoints: {
        768: {
          slidesPerView: 2
        },

        992: {
          slidesPerView: 3
        },
      },
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
        dynamicBullets: true,
        dynamicMainBullets: 4,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
    })
  })();

  (() => {
    const logoSlider = new Swiper('.js-logoSlider', {
      modules: [Pagination, Autoplay],
      slidesPerView: 'auto',
      loop: true,
      spaceBetween: 35,
      autoplay: {
        delay: 3000,
      },
      breakpoints: {
        0: {
          slidesPerView: 1,
        },
        576: {
          slidesPerView: 'auto',
        }
      },

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

  (() => {
    const fiveSliders = new Swiper('.js-videoSlider6', {
      modules: [Navigation, Pagination],
      slidesPerView: 6,
      // loop: true,
      spaceBetween: 20,
      breakpoints: {  
        0: {
          slidesPerView: 'auto'
        },

        576: {
          slidesPerView: 2
        },

        768: {
          slidesPerView: 3
        },

        992: {
          slidesPerView: 4
        },

        1280: {
          slidesPerView: 5
        },

        1366: {
          slidesPerView: 6
        }
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
    });

    const oneSlider = new Swiper('.js-videoSlider1', {
      modules: [Navigation, Pagination, Thumbs, EffectFade],
      slidesPerView: 1,
      loop: true,
      spaceBetween: 0,  
      effect: 'fade',
      fadeEffect: {
        crossFade: true
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
      thumbs: {
        swiper: fiveSliders,
        slideThumbActiveClass: 'item-active'
      },
      on: {
        slideChange: function () {
          handleVideoPlayPause(this);
        },
      }
      
    });

    function handleVideoPlayPause(swiperInstance) {
      var allSlides = swiperInstance.slides;
      // var activeIndex = swiperInstance.activeIndex;

      allSlides.forEach((slide, index) => {
          var vidyardPlayer = slide.querySelector('.vidyard-player-embed');
          if (vidyardPlayer && window.VidyardV4 != undefined) {
              var uuid = vidyardPlayer.getAttribute('data-uuid');
                VidyardV4.api.getPlayersByUUID(uuid).forEach(function(player) {
                  if(player) {
                    player.pause();
                  }
                });
          }

          var video = slide.querySelector('video');
          if(video) {
            video.pause();
          }
      });
  }

    
  })();

  (() => {
    const sliders = new Swiper('.js-videoSlider3', {
      modules: [Navigation, Pagination],
      slidesPerView: 1,
      loop: true,
      spaceBetween: 20,
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
  
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
        dynamicBullets: true,
        dynamicMainBullets: 4,
      },
    })
  })();

  (() => {
    const sliders = new Swiper('.js-videoSlider4', {
      modules: [Navigation, Pagination],
      slidesPerView: 1,
      loop: true,
      spaceBetween: 20,
      breakpoints: {
        768: {
          slidesPerView: 2
        },
  
        992: {
          slidesPerView: 3
        },

        1280: {
          slidesPerView: 4
        }
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
    })
  })();
}

