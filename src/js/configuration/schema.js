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

function buildFieldsFromSectionSchema (sectionSchema) {
    if (!Array.isArray(sectionSchema.fields)) {
        return {}
    }

    let fields = {}

    for (let fieldIndex = 0; fieldIndex < sectionSchema.fields.length; ++fieldIndex) {
        const fieldSchema = sectionSchema.fields[fieldIndex]
        if (!fieldSchema.field) {
            continue
        }

        const type = fieldSchema.type || 'text'
        fields[fieldSchema.field] = {
            field: fieldSchema.field,
            type: type,
            sectionName: sectionSchema.name || '',
            values: fieldSchema.values || [],
            default: getFieldDefaultValue(type, fieldSchema.default),
            validate: fieldSchema.validate || {},
            fields: fieldSchema.fields || null,
            min: typeof fieldSchema.min === 'undefined' ? 1 : fieldSchema.min,
            max: fieldSchema.max || 16,
            required: fieldIsRequired(fieldSchema.required, type, fieldSchema.streamType),
            disabled: fieldSchema.disabled === true,
            translatable: fieldSchema.translatable,
        }
    }

    return fields
}

function getFieldValue (schema, configuration) {
    let value = schema.default
    if (typeof configuration[schema.field] !== 'undefined' && configuration[schema.field] !== null) {
        value = configuration[schema.field]
    }

    if (schema.type === 'group') {
        let values = (value || []).slice(0, schema.max)
        for (let i = values.length; i < schema.min; ++i) {
            values[i] = {}
        }
        return completeGroupConfig(values, schema.fields)
    }

    return value
}

function completeGroupConfig (groupEntries, fieldDefinitions) {
    return (groupEntries || []).map((groupEntry) => {
        if (groupEntry === null || typeof groupEntry !== 'object') {
            groupEntry = {}
        }

        for (let fieldDefinition of fieldDefinitions) {
            if (typeof groupEntry[fieldDefinition.field] === 'undefined' || groupEntry[fieldDefinition.field] === null) {
                groupEntry[fieldDefinition.field] = fieldDefinition.default || null
            }
        }

        return groupEntry
    })
}

function getFieldsWithResolvedStreams (fields, configuration, streamData, customStreamData) {
    if (typeof customStreamData !== 'object' || Array.isArray(customStreamData)) {
        customStreamData = {}
    }

    let resolvedFields = {}
    for (let schema of Object.values(fields)) {
        resolvedFields[schema.field] = getSchemaWithResolvedStreams(schema, configuration, streamData, customStreamData)
    }
    return resolvedFields
}

function getSchemaWithResolvedStreams (schema, configuration, streamData, customStreamData) {
    const customStreamValue = customStreamData[schema.field]

    if (schema.type === 'group') {
        const groupFields = buildFieldsFromSectionSchema(schema)
        return getFieldValue(schema, configuration)
            .map((value, groupIndex) => {
                const elementCustomStreamData = typeof customStreamValue !== 'undefined' && customStreamValue.length > groupIndex ? customStreamValue[groupIndex] : {}
                return getFieldsWithResolvedStreams(groupFields, value, streamData, elementCustomStreamData)
            })
    }

    if (typeof customStreamValue !== 'undefined') {
        return customStreamValue
    }

    const value = getFieldValue(schema, configuration)

    if (schema.type === 'stream') {
        return streamData[value] || null
    }

    return value
}

function getFieldDefaultValue (type, defaultValue) {
    if (typeof defaultValue !== 'undefined') {
        return defaultValue
    }

    switch (type) {
    case 'group':
        return []
    case 'decimal':
    case 'integer':
    case 'float':
    case 'number':
        return 0
    case 'string':
    case 'text':
    case 'markdown':
        return ''
    case 'json':
        return '{}'
    case 'boolean':
        return false
    default:
        return null
    }
}

class ConfigurationSchema {
    constructor (schema = [], configuration = {}) {
        this.schema = schema
        this.setConfiguration(configuration)

        this.fields = {}

        for (let sectionIndex = 0; sectionIndex < this.schema.length; ++sectionIndex) {
            this.fields = {
                ...this.fields,
                ...buildFieldsFromSectionSchema(this.schema[sectionIndex]),
            }
        }
    }

    setConfiguration (configuration) {
        this.configuration = !Array.isArray(configuration) ? configuration || {} : {}
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
            {
                ...this.configuration,
                ...{ [field]: value },
            }
        )
    }

    get (field) {
        const fieldConfig = this.fields[field]

        if (!fieldConfig) {
            console.warn('Unknown field ' + field + ' in this configuration schema.')
            return this.configuration[field] || null
        }

        return getFieldValue(fieldConfig, this.configuration)
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
                const groupSchema = new ConfigurationSchema([schema], configuration)
                return groupSchema.hasMissingRequiredFieldValues(skipStreams)
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

        // If media field has an empty media object like: {media: null}
        // It should be flagged as a missing required value
        if (schema.type === 'media' && value) {
            return Object.values(value).some((v) => !v)
        }

        // If string field has an empty translatable object like: {en_GB@EUR: ""}
        // It should be flagged as a missing required value
        if (schema.type === 'string' && schema.translatable && value) {
            return Object.values(value).some((v) => !v)
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

    getConfigurationWithResolvedStreams (streamData = {}, customStreamData = {}) {
        return getFieldsWithResolvedStreams(this.fields, this.configuration, streamData, customStreamData)
    }
}

export default ConfigurationSchema
