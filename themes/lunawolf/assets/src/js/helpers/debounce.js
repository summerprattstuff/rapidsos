export default (func, wait, immediate) => {
    let timeout;
    let waitTime = wait || 100;
    return function () {
        let context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            timeout = null;
            if (!immediate) {
                func.apply(context, args);
            }
        }, waitTime);
        if (immediate && !timeout) {
            func.apply(context, args);
        }
    };
}