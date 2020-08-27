import translate, { shouldFieldBeTranslated } from '../../src/js/translate'

describe('shouldFieldBeTranslated', () => {
    it('returns true if "translatable" is set to true', () => {
        const fieldSchema = {
            type: 'string',
            translatable: true,
        }

        expect(shouldFieldBeTranslated(fieldSchema)).toBe(true)
    })

    it('returns false if "translatable" is set to false', () => {
        const fieldSchema = {
            type: 'text',
            translatable: false,
        }

        expect(shouldFieldBeTranslated(fieldSchema)).toBe(false)
    })

    it('returns true if the field is of type "string" and "translatable" is not defined (as it defaults to true)', () => {
        const fieldSchema = {
            type: 'string',
        }

        expect(shouldFieldBeTranslated(fieldSchema)).toBe(true)
    })

    it('returns true if the field is of type "text" and "translatable" is not defined (as it defaults to true)', () => {
        const fieldSchema = {
            type: 'text',
        }

        expect(shouldFieldBeTranslated(fieldSchema)).toBe(true)
    })

    it('returns true if the field is of type "json" and "translatable" is not defined (as it defaults to true)', () => {
        const fieldSchema = {
            type: 'json',
        }

        expect(shouldFieldBeTranslated(fieldSchema)).toBe(true)
    })

    it('returns true if the field is of type "markdown" and "translatable" is not defined (as it defaults to true)', () => {
        const fieldSchema = {
            type: 'markdown',
        }

        expect(shouldFieldBeTranslated(fieldSchema)).toBe(true)
    })

    it('returns true if "translatable" is set to true, but we are on a number field', () => {
        const fieldSchema = {
            type: 'number',
            translatable: true,
        }

        expect(shouldFieldBeTranslated(fieldSchema)).toBe(true)
    })

    it('returns false if "translatable" is NOT set and we are on a number field', () => {
        const fieldSchema = {
            type: 'number',
        }

        expect(shouldFieldBeTranslated(fieldSchema)).toBe(false)
    })
})

describe('translate', () => {
    test.each([
        ['string'],
        [23],
        [null],
        [undefined],
    ])('returns input literal, when passed', (input) => {
        expect(translate(input, 'de_DE', 'en_GB')).toEqual({ text: input, locale: 'de_DE' })
    })

    it('returns current locale if available', () => {
        expect(translate(
            { de_DE: 'Hallo Welt', en_GB: 'Hello World!' },
            'de_DE',
            'en_GB',
        )).toEqual({ text: 'Hallo Welt', locale: 'de_DE' })
    })

    it('returns default locale if current not available', () => {
        expect(translate(
            { de_DE: 'Hallo Welt', en_GB: 'Hello World!' },
            'it_IT',
            'en_GB',
        )).toEqual({ text: 'Hello World!', locale: 'en_GB' })
    })

    it('first value, if no translation availble', () => {
        expect(translate(
            { de_DE: 'Hallo Welt' },
            'it_IT',
            'en_GB',
        )).toEqual({ text: 'Hallo Welt', locale: 'de_DE', translated: false })
    })

    test.each([
        [{}],
        [[]],
    ])('empty text on broken input object', (input) => {
        expect(translate( input, 'it_IT', 'en_GB')).toEqual({ text: '', locale: null, translated: false })
    })
})
