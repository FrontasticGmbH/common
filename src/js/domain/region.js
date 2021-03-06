import ConfigurationSchema from '../configuration/schema'
import generateId from '../generateId'

import Cell from './cell'
import Kit from './kit'

class Region {
    constructor (region = {}) {
        this.regionId = region.regionId || generateId()
        this.configuration = region.configuration || {}
        this.schema = new ConfigurationSchema([
            {
                name: 'Responsive',
                folded: true,
                fields: [
                    {
                        label: 'Show on mobile',
                        field: 'mobile',
                        type: 'boolean',
                        default: true,
                    },
                    {
                        label: 'Show on tablet',
                        field: 'tablet',
                        type: 'boolean',
                        default: true,
                    },
                    {
                        label: 'Show on desktop',
                        field: 'desktop',
                        type: 'boolean',
                        default: true,
                    },
                ],
            },
            {
                name: 'Layout',
                fields: [
                    {
                        label: 'Cell direction',
                        field: 'flexDirection',
                        type: 'enum',
                        default: 'row',
                        values: [
                            {
                                value: 'row',
                                name: 'Row',
                            },
                            {
                                value: 'column',
                                name: 'Column',
                            },
                            {
                                value: 'row-reverse',
                                name: 'Row (reversed)',
                            },
                            {
                                value: 'column-reverse',
                                name: 'Column (reversed)',
                            },
                        ],
                    },
                    {
                        label: 'Cell wrapping',
                        field: 'flexWrap',
                        type: 'enum',
                        default: 'wrap',
                        values: [
                            {
                                value: 'nowrap',
                                name: 'No wrapping',
                            },
                            {
                                value: 'wrap',
                                name: 'Wrap cells',
                            },
                        ],
                    },
                    {
                        label: 'Justify cells',
                        field: 'justifyContent',
                        type: 'enum',
                        default: 'space-between',
                        values: [
                            {
                                value: 'flex-start',
                                name: 'Put at beginning',
                            },
                            {
                                value: 'flex-end',
                                name: 'Put at end',
                            },
                            {
                                value: 'center',
                                name: 'Center Cells',
                            },
                            {
                                value: 'space-between',
                                name: 'Space between cells',
                            },
                            {
                                value: 'space-around',
                                name: 'Space around cells',
                            },
                            {
                                value: 'space-even',
                                name: 'Evenly spaced cells',
                            },
                        ],
                    },
                    {
                        label: 'Cell alignment',
                        field: 'alignItems',
                        type: 'enum',
                        default: 'stretch',
                        values: [
                            {
                                value: 'flex-start',
                                name: 'Align to start',
                            },
                            {
                                value: 'flex-end',
                                name: 'Align to end',
                            },
                            {
                                value: 'center',
                                name: 'Center cells',
                            },
                            {
                                value: 'stretch',
                                name: 'Stretch cells',
                            },
                            {
                                value: 'baseline',
                                name: 'Align to baseline',
                            },
                        ],
                    },
                    {
                        label: 'Align multiple cell rows',
                        field: 'alignContent',
                        type: 'enum',
                        default: 'space-between',
                        values: [
                            {
                                value: 'flex-start',
                                name: 'Put at beginning',
                            },
                            {
                                value: 'flex-end',
                                name: 'Put at end',
                            },
                            {
                                value: 'center',
                                name: 'Center rows',
                            },
                            {
                                value: 'stretch',
                                name: 'Stretch rows',
                            },
                            {
                                value: 'space-between',
                                name: 'Space between rows',
                            },
                            {
                                value: 'space-around',
                                name: 'Space around rows',
                            },
                        ],
                    },
                ],
            },
        ], this.configuration)
        this.elements = []

        if (region.elements && region.elements.length) {
            for (let i = 0; i < region.elements.length; ++i) {
                this.addElement(region.elements[i])
            }
        }
    }

    addElement (element) {
        if (element.cellId) {
            return this.addCell(element)
        }

        if (element.kitId) {
            return this.addKit(element)
        }

        throw new TypeError('Unknown element type: ' + JSON.stringify(element))
    }

    addCell (cell) {
        this.elements.push(new Cell(cell))

        return this.elements[this.elements.length - 1]
    }

    addKit (kit) {
        this.elements.push(new Kit(kit))

        return this.elements[this.elements.length - 1]
    }

    getElement (elementIdentifier) {
        const elementIdProperty = Object.keys(elementIdentifier)[0]
        const elementId = Object.values(elementIdentifier)[0]

        for (let element of this.elements) {
            if (element[elementIdProperty] === elementId) {
                return element
            }
        }

        throw new Error('Could not find element with ID ' + JSON.stringify(elementId))
    }

    getCells () {
        return this.elements.filter((element) => {
            return element instanceof Cell
        })
    }

    getKits () {
        return this.elements.filter((element) => {
            return element instanceof Kit
        })
    }

    export () {
        return {
            regionId: this.regionId,
            configuration: this.schema.getConfiguration(),
            elements: this.elements.map((element) => {
                return element.export()
            }),
        }
    }
}

export default Region
