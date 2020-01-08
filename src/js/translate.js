import _ from 'lodash'

export default function (value, currentLocale, defaultLocale) {
    if (!_.isObject(value)) {
        return {
            text: value,
            locale: currentLocale,
        }
    }

    if (value[currentLocale]) {
        return {
            text: value[currentLocale],
            locale: currentLocale,
        }
    }

    if (value[defaultLocale]) {
        return {
            text: value[defaultLocale],
            locale: defaultLocale,
        }
    }

    if (!Object.keys(value).length) {
        return { text: '', locale: null, translated: false }
    }

    let firstAvailableLocale = Object.keys(value)[0]
    return {
        text: value[firstAvailableLocale] || '',
        locale: firstAvailableLocale,
        translated: false,
    }
}

export const isTranslatableByDefault = (fieldType) => {
    switch (fieldType) {
    case 'string':
    case 'text':
    case 'markdown':
    case 'json':
        return true
    default:
        return false
    }
}

export const shouldFieldBeTranslated = (fieldSchema) => {
    if (typeof fieldSchema.translatable !== 'undefined') {
        return fieldSchema.translatable
    }

    return isTranslatableByDefault(fieldSchema.type)
}
