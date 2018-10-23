import _ from 'lodash'

let VisibilityChange = function () {
    // Set the name of the hidden property and the change event for visibility
    let hidden
    let visibilityChange
    let callbacks = {}

    this.registerCallBack = function (hidden, active) {
        let id = null

        do {
            id = Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1)
        } while (id in callbacks)

        callbacks[id] = {
            hidden: hidden,
            active: active,
        }

        return id
    }

    this.removeCallBack = function (index) {
        delete callbacks[index]
    }

    if (typeof document === 'undefined') {
        hidden = false
        visibilityChange = false
    } else if (typeof document.hidden !== 'undefined') { // Opera 12.10 and Firefox 18 and later support
        hidden = 'hidden'
        visibilityChange = 'visibilitychange'
    } else if (typeof document.mozHidden !== 'undefined') {
        hidden = 'mozHidden'
        visibilityChange = 'mozvisibilitychange'
    } else if (typeof document.msHidden !== 'undefined') {
        hidden = 'msHidden'
        visibilityChange = 'msvisibilitychange'
    } else if (typeof document.webkitHidden !== 'undefined') {
        hidden = 'webkitHidden'
        visibilityChange = 'webkitvisibilitychange'
    }

    if (hidden && visibilityChange) {
        let handleVisibilityChange = function () {
            _.map(callbacks, (callback, id) => {
                if (document[hidden]) {
                    callback.hidden()
                } else {
                    callback.active()
                }
            })
        }

        if (typeof document.addEventListener === 'undefined' ||
            typeof document[hidden] === 'undefined') {
            console.warn(
                'This feature requires a browser, such as Google Chrome or ' +
                'Firefox, that supports the Page Visibility API.'
            )
        } else {
            document.addEventListener(visibilityChange, handleVisibilityChange, false)
        }
    }
}

export default VisibilityChange
