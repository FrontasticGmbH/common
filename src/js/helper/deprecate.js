export default (message) => {
    if ((typeof PRODUCTION === 'undefined' || !PRODUCTION) && // eslint-disable-line no-undef
        (typeof window !== 'undefined') &&
        window &&
        window.document) {
        // eslint-disable-next-line no-console
        console.info('%c🗑 %cDeprecation Notice: %s', 'color: gray', 'color: orange', message)
    }
}
