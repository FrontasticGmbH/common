import Page, { DEFAULT_PAGE_NAME } from '../../../src/js/domain/page.js'
import Region from '../../../src/js/domain/region.js'
import Element from '../../../src/js/domain/cell.js'
import Kit from '../../../src/js/domain/kit'
import Tastic from '../../../src/js/domain/tastic'

let mockId = 'id'
/* eslint-disable arrow-body-style */
jest.mock(
    '../../../src/js/generateId',
    () => jest.fn(() => mockId)
)
/* eslint-enable arrow-body-style */

describe('Page', function () {
    it('creates region automatically', () => {
        let page = new Page({}, ['region'])

        expect(page.getRegion('region')).toEqual(new Region({ regionId: 'region' }))
    })

    it('maps tastic configuration correctly', () => {
        let page = new Page(
            {
                regions: {
                    someRegion: {
                        elements: [
                            {
                                cellId: 'someCell',
                                tastics: [
                                    {
                                        tasticId: '123abc',
                                        tasticType: 'text',
                                    },
                                ],
                            },
                        ],
                    },
                },
            },
            ['someRegion'],
            [
                {
                    configurationSchema: {
                        tasticType: 'text',
                        schema: [
                            {
                                name: 'Content',
                                fields: [{
                                    field: 'text',
                                    type: 'text',
                                }],
                            },
                        ],
                    },
                },
            ]
        )

        expect(page.getTastic('123abc').schema.fields.text.type).toEqual('text')
    })

    it('creates region from existing data', () => {
        let page = new Page({
            regions: {
                region: {
                    configuration: {
                        mobile: false,
                    },
                },
            },
        }, ['region'])

        expect(page.getRegion('region')).toEqual(new Region({
            regionId: 'region',
            configuration: { mobile: false },
        }))
    })

    it('throws error on unknown region', () => {
        let page = new Page({}, ['region'])

        expect(function () {
            page.getRegion('unknown')
        }).toThrow(
            new Error('Region with identifier unknown unknown.')
        )
    })

    it('omits invalid region', () => {
        let page = new Page({
            regions: {
                invalid: {
                    configuration: {
                        mobile: false,
                    },
                },
            },
        }, ['region'])

        expect(function () {
            page.getRegion('invalid')
        }).toThrow(
            new Error('Region with identifier invalid unknown.')
        )
    })

    it('adds cell to empty page correctly', () => {
        let page = new Page({}, ['region'])
        page.addCell('region', { size: 6 })

        expect(page.getRegion('region')).toEqual(new Region({
            regionId: 'region',
            elements: [new Element({ configuration: { size: 6 } })],
        }))
    })

    it('adds kit to empty page correctly', () => {
        let page = new Page({}, ['region'])
        page.addKit('region', { kitDefinitionId: 'abc-23' })

        expect(page.getRegion('region')).toEqual(new Region({
            regionId: 'region',
            elements: [new Kit({ kitDefinitionId: 'abc-23', kitId: 'id', configuration: {} })],
        }))
    })

    it('exports a simple page variant ready for storage', () => {
        let page = new Page({}, ['region'])
        let region = page.getRegion('region')
        let cell = page.addCell('region', { size: 6 })
        let kit = page.addKit('region', { kitDefinitionId: 'abc-23' })
        let tastic = page.addTastic(region.regionId, cell.cellId, 'text')

        region.schema = region.schema.set('mobile', false)
        cell.schema = cell.schema.set('tablet', false)
        tastic.schema = tastic.schema.set('desktop', false)
        kit.configuration.something = 'anything'

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                region: {
                    regionId: 'region',
                    configuration: {
                        mobile: false,
                    },
                    elements: [
                        {
                            cellId: 'id',
                            configuration: {
                                size: 6,
                                tablet: false,
                            },
                            customConfiguration: {},
                            tastics: [
                                {
                                    tasticId: 'id',
                                    tasticType: 'text',
                                    configuration: {
                                        desktop: false,
                                    },
                                },
                            ],
                        },
                        {
                            kitId: 'id',
                            kitDefinitionId: 'abc-23',
                            configuration: {
                                something: 'anything',
                            },
                        },
                    ],
                },
            },
        })
    })

    it('moves a cell to a clean region', () => {
        let page = new Page({}, ['a', 'b'])
        page.addCell('a').cellId = 'a'

        page.moveElement({ cellId: 'a' }, { region: 'b' })

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                a: {
                    regionId: 'a',
                    configuration: {},
                    elements: [],
                },
                b: {
                    regionId: 'b',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                    ],
                },
            },
        })
    })

    it('throws an error on undefined source region', () => {
        let page = new Page({}, ['a', 'b'])
        page.addCell('a').cellId = 'a'

        expect(function () {
            page.moveElement({ cellId: 'undefined' }, { region: 'b' })
        }).toThrow(
            new Error('Could not find element with ' + JSON.stringify({ cellId: 'undefined' }))
        )
    })

    it('throws an error on undefined target region', () => {
        let page = new Page({}, ['a', 'b'])
        page.addCell('a').cellId = 'a'

        expect(function () {
            page.moveElement({ cellId: 'a' }, { region: 'undefined' })
        }).toThrow(
            new Error('Unknown target region undefined')
        )
    })

    it('moves middle cell to a clean region', () => {
        let page = new Page({}, ['a', 'b'])
        page.addCell('a').cellId = 'a'
        page.addCell('a').cellId = 'b'
        page.addCell('a').cellId = 'c'

        page.moveElement({ cellId: 'b' }, { region: 'b' })

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                a: {
                    regionId: 'a',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                        {
                            cellId: 'c',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                    ],
                },
                b: {
                    regionId: 'b',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'b',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                    ],
                },
            },
        })
    })

    it('moves cell to the middle of a region', () => {
        let page = new Page({}, ['a', 'b'])
        page.addCell('a').cellId = 'b'
        page.addCell('b').cellId = 'a'
        page.addCell('b').cellId = 'c'

        page.moveElement({ cellId: 'b' }, { region: 'b', element: 1 })

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                a: {
                    regionId: 'a',
                    configuration: {},
                    elements: [],
                },
                b: {
                    regionId: 'b',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                        {
                            cellId: 'b',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                        {
                            cellId: 'c',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                    ],
                },
            },
        })
    })

    it('moves cell inside of a region', () => {
        let page = new Page({}, ['a'])
        page.addCell('a').cellId = 'b'
        page.addCell('a').cellId = 'a'
        page.addCell('a').cellId = 'c'

        page.moveElement({ cellId: 'b' }, { region: 'a', element: 2 })

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                a: {
                    regionId: 'a',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                        {
                            cellId: 'b',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                        {
                            cellId: 'c',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                    ],
                },
            },
        })
    })

    it('moves cell inside of a region to the beginning', () => {
        let page = new Page({}, ['a'])
        page.addCell('a').cellId = 'b'
        page.addCell('a').cellId = 'a'
        page.addCell('a').cellId = 'c'

        page.moveElement({ cellId: 'a' }, { region: 'a', element: 0 })

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                a: {
                    regionId: 'a',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                        {
                            cellId: 'b',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                        {
                            cellId: 'c',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                    ],
                },
            },
        })
    })

    it('moves cell to the end of region', () => {
        let page = new Page({}, ['a', 'b'])
        page.addCell('a').cellId = 'b'
        page.addCell('b').cellId = 'a'
        page.addCell('b').cellId = 'c'

        page.moveElement({ cellId: 'b' }, { region: 'b' })

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                a: {
                    regionId: 'a',
                    configuration: {},
                    elements: [],
                },
                b: {
                    regionId: 'b',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                        {
                            cellId: 'c',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                        {
                            cellId: 'b',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                    ],
                },
            },
        })
    })

    it('moves cell to the beginning of region', () => {
        let page = new Page({}, ['a', 'b'])
        page.addCell('a').cellId = 'b'
        page.addCell('b').cellId = 'a'
        page.addCell('b').cellId = 'c'

        page.moveElement({ cellId: 'b' }, { region: 'b', element: 0 })

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                a: {
                    regionId: 'a',
                    configuration: {},
                    elements: [],
                },
                b: {
                    regionId: 'b',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'b',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                        {
                            cellId: 'a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                        {
                            cellId: 'c',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                    ],
                },
            },
        })
    })

    it('moves a tastic to a clean cell', () => {
        let page = new Page({}, ['a', 'b'])
        page.addCell('a').cellId = 'a'
        page.addTastic('a', 'a', 'type').tasticId = 'a'
        page.addCell('b').cellId = 'b'

        page.moveTastic('a', { cell: 'b' })

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                a: {
                    regionId: 'a',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                    ],
                },
                b: {
                    regionId: 'b',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'b',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [
                                {
                                    tasticId: 'a',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                            ],
                        },
                    ],
                },
            },
        })
    })

    it('throws an error on undefined source tastic', () => {
        let page = new Page({}, ['a', 'b'])
        page.addCell('a').cellId = 'a'
        page.addTastic('a', 'a', 'a').tasticId = 'a'
        page.addCell('b').cellId = 'b'

        expect(function () {
            page.moveTastic('undefined', { region: 'b' })
        }).toThrow(
            new Error('Could not find tastic with id undefined')
        )
    })

    it('throws an error on undefined target region', () => {
        let page = new Page({}, ['a', 'b'])
        page.addCell('a').cellId = 'a'
        page.addTastic('a', 'a', 'a').tasticId = 'a'

        expect(function () {
            page.moveTastic('a', { cell: 'undefined' })
        }).toThrow(
            new Error('Could not find element with ' + JSON.stringify({ cellId: 'undefined' }))
        )
    })

    it('moves a tastic to the middle of a cell', () => {
        let page = new Page({}, ['regA', 'regB'])
        page.addCell('regA').cellId = 'cell-a'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'b'
        page.addCell('regB').cellId = 'cell-b'
        page.addTastic('regB', 'cell-b', 'type').tasticId = 'a'
        page.addTastic('regB', 'cell-b', 'type').tasticId = 'c'

        page.moveTastic('b', { cell: 'cell-b', tasticDropPosition: 1 })

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                regA: {
                    regionId: 'regA',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'cell-a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                    ],
                },
                regB: {
                    regionId: 'regB',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'cell-b',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [
                                {
                                    tasticId: 'c',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'b',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'a',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                            ],
                        },
                    ],
                },
            },
        })
    })

    it('moves a tastic inside of a cell', () => {
        let page = new Page({}, ['regA'])
        page.addCell('regA').cellId = 'cell-a'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'b'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'a'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'c'

        page.moveTastic('c', { cell: 'cell-a', tasticDropPosition: 2 })

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                regA: {
                    regionId: 'regA',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'cell-a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [
                                {
                                    tasticId: 'a',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'c',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'b',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                            ],
                        },
                    ],
                },
            },
        })
    })

    it('moves a tastic inside of a cell to the beginning', () => {
        let page = new Page({}, ['regA'])
        page.addCell('regA').cellId = 'cell-a'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'b'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'a'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'c'

        page.moveTastic('a', { cell: 'cell-a', tasticDropPosition: 0 })

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                regA: {
                    regionId: 'regA',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'cell-a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [
                                {
                                    tasticId: 'a',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'c',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'b',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                            ],
                        },
                    ],
                },
            },
        })
    })

    it('moves a tastic to the end of a cell', () => {
        let page = new Page({}, ['regA', 'regB'])
        page.addCell('regA').cellId = 'cell-a'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'b'
        page.addCell('regB').cellId = 'cell-b'
        page.addTastic('regB', 'cell-b', 'type').tasticId = 'a'
        page.addTastic('regB', 'cell-b', 'type').tasticId = 'c'

        page.moveTastic('b', { cell: 'cell-b' })

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                regA: {
                    regionId: 'regA',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'cell-a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                    ],
                },
                regB: {
                    regionId: 'regB',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'cell-b',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [
                                {
                                    tasticId: 'c',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'a',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'b',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                            ],
                        },
                    ],
                },
            },
        })
    })

    it('moves a tastic to the beginning of a cell', () => {
        let page = new Page({}, ['regA', 'regB'])
        page.addCell('regA').cellId = 'cell-a'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'b'
        page.addCell('regB').cellId = 'cell-b'
        page.addTastic('regB', 'cell-b', 'type').tasticId = 'a'
        page.addTastic('regB', 'cell-b', 'type').tasticId = 'c'

        page.moveTastic('b', { cell: 'cell-b', tasticDropPosition: 0 })

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                regA: {
                    regionId: 'regA',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'cell-a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [],
                        },
                    ],
                },
                regB: {
                    regionId: 'regB',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'cell-b',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [
                                {
                                    tasticId: 'b',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'c',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'a',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                            ],
                        },
                    ],
                },
            },
        })
    })

    it('adds a tastic inaa cell when position is not defined', () => {
        let page = new Page({}, ['regA'])
        page.addCell('regA').cellId = 'cell-a'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'a'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'b'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'c'

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                regA: {
                    regionId: 'regA',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'cell-a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [
                                {
                                    tasticId: 'c',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'b',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'a',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                            ],
                        },
                    ],
                },
            },
        })
    })

    it('adds a tastic in a cell on a specific position', () => {
        let page = new Page({}, ['regA'])
        page.addCell('regA').cellId = 'cell-a'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'b'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'a'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'c'
        page.addTastic('regA', 'cell-a', 'type').tasticId = 'd'

        page.addTastic('regA', 'cell-a', 'type', 2).tasticId = 'e'

        expect(page.export()).toEqual({
            layoutId: 'three_rows',
            nodes: [],
            pageId: null,
            name: DEFAULT_PAGE_NAME,
            regions: {
                regA: {
                    regionId: 'regA',
                    configuration: {},
                    elements: [
                        {
                            cellId: 'cell-a',
                            configuration: {},
                            customConfiguration: {},
                            tastics: [
                                {
                                    tasticId: 'd',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'c',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'e',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'a',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                                {
                                    tasticId: 'b',
                                    tasticType: 'type',
                                    configuration: {},
                                },
                            ],
                        },
                    ],
                },
            },
        })
    })

    it('getTastics returns tastics from multiple regions', () => {
        const page = new Page({}, ['regA', 'regB'])
        page.addCell('regA').cellId = 'cell-a'
        page.addCell('regA').cellId = 'cell-b'
        page.addCell('regB').cellId = 'cell-c'
        mockId = 'b'
        page.addTastic('regA', 'cell-a', 'type')
        mockId = 'a'
        page.addTastic('regA', 'cell-a', 'type')
        mockId = 'c'
        page.addTastic('regA', 'cell-b', 'type')
        mockId = 'd'
        page.addTastic('regB', 'cell-c', 'type')

        expect(page.getTastics()).toEqual([
            new Tastic({ tasticId: 'a', tasticType: 'type' }),
            new Tastic({ tasticId: 'b', tasticType: 'type' }),
            new Tastic({ tasticId: 'c', tasticType: 'type' }),
            new Tastic({ tasticId: 'd', tasticType: 'type' }),
        ])
    })
})
