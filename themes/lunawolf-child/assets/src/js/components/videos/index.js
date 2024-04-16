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
    let cardVideosColumn = document.querySelectorAll('.js-cardVideoColumn');

    cardVideosColumn.forEach(video => {
      const dataVideo = video.dataset.video;
      const videoTrigger = document.querySelector(`.js-videoColumnTrigger[data-video-trigger="${dataVideo}"]`);

      const player = new Plyr(video, {
        controls: [
          'play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'
        ],
        autoplay: false,
        muted: false,
      });

      player.on('pause', () => {
        videoTrigger.textContent = 'Watch video';
      });

      player.on('play', () => {
        videoTrigger.textContent = 'Pause video';
      });

      videoTrigger.addEventListener('click', function (e) {
        e.preventDefault();

        player.togglePlay();
      });
    });
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