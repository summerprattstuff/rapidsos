import Plyr from 'plyr';

export default () => {
  (() => {
    let heroVideos = document.querySelectorAll('.js-heroVideo');

    heroVideos.forEach(video => {
      const player = new Plyr(video, {
        controls: false,
        muted: true,
        autoplay: true,
        ratio: '16:9'
      });

      player.on('ready', () => {
        player.play();
      })
    })
  })();

  (() => {
    let cardVideos = document.querySelectorAll('.js-cardVideo');

    cardVideos.forEach(video => {
      let hasAutoplay = video.classList.contains('js-cardVideoAutoplay');

      console.log('hasAutoplay', hasAutoplay);

      const player = new Plyr(video, {
        controls: !hasAutoplay ? [
          'play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'
        ] : false,
        autoplay: hasAutoplay,
        muted: hasAutoplay
      });

      player.on('ready', () => {
        if (hasAutoplay) {
          player.play();
        }
      })

      if (hasAutoplay) {
        document.addEventListener('click', () => {
          if (!player.playing) {
            player.play();
          }
        }, { once: true });
        window.addEventListener('scroll', () => {
          if (!player.playing) {
            player.play();
          }
        }, { once: true });
      }
    })
  })();

  (() => {
    let bgVideos = document.querySelectorAll('.js-bgVideo');

    bgVideos.forEach(video => {
      const player = new Plyr(video, {
        controls: false,
        muted: true,
        autoplay: true,
      });

      player.on('ready', () => {
        player.play();
      })
    })
  })();
}