import { Fancybox } from '@fancyapps/ui'; // eslint-disable-line

export default () => {
  (() => {
    Fancybox.bind('[data-fancybox]', {

    });
  })();
  (() => {
    let popupTriggers = document.querySelectorAll('[href^="#popup="]');

    popupTriggers.forEach(popupTrigger => {
      popupTrigger.addEventListener('click', (e) => {
        e.preventDefault();

        let popupString = popupTrigger.getAttribute('href');
        let popupValue = popupString.split('=')[1];

        let popupElement = document.querySelector(`[data-popup="${popupValue}"]`);

        if (!popupElement) return;

        Fancybox.show([{
          src: popupElement,
          type: 'inline'
        }])
      });
    });
  })();
}