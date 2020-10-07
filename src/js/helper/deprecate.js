export default (message, component = null) => {
    if ((typeof PRODUCTION === 'undefined' || !PRODUCTION) && // eslint-disable-line no-undef
        (typeof window !== 'undefined') &&
        window &&
        window.document) {
        // eslint-disable-next-line no-console
        console.info(
            '%c🗑 %cDeprecation Notice: %s %s',
            'color: gray',
            'color: orange',
            component ? ('[' + (component.displayName || component.constructor.name) + ']') : '',
            message
        )
    }
}
