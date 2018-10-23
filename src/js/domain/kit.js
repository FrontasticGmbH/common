import generateId from '../generateId'

class Kit {
    constructor (kit) {
        if (!kit.kitDefinitionId) {
            throw new Error('Missing kitDefinitionId in ' + JSON.stringify(kit))
        }

        this.kitDefinitionId = kit.kitDefinitionId
        this.kitId = kit.kitId || generateId()
        this.configuration = kit.configuration || {}
    }

    export () {
        return {
            kitId: this.kitId,
            kitDefinitionId: this.kitDefinitionId,
            configuration: this.configuration,
        }
    }
}

export default Kit
