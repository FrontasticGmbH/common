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

    it('adds minimum number of entries to groups', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'group',
                fields: [],
                min: 3,
            }],
        }])

        expect(schema.get('test')).toEqual([{}, {}, {}])
    })

    it('adds one entry to groups without minimum', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'group',
                fields: [],
            }],
        }])

        expect(schema.get('test')).toEqual([{}])
    })

    it('removes entries past the maximum from groups', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'group',
                    max: 2,
                    fields: [{
                        label: 'First',
                        field: 'groupFirst',
                        type: 'string',
                    }],
                }],
            }],
            {
                test: [
                    { groupFirst: 'first' },
                    { groupFirst: 'second' },
                    { groupFirst: 'third' },
                    { groupFirst: 'fourth' },
                ],
            })

        expect(schema.get('test'))
            .toEqual([
                { groupFirst: 'first' },
                { groupFirst: 'second' }])
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

    it('claims to have missing required field values', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'string',
                required: true,
            }],
        }])

        expect(schema.hasMissingRequiredFieldValues()).toBe(true)
    })

    it('claims to have no missing required field values when value is given', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'string',
                    required: true,
                }],
            }],
            { test: 'Some Value' })

        expect(schema.hasMissingRequiredFieldValues()).toBe(false)
    })

    it('claims to have missing required field values when value is empty string', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'string',
                    required: true,
                }],
            }],
            { test: '' })

        expect(schema.hasMissingRequiredFieldValues()).toBe(true)
    })

    it('claims to have no missing required field values when value is boolean false', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'boolean',
                    required: true,
                }],
            }],
            { test: false })

        expect(schema.hasMissingRequiredFieldValues()).toBe(false)
    })

    it('claims to have missing required field values when value is null', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'string',
                    required: true,
                }],
            }],
            { test: null })

        expect(schema.hasMissingRequiredFieldValues()).toBe(true)
    })

    it('claims to have no missing required field values when default is set', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'string',
                required: true,
                default: '42',
            }],
        }])

        expect(schema.hasMissingRequiredFieldValues()).toBe(false)
    })

    it('claims to have missing required field values when default is null', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'string',
                required: true,
                default: null,
            }],
        }])

        expect(schema.hasMissingRequiredFieldValues()).toBe(true)
    })

    it('claims to have no missing required field values when field is not required', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'string',
            }],
        }])

        expect(schema.hasMissingRequiredFieldValues()).toBe(false)
    })

    it('claims to have missing required field values when stream field is not required', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'stream',
            }],
        }])

        expect(schema.hasMissingRequiredFieldValues()).toBe(true)
    })

    it('claims to have no missing required field values when skipping streams', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'stream',
                required: true,
            }],
        }])

        expect(schema.hasMissingRequiredFieldValues(true)).toBe(false)
    })

    it('claims to have missing required field values inside an undefined group', () => {
        const schema = new Schema([{
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
                        required: true,
                    },
                ],
            }],
        }])

        expect(schema.hasMissingRequiredFieldValues()).toBe(true)
    })

    it('claims to have missing required field values inside an empty group', () => {
        const schema = new Schema(
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
                            required: true,
                        },
                    ],
                }],
            }],
            { test: [] })

        expect(schema.hasMissingRequiredFieldValues()).toBe(true)
    })

    it('claims to have missing required field values inside a group', () => {
        const schema = new Schema(
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
                            required: true,
                        },
                    ],
                }],
            }],
            { test: [{ groupFirst: '42' }, {}] })

        expect(schema.hasMissingRequiredFieldValues()).toBe(true)
    })

    it('claims to have no missing required field values inside a group with present value', () => {
        const schema = new Schema(
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
                            required: true,
                        },
                    ],
                }],
            }],
            { test: [{ groupFirst: '42' }, { groupFirst: '23' }] })

        expect(schema.hasMissingRequiredFieldValues()).toBe(false)
    })

    it('claims to have missing required field values when reference is null', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'reference',
                    required: true,
                }],
            }],
            { test: null })

        expect(schema.hasMissingRequiredFieldValues()).toBe(true)
    })

    it('claims to have missing required field values when reference is empty object', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'reference',
                    required: true,
                }],
            }],
            { test: {} })

        expect(schema.hasMissingRequiredFieldValues()).toBe(true)
    })

    it('claims to have missing required field values when reference type is null', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'reference',
                    required: true,
                }],
            }],
            { test: { type: null } })

        expect(schema.hasMissingRequiredFieldValues()).toBe(true)
    })

    it('claims to have missing required field values when reference target is null', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'reference',
                    required: true,
                }],
            }],
            { test: { type: 'link', target: null } })

        expect(schema.hasMissingRequiredFieldValues()).toBe(true)
    })

    it('claims to have no missing required field values when reference type and target are set', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'reference',
                    required: true,
                }],
            }],
            { test: { type: 'link', target: 'https://frontastic.io/' } })

        expect(schema.hasMissingRequiredFieldValues()).toBe(false)
    })

    it('claims to have no missing required values in unknown section', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [],
        }])

        expect(schema.hasMissingRequiredFieldValuesInSection('Unknown Schema')).toBe(false)
    })

    it('claims to have no missing required values in empty section', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [],
        }])

        expect(schema.hasMissingRequiredFieldValuesInSection('Section')).toBe(false)
    })

    it('claims to have missing required values in section', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'string',
                required: true,
            }],
        }])

        expect(schema.hasMissingRequiredFieldValuesInSection('Section')).toBe(true)
    })

    it('claims to have missing required values in other section', () => {
        const schema = new Schema([
            {
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'string',
                    required: true,
                }],
            },
            {
                name: 'Other Section',
                fields: [],
            },
        ])

        expect(schema.hasMissingRequiredFieldValuesInSection('Other Section')).toBe(false)
    })

    it('merges 2 schemas', () => {
        const a = new Schema([
            {
                name: 'Section A1',
                fields: [{
                    label: 'A1',
                    field: 'a1',
                    type: 'string',
                }]
            },
            {
                name: 'Section A2',
                fields: [{
                    label: 'A2',
                    field: 'a2',
                    type: 'string',
                }]
            },
        ], {
            a1: 'A-1',
        })

        const b = new Schema([
            {
                name: 'Section B1',
                fields: [{
                    label: 'B1',
                    field: 'b1',
                    type: 'string',
                }]
            },
        ], {
            b1: 'B-1',
        })

        const expected = new Schema([
            {
                name: 'Section A1',
                fields: [{
                    label: 'A1',
                    field: 'a1',
                    type: 'string',
                }]
            },
            {
                name: 'Section A2',
                fields: [{
                    label: 'A2',
                    field: 'a2',
                    type: 'string',
                }]
            },
            {
                name: 'Section B1',
                fields: [{
                    label: 'B1',
                    field: 'b1',
                    type: 'string',
                }]
            },
        ], {
            a1: 'A-1',
            b1: 'B-1',
        })

        expect(Schema.merge(a, b)).toEqual(expected)
    })
})
