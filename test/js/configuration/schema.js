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

    it('gets correct "false" default for boolean field', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'boolean',
                default: false,
            }],
        }])

        expect(schema.get('test')).toBe(false)
    })

    it('gets correct "null" default for boolean field', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'boolean',
                default: null,
            }],
        }])

        expect(schema.get('test')).toBeNull()
    })

    it('gets "false" for undefined boolean field without default', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'boolean',
            }],
        }])

        expect(schema.get('test')).toBe(false)
    })

    it('gets "false" for null boolean field without default', () => {
        let schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'boolean',
                }],
            }],
            {
                test: null,
            }
        )

        expect(schema.get('test')).toBe(false)
    })

    it('gets 0 for undefined decimal field without default', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'decimal',
            }],
        }])

        expect(schema.get('test')).toBe(0)
    })

    it('gets 0 for undefined integer field without default', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'integer',
            }],
        }])

        expect(schema.get('test')).toBe(0)
    })

    it('gets 0 for undefined float field without default', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'float',
            }],
        }])

        expect(schema.get('test')).toBe(0)
    })

    it('gets 0 for undefined number field without default', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'number',
            }],
        }])

        expect(schema.get('test')).toBe(0)
    })

    it('gets empty string for undefined string field without default', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'string',
            }],
        }])

        expect(schema.get('test')).toBe('')
    })

    it('gets empty string for undefined text field without default', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'text',
            }],
        }])

        expect(schema.get('test')).toBe('')
    })

    it('gets empty string for undefined markdown field without default', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'markdown',
            }],
        }])

        expect(schema.get('test')).toBe('')
    })

    it('gets "{}" for undefined json field without default', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'json',
            }],
        }])

        expect(schema.get('test')).toBe('{}')
    })

    it('gets empty array for undefined group field without default with min 0', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'group',
                min: 0,
            }],
        }])

        expect(schema.get('test')).toStrictEqual([])
    })

    it('gets null for undefined custom field without default', () => {
        let schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'custom',
            }],
        }])

        expect(schema.get('test')).toBeNull()
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

    it('uses default of groups and adds minimum number of entries afterwards', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'group',
                fields: [],
                min: 3,
                default: [
                    { foo: 1 },
                    { foo: 2 },
                ],
            }],
        }])

        expect(schema.get('test')).toEqual([{ foo: 1 }, { foo: 2 }, {}])
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

    it('replaces a null group element', () => {
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
                test: [null],
            }
        )

        expect(schema.get('test')).toEqual([
            {
                groupFirst: 'A default',
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

    it('claims to have missing required field values when product stream field is not required', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'stream',
                streamType: 'product',
            }],
        }])

        expect(schema.hasMissingRequiredFieldValues()).toBe(true)
    })

    it('claims to have missing required field values when product stream field is un-required', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'stream',
                streamType: 'product',
                required: false,
            }],
        }])

        expect(schema.hasMissingRequiredFieldValues()).toBe(false)
    })

    it('claims to have no missing required field values when custom stream field is not required', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'stream',
                streamType: 'custom-stream',
            }],
        }])

        expect(schema.hasMissingRequiredFieldValues()).toBe(false)
    })

    it('claims to have no missing required field values when skipping streams', () => {
        const schema = new Schema([{
            name: 'Section',
            fields: [{
                label: 'Test Field',
                field: 'test',
                type: 'stream',
                streamType: 'product',
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

    it('claims to have missing required field values inside a group with element null', () => {
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
            { test: [null] })

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

    it('returns the configuration when resolving streams', () => {
        const schema = new Schema(
            [
                {
                    name: 'Section',
                    fields: [{
                        label: 'Test Field',
                        field: 'test',
                        type: 'string',
                        default: 'Default Value',
                    }],
                },
            ],
            {
                test: 'My Value',
            }
        )

        expect(schema.getConfigurationWithResolvedStreams()).toEqual({
            test: 'My Value',
        })
    })

    it('returns the default value when resolving streams', () => {
        const schema = new Schema(
            [
                {
                    name: 'Section',
                    fields: [{
                        label: 'Test Field',
                        field: 'test',
                        type: 'string',
                        default: 'Default Value',
                    }],
                },
            ],
            {}
        )

        expect(schema.getConfigurationWithResolvedStreams()).toEqual({
            test: 'Default Value',
        })
    })

    it('returns null for missing streams when resolving streams', () => {
        const schema = new Schema(
            [
                {
                    name: 'Section',
                    fields: [{
                        label: 'Test Field',
                        field: 'test',
                        type: 'stream',
                    }],
                },
            ],
            {
                test: 'testStream',
            }
        )

        expect(schema.getConfigurationWithResolvedStreams()).toEqual({
            test: null,
        })
    })

    it('returns stream data when resolving streams', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'stream',
                }],
            }],
            {
                test: 'testStream',
            }
        )

        expect(schema.getConfigurationWithResolvedStreams(
            {
                testStream: ['foo', 'bar'],
            }
        )).toEqual({
            test: ['foo', 'bar'],
        })
    })

    it('returns custom stream data when resolving streams', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Field',
                    field: 'test',
                    type: 'stream',
                }],
            }],
            {
                test: 'This value will be ignored',
            }
        )

        expect(schema.getConfigurationWithResolvedStreams(
            {},
            {
                test: ['foo', 'bar'],
            }
        )).toEqual({
            test: ['foo', 'bar'],
        })
    })

    it('returns stream data inside groups when resolving streams', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Group',
                    field: 'test',
                    type: 'group',
                    fields: [
                        {
                            label: 'First',
                            field: 'groupFirst',
                            type: 'stream',
                        },
                    ],
                }],
            }],
            {
                test: [
                    { groupFirst: 'testStream' },
                    { groupFirst: 'otherStream' },
                ],
            }
        )

        expect(schema.getConfigurationWithResolvedStreams(
            {
                testStream: ['foo', 'bar'],
                otherStream: ['some', 'values'],
            }
        )).toEqual({
            test: [
                { groupFirst: ['foo', 'bar'] },
                { groupFirst: ['some', 'values'] },
            ],
        })
    })

    it('returns custom stream data inside groups when resolving streams', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [{
                    label: 'Test Group',
                    field: 'test',
                    type: 'group',
                    fields: [
                        {
                            label: 'First',
                            field: 'groupFirst',
                            type: 'stream',
                        },
                    ],
                }],
            }],
            {
                test: [
                    { groupFirst: 'This value will be ignored' },
                    { groupFirst: 'This will be ignored as well' },
                ],
            }
        )

        expect(schema.getConfigurationWithResolvedStreams(
            {},
            {
                test: [
                    { groupFirst: ['foo', 'bar'] },
                    { groupFirst: ['some', 'values'] },
                ],
            }
        )).toEqual({
            test: [
                { groupFirst: ['foo', 'bar'] },
                { groupFirst: ['some', 'values'] },
            ],
        })
    })

    it('returns the configuration for a field in a group with a name of an array function and with empty custom stream data which is exported as array by PHP when resolving streams', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [
                    {
                        label: 'Test Group',
                        field: 'test',
                        type: 'group',
                        fields: [
                            {
                                label: 'First',
                                field: 'filter',
                                type: 'string',
                                translatable: false,
                                required: true,
                            },
                        ],
                    },
                ],
            }],
            {
                test: [
                    { filter: 'Value' },
                ],
            }
        )

        expect(schema.getConfigurationWithResolvedStreams(
            {},
            {
                test: [
                    [],
                ],

            }
        )).toEqual({
            test: [
                { filter: 'Value' },
            ],
        })
    })
})
