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
      const player = new Plyr(video, {
        // controls: true,
        // ratio: '16:9'
      });
    })
  })();

  (() => {
    let bgVideos = document.querySelectorAll('.js-bgVideo');
console.log('test');
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