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

                this.fields[this.schema[i].fields[j].field] = {
                    type: this.schema[i].fields[j].type || 'text',
                    values: this.schema[i].fields[j].values || [],
                    default: this.schema[i].fields[j].default || null,
                    validate: this.schema[i].fields[j].validate || {},
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

        if (typeof this.configuration[field] === 'undefined') {
            return this.fields[field].default
        }

        return this.configuration[field]
    }

    getSchema () {
        return this.schema
    }

    getConfiguration () {
        return this.configuration
    }
}

export default ConfigurationSchema
