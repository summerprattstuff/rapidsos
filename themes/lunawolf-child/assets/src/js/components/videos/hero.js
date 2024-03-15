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
}