import { shouldFieldBeTranslated } from '../../src/js/translate'

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
