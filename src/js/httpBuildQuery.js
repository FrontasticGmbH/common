let urlencode = function (str) {
    str = (str + '')
        .toString()

    return encodeURIComponent(str)
        .replace(/!/g, '%21')
        .replace(/'/g, '%27')
        .replace(/\(/g, '%28')
        .replace(/\)/g, '%29')
        .replace(/\*/g, '%2A')
        .replace(/%20/g, '+')
}

let httpBuildQuery = function (formdata, numericPrefix, argSeparator) {
    let value
    let key
    let tmp = []

    let httpBuildQueryHelper = function (key, val, argSeparator) {
        let k
        let tmp = []
        if (val === true) {
            val = '1'
        } else if (val === false) {
            val = '0'
        }
        if (val != null) {
            if (typeof val === 'object') {
                for (k in val) {
                    if (val[k] != null) {
                        tmp.push(httpBuildQueryHelper(key + '[' + k + ']', val[k], argSeparator))
                    }
                }
                return tmp.join(argSeparator)
            } else if (typeof val !== 'function') {
                return urlencode(key) + '=' + urlencode(val)
            } else {
                throw new Error('There was an error processing for httpBuildQuery().')
            }
        } else {
            return ''
        }
    }

    if (!argSeparator) {
        argSeparator = '&'
    }
    for (key in formdata) {
        value = formdata[key]
        if (numericPrefix && !isNaN(key)) {
            key = String(numericPrefix) + key
        }
        let query = httpBuildQueryHelper(key, value, argSeparator)
        if (query !== '') {
            tmp.push(query)
        }
    }

    return tmp.join(argSeparator)
}

export default httpBuildQuery
