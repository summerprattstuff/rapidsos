export default () => {
	(() => {
		const body = document.body;
		const delay = window?.pangos?.animationDelay ? window.pangos.animationDelay : 0;
		
		setTimeout(() => {
			body.classList.add('-loaded');
		}, delay);
	})();
	
}