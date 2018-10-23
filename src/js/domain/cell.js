import _ from 'lodash'

import ConfigurationSchema from '../configuration/schema'
import generateId from '../generateId'

import Tastic from './tastic'

class Cell {
    constructor (cell = {}) {
        this.cellId = cell.cellId || generateId()
        this.configuration = cell.configuration || {}
        this.schema = new ConfigurationSchema([
            {
                name: 'Responsive',
                folded: true,
                fields: [
                    {
                        label: 'Cell Width',
                        field: 'size',
                        type: 'enum',
                        values: [
                            { value: 2, name: '1/6' },
                            { value: 3, name: '1/4' },
                            { value: 4, name: '1/3' },
                            { value: 6, name: '1/2' },
                            { value: 12, name: '1' },
                        ],
                        default: 12,
                    },
                    {
                        label: 'Show on Mobile',
                        field: 'mobile',
                        type: 'boolean',
                        default: true,
                    },
                    {
                        label: 'Show on Tablet',
                        field: 'tablet',
                        type: 'boolean',
                        default: true,
                    },
                    {
                        label: 'Show on Desktop',
                        field: 'desktop',
                        type: 'boolean',
                        default: true,
                    },
                ],
            },
        ], this.configuration)
        this.tastics = []

        if (cell.tastics && cell.tastics.length) {
            for (let i = 0; i < cell.tastics.length; ++i) {
                this.tastics.push(new Tastic(cell.tastics[i]))
            }
        }
    }

    addTastic (tasticType, configuration = {}, schema = []) {
        this.tastics.push(new Tastic({
            tasticType: tasticType,
            configuration: configuration,
            schema: schema,
        }))

        return this.tastics[this.tastics.length - 1]
    }

    getTastic (tasticId) {
        let tastic = _.find(this.tastics, { tasticId: tasticId })
        if (!tastic) {
            throw new Error('Could not find tastic with ID ' + tasticId)
        }

        return tastic
    }

    export () {
        return {
            cellId: this.cellId,
            configuration: this.schema.getConfiguration(),
            tastics: _.map(this.tastics, (tastic) => {
                return tastic.export()
            }),
        }
    }
}

export default Cell
