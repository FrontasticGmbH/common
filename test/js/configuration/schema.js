/* global spyOn */

import Schema from '../../../src/js/configuration/schema.js'

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

    it('claims to have defined field', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'string',
                default: '42',

            }],
        }])

        expect(schema.has('test')).toBe(true)
    })

    it('does not claim to have undefined field', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'string',
                default: '42',

            }],
        }])

        expect(schema.has('testaboo')).toBe(false)
    })

    it('completes defaults in group values', () => {
        let schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'group',
                    fields: [
                        {
                            label: 'First',
                            field: 'groupFirst',
                            type: 'string',
                            default: 'A default',
                        },
                    ],
                }],
            }],
            {
                test: [
                    {},
                    {
                        groupFirst: 'Not a default',
                    },
                ],
            }
        )

        expect(schema.get('test')).toEqual([
            {
                groupFirst: 'A default',
            },
            {
                groupFirst: 'Not a default',
            },
        ])
    })
})
