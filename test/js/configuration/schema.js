/* global spyOn */

import Schema from '../../..//src/js/configuration/schema.js'

describe('ConfigurationSchema', function () {
    beforeEach(function () {
        spyOn(console, 'warn')
    })

    it('returns null on getting an undefined option', () => {
        let schema = new Schema()

        expect(schema.get('undefined')).toBe(null)
        expect(console.warn).toHaveBeenCalled()
    })

    it('returns value on getting an unknown option', () => {
        let schema = new Schema()
        schema.configuration['unknown'] = 42

        expect(schema.get('unknown')).toBe(42)
        expect(console.warn).toHaveBeenCalled()
    })

    it('throws an error on setting an undefined option', () => {
        let schema = new Schema()

        expect(function () { schema.set('undefined') }).toThrow(
            new Error('Unknown field undefined in this configuration schema.')
        )
    })

    it('gets default option for undefined existing field', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'string',
                default: '42',

            }],
        }])

        expect(schema.get('test')).toBe('42')
    })

    it('gets set option for defined field', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'string',
                default: '42',

            }],
        }])
        schema = schema.set('test', '23')

        expect(schema.get('test')).toBe('23')
    })
})
