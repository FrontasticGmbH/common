import _ from 'lodash'

import ConfigurationSchema from '../configuration/schema'
import generateId from '../generateId'

import Tastic from './tastic'
import cellDimensions from './cellDimensions'

class Cell {
    constructor (cell = {}) {
        this.cellId = cell.cellId || generateId()
        this.configuration = cell.configuration || {}
        this.customConfiguration = cell.customConfiguration || {}
        this.schema = new ConfigurationSchema([
            {
                name: 'Responsive',
                folded: true,
                fields: [
                    {
                        label: 'Cell Width',
                        field: 'size',
                        type: 'enum',
                        values: _.map(
                            cellDimensions,
                            (cell) => {
                                return {
                                    name: cell.name,
                                    value: cell.size,
                                }
                            }
                        ),
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

    addTastic (tasticType, configuration = {}, schema = [], position = 0) {
        const newTastic = new Tastic({
            tasticType: tasticType,
            configuration: configuration,
            schema: schema,
        })

        this.tastics.splice(position, 0, newTastic)

        return newTastic
    }

    getTastic (tasticId) {
        let tastic = _.find(this.tastics, { tasticId: tasticId })
        if (!tastic) {
            throw new Error('Could not find tastic with ID ' + tasticId)
        }

        return tastic
    }

    getTasticCount () {
        return this.tastics.length
    }

    export () {
        return {
            cellId: this.cellId,
            configuration: this.schema.getConfiguration(),
            customConfiguration: this.customConfiguration,
            tastics: _.map(this.tastics, (tastic) => {
                return tastic.export()
            }),
        }
    }
}

export default Cell
