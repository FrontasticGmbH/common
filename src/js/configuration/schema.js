import _ from 'lodash'

const AUTOMATIC_REQUIRED_STREAM_TYPES = [
    'product',
    'product-list',
    'content',
    'content-list',
]

function fieldIsRequired (requiredFlag, type, streamType) {
    if (requiredFlag !== undefined) {
        return Boolean(requiredFlag)
    }

    // @TODO: Streams should be marked as required in the tastic configurations
    return type === 'stream' && AUTOMATIC_REQUIRED_STREAM_TYPES.includes(streamType)
}

class ConfigurationSchema {
    constructor (schema = [], configuration = {}, id = null) {
        this.schema = schema
        this.setConfiguration(configuration)

        this.fields = {}

        for (let sectionIndex = 0; sectionIndex < this.schema.length; ++sectionIndex) {
            const sectionSchema = this.schema[sectionIndex]
            for (let fieldIndex = 0; fieldIndex < sectionSchema.fields.length; ++fieldIndex) {
                const fieldSchema = sectionSchema.fields[fieldIndex]
                if (!fieldSchema.field) {
                    continue
                }

                const type = fieldSchema.type || 'text'
                this.fields[fieldSchema.field] = {
                    type: type,
                    sectionName: sectionSchema.name || '',
                    values: fieldSchema.values || [],
                    default: (typeof fieldSchema.default !== 'undefined' ? fieldSchema.default : null),
                    validate: fieldSchema.validate || {},
                    fields: fieldSchema.fields || null,
                    min: (typeof fieldSchema.min === 'undefined') ? 1 : fieldSchema.min,
                    max: fieldSchema.max || 16,
                    required: fieldIsRequired(fieldSchema.required, type, fieldSchema.streamType),
                    disabled: fieldSchema.disabled === true,
                }
            }
        }
    }

    setConfiguration (configuration) {
        this.configuration = !_.isArray(configuration) ? configuration || {} : {}
    }

    set (field, value) {
        if (!this.fields[field]) {
            throw new Error('Unknown field ' + field + ' in this configuration schema.')
        }

        // @TODO: Validate:
        // * Type
        // * Values (enum)
        // * Validation rules

        // Ensure we get a new object on write so that change detection in
        // React-Redux apps works
        return new ConfigurationSchema(
            this.schema,
            _.extend(
                {},
                this.configuration,
                { [field]: value }
            ),
            this.id
        )
    }

    get (field) {
        const fieldConfig = this.fields[field]

        if (!fieldConfig) {
            console.warn('Unknown field ' + field + ' in this configuration schema.')
            return this.configuration[field] || null
        }

        if (fieldConfig.type === 'group') {
            let values = (this.configuration[field] || []).slice(0, fieldConfig.max)
            for (let i = values.length; i < fieldConfig.min; ++i) {
                values[i] = {}
            }
            return this.completeGroupConfig(values, fieldConfig.fields)
        }

        if (typeof this.configuration[field] === 'undefined' || this.configuration[field] === null) {
            return fieldConfig.default
        }

        return this.configuration[field]
    }

    getField (field) {
        const schema = this.fields[field]
        if (!schema) {
            throw new Error('Unknown field ' + field + ' in this configuration schema.')
        }
        return schema
    }

    has (field) {
        return !!this.fields[field]
    }

    getSchema () {
        return this.schema
    }

    getConfiguration () {
        return this.configuration
    }

    isFieldRequired (field) {
        return this.getField(field).required
    }

    isFieldDisabled (field) {
        return this.getField(field).disabled
    }

    hasMissingRequiredValueInField (field, skipStreams = false) {
        const schema = this.getField(field)
        const value = this.get(field)

        if (schema.type === 'group') {
            return value.some(configuration => {
                const gropuSchema = new ConfigurationSchema([schema], configuration)
                return gropuSchema.hasMissingRequiredFieldValues(skipStreams)
            })
        }

        if (!schema.required) {
            return false
        }

        if (schema.type === 'stream' && skipStreams) {
            return false
        }

        if (schema.type === 'reference') {
            return typeof value !== 'object' || value === null ||
                typeof value.type !== 'string' || value.type === '' ||
                typeof value.target !== 'string' || value.target === ''
        }

        return typeof value === 'undefined' || value === null || value === ''
    }

    hasMissingRequiredFieldValues (skipStreams = false) {
        return Object.keys(this.fields).some((field) => {
            return this.hasMissingRequiredValueInField(field, skipStreams)
        })
    }

    hasMissingRequiredFieldValuesInSection (sectionName, skipStreams = false) {
        return Object.entries(this.fields).some(([field, schema]) => {
            return schema.sectionName === sectionName && this.hasMissingRequiredValueInField(field, skipStreams)
        })
    }

    /**
     * @private
     * @param {object[]} groupEntries
     * @param {object[]} fieldDefinitions
     */
    completeGroupConfig (groupEntries, fieldDefinitions) {
        return _.map(groupEntries, (groupEntry) => {
            if (groupEntry === null || typeof groupEntry !== 'object') {
                groupEntry = {}
            }
            _.forEach(fieldDefinitions, (fieldDefinition) => {
                if (typeof groupEntry[fieldDefinition.field] === 'undefined' || groupEntry[fieldDefinition.field] === null) {
                    groupEntry[fieldDefinition.field] = fieldDefinition.default || null
                }
            })
            return groupEntry
        })
    }
}

export default ConfigurationSchema
