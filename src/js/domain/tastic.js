import ConfigurationSchema from '../configuration/schema'
import generateId from '../generateId'

class Tastic {
    constructor (tastic = {}) {
        this.tasticId = tastic.tasticId || generateId()
        this.tasticType = tastic.tasticType
        this.configuration = tastic.configuration || {}
        let mergedSchema = [
            {
                name: 'General',
                folded: true,
                fields: [
                    {
                        label: 'Name',
                        field: 'name',
                        type: 'string',
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
                    {
                        label: 'Anchor',
                        field: 'tasticId',
                        type: 'string',
                        translatable: false,
                        disabled: true,
                        default: '#' + this.tasticId,
                    },
                ],
            },
        ]

        if (tastic.schema) {
            for (let i = 0; i < tastic.schema.length; ++i) {
                mergedSchema.push(tastic.schema[i])
            }
        }

        this.schema = new ConfigurationSchema(mergedSchema, this.configuration)
    }

    export () {
        return {
            tasticId: this.tasticId,
            tasticType: this.tasticType,
            configuration: this.schema.getConfiguration(),
        }
    }
}

export default Tastic
