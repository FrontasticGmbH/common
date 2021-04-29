import Tastic from '../../../src/js/domain/tastic'

describe('Tastic', () => {
    it('contains general settings in schema when no schema is given', () => {
        const tastic = new Tastic()

        expect(tastic.schema.schema).toHaveLength(1)
        expect(tastic.schema.schema[0].name).toBe('General settings')
    })

    it('merges general settings into the schema', () => {
        const firstSchema = {
            name: 'First',
            fields: [],
        }
        const secondSchema = {
            name: 'Second',
            fields: [],
        }

        const tastic = new Tastic({
            schema: [firstSchema, secondSchema],
        })

        expect(tastic.schema.schema).toHaveLength(3)
        expect(tastic.schema.schema[0].name).toBe('General settings')
        expect(tastic.schema.schema[1]).toEqual(firstSchema)
        expect(tastic.schema.schema[2]).toEqual(secondSchema)
    })
})
