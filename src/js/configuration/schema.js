import _ from 'lodash'

class ConfigurationSchema {
    constructor (schema = [], configuration = {}, id = null) {
        this.schema = schema
        this.setConfiguration(configuration)

        this.fields = {}

        for (let i = 0; i < this.schema.length; ++i) {
            for (let j = 0; j < this.schema[i].fields.length; ++j) {
                if (!this.schema[i].fields[j].field) {
                    continue
                }

                const type = this.schema[i].fields[j].type || 'text'
                this.fields[this.schema[i].fields[j].field] = {
                    type: type,
                    sectionName: this.schema[i].name || '',
                    values: this.schema[i].fields[j].values || [],
                    default: this.schema[i].fields[j].default || null,
                    validate: this.schema[i].fields[j].validate || {},
                    fields: this.schema[i].fields[j].fields || null,
                    min: this.schema[i].fields[j].min || 1,
                    max: this.schema[i].fields[j].max || 16,
                    // @TODO: Streams should be marked as required in the tastic configurations
                    required: Boolean(this.schema[i].fields[j].required) || type === 'stream',
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

        if (typeof this.configuration[field] === 'undefined') {
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
                if (typeof groupEntry[fieldDefinition.field] === 'undefined') {
                    groupEntry[fieldDefinition.field] = fieldDefinition.default || null
                }
            })
            return groupEntry
        })
    }
}

export default ConfigurationSchema
