// See https://stackoverflow.com/a/27078401
//
// This is a simplified version from the lodash source to just use this
// function and not the entirety of lodash:
export default (callback, limit) => {
    let waiting = false
    return function () {
        if (!waiting) {
            callback.apply(this, arguments)
            waiting = true
            setTimeout(function () {
                waiting = false
            }, limit)
        }
    }
}
