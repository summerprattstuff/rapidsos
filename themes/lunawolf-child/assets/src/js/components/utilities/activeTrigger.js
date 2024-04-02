export default () => {
  let activeTriggers = document.querySelectorAll('.js-activeTrigger');
  let ACTIVE_CLASS = '-active';

  activeTriggers.forEach(trigger => {
    trigger.addEventListener('click', e => {
      e.preventDefault();

      let dataTarget = trigger.dataset.target;
      let dataClass  = trigger.dataset.class;

      if (dataTarget) {
        let targetElement = document.querySelector(dataTarget);
        targetElement.classList.toggle(dataClass ? dataClass : ACTIVE_CLASS);
      }
      trigger.classList.toggle(ACTIVE_CLASS);
    })
  })
}