/* global spyOn */

import Schema from '../../../src/js/configuration/schema.js'

import fs from 'fs'
import path from 'path'

const loadRegressionExamples = () => {
    const fixtureBase = path.join(__dirname, '..', '..', '_fixture', 'configuration')
    return fs.readdirSync(
        fixtureBase
    ).map((directory) => {
        return {
            exampleName: directory,
            inputFixture: (JSON.parse(fs.readFileSync(
                path.join(fixtureBase, directory, 'input_fixture.json')).toString()
            )),
            outputExpectation: (JSON.parse(fs.readFileSync(
                path.join(fixtureBase, directory, 'output_expectation.json')).toString()
            )),
        }
    })
}

describe.each(loadRegressionExamples())("A schema", ({ exampleName, inputFixture, outputExpectation }) => {
    beforeEach(function () {
        spyOn(console, 'warn')
    })

    it(exampleName, () => {
        const schema = new Schema(inputFixture.schema, inputFixture.configuration || {})

        outputExpectation.forEach((expectation) => {
            expect(schema.get(expectation.key)).toStrictEqual(expectation.value)

            if (expectation.warning) {
                expect(console.warn).toHaveBeenCalled()
            } else {
                expect(console.warn).not.toHaveBeenCalled()
            }
        })
    })
})

describe('ConfigurationSchema', function () {
    beforeEach(function () {
        spyOn(console, 'warn')
    })

    it('throws an error on setting an undefined option', () => {
        let schema = new Schema()

        expect(function () { schema.set('undefined') }).toThrow(
            new Error('Unknown field undefined in this configuration schema.')
        )
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

    it('return empty string for missing value inside group inside group when resolving streams', () => {
        const schema = new Schema(
            [{
                name: 'Section',
                fields: [
                    {
                        label: 'Outer Test Group',
                        field: 'outerGroup',
                        type: 'group',
                        fields: [
                            {
                                label: 'Inner Test Group',
                                field: 'innerGroup',
                                type: 'group',
                                fields: [
                                    {
                                        label: 'First',
                                        field: 'groupFirst',
                                        type: 'string',
                                    },
                                ],
                            },
                        ],
                    },
                ],
            }],
            {}
        )

        expect(schema.getConfigurationWithResolvedStreams({}, {})).toEqual({
            outerGroup: [
                {
                    innerGroup: [
                        { groupFirst: '' },
                    ],
                },
            ],
        })
    })
})
