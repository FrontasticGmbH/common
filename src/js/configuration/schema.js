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
                    values: this.schema[i].fields[j].values || [],
                    default: this.schema[i].fields[j].default || null,
                    validate: this.schema[i].fields[j].validate || {},
                    fields: this.schema[i].fields[j].fields || null,
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
        if (!this.fields[field]) {
            console.warn('Unknown field ' + field + ' in this configuration schema.')
            return this.configuration[field] || null
        }

        if (this.fields[field].type === 'group') {
            return this.completeGroupConfig(
                this.configuration[field] || [],
                this.fields[field].fields
            )
        }

        if (typeof this.configuration[field] === 'undefined') {
            return this.fields[field].default
        }

        return this.configuration[field]
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

    hasMissingRequiredFieldValues (skipStreams = false) {
        return Object.entries(this.fields).some(([field, schema]) => {
            let configurationValue = this.configuration[field]

            if (schema.type === 'group') {
                const groupEntries = configurationValue || []
                return groupEntries.some(configuration => {
                    const gropuSchema = new ConfigurationSchema([schema], configuration)
                    return gropuSchema.hasMissingRequiredFieldValues(skipStreams)
                })
            }

            if (schema.type === 'stream' && skipStreams) {
                return false
            }

            return schema.required && schema.default === null && (
                typeof configurationValue === 'undefined' || configurationValue === null || configurationValue === '')
        })
    }

    /**
     * @private
     * @param {object[]} groupEntries
     * @param {object[]} fieldDefinitions
     */
    completeGroupConfig (groupEntries, fieldDefinitions) {
        return _.map(groupEntries, (groupEntry) => {
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
