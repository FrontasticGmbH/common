import _ from 'lodash'

import Region from './region'

class Page {
    constructor (page = {}, layoutRegions = [], tastics = []) {
        this.pageId = page.pageId || null
        this.nodes = page.nodes || []
        this.layoutId = page.layoutId || 'three_rows'
        this.name = page.name || 'Unnamed Page'

        this.regions = {}

        this.tastics = _.map(tastics, 'configurationSchema')

        for (let i = 0; i < layoutRegions.length; ++i) {
            let regionId = layoutRegions[i]
            if (page.regions &&
                page.regions[regionId] &&
                page.regions[regionId].elements &&
                page.regions[regionId].elements.length
            ) {
                page.regions[regionId].elements = this.mapTastics(page.regions[regionId].elements)
            }

            this.createRegion(
                regionId,
                (page.regions && page.regions[regionId]) || {}
            )
        }
    }

    mapTastics (elements) {
        for (let j = 0; j < elements.length; ++j) {
            let element = elements[j]

            if (!element.cellId) {
                continue
            }

            if (!element.tastics || !element.tastics.length) {
                continue
            }

            for (let k = 0; k < element.tastics.length; ++k) {
                let tastic = element.tastics[k]

                let tasticSpecification = _.find(this.tastics, (tasticSpecification) => {
                    return tasticSpecification.tasticType === tastic.tasticType
                }) || { schema: [] }

                tastic.schema = tasticSpecification.schema
            }
        }

        return elements
    }

    createRegion (identifier, regionData) {
        regionData.regionId = identifier

        this.regions[identifier] = new Region(regionData)
    }

    getRegion (identifier) {
        if (!this.regions[identifier]) {
            throw new Error('Region with identifier ' + identifier + ' unknown.')
        }

        return this.regions[identifier]
    }

    addCell (region, configuration = {}) {
        return this.getRegion(region).addCell({
            configuration: configuration,
        })
    }

    addKit (region, kit) {
        return this.getRegion(region).addKit(kit)
    }

    findElement (elementId) {
        for (let region in this.regions) {
            let elementIndex = _.findIndex(this.regions[region].elements, elementId)
            if (elementIndex >= 0) {
                return [region, elementIndex]
            }
        }

        throw new Error('Could not find element with ' + JSON.stringify(elementId))
    }

    hasElement (elementId) {
        try {
            return !!this.findElement(elementId)
        } catch (error) {
            return false
        }
    }

    getElement (elementId) {
        let [region, elementIndex] = this.findElement(elementId)
        return this.regions[region].elements[elementIndex]
    }

    removeElement (elementId) {
        let [region, elementIndex] = this.findElement(elementId)
        this.regions[region].elements.splice(elementIndex, 1)
    }

    moveElement (elementId, target) {
        if (!this.regions[target.region]) {
            throw new Error('Unknown target region ' + target.region)
        }

        let [region, elementIndex] = this.findElement(elementId)
        let element = this.regions[region].elements.splice(elementIndex, 1)[0]

        this.regions[target.region].elements.splice(
            typeof target.element === 'undefined' ?
                this.regions[target.region].elements.length :
                target.element - ((region === target.region) && (target.element > elementIndex) ? 1 : 0),
            0,
            element
        )
    }

    addTastic (regionId, cellId, tasticType, position) {
        let schema = _.find(this.tastics, { tasticType: tasticType })

        return this.getRegion(regionId).getElement({ cellId: cellId }).addTastic(tasticType, {}, schema, position)
    }


    findTastic (tasticId) {
        for (let region in this.regions) {
            for (let elementIndex = 0; elementIndex < this.regions[region].elements.length; ++elementIndex) {
                let tasticIndex = _.findIndex(
                    this.regions[region].elements[elementIndex].tastics,
                    { tasticId: tasticId }
                )
                if (tasticIndex >= 0) {
                    return [region, elementIndex, tasticIndex]
                }
            }
        }

        throw new Error('Could not find tastic with id ' + tasticId)
    }

    hasTastic (tasticId) {
        try {
            return !!this.findTastic(tasticId)
        } catch (error) {
            return false
        }
    }

    getTastic (tasticId) {
        let [region, elementIndex, tasticIndex] = this.findTastic(tasticId)
        return this.regions[region].elements[elementIndex].tastics[tasticIndex]
    }

    removeTastic (tasticId) {
        let [region, elementIndex, tasticIndex] = this.findTastic(tasticId)
        this.regions[region].elements[elementIndex].tastics.splice(tasticIndex, 1)
    }

    moveTastic (tasticId, target) {
        let [region, elementIndex, tasticIndex] = this.findTastic(tasticId)
        let tastic = this.regions[region].elements[elementIndex].tastics.splice(tasticIndex, 1)[0]
        let [targetRegion, targetElementIndex] = this.findElement({ cellId: target.cell })

        this.regions[targetRegion].elements[targetElementIndex].tastics.splice(
            typeof target.tasticDropPosition === 'undefined' ?
                this.regions[targetRegion].elements[targetElementIndex].tastics.length :
                target.tasticDropPosition - (
                    (region === targetRegion) &&
                    (elementIndex === targetElementIndex) &&
                    (target.tasticDropPosition > tasticIndex) ? 1 : 0),
            0,
            tastic
        )
    }

    export () {
        return {
            pageId: this.pageId,
            nodes: this.nodes,
            layoutId: this.layoutId,
            regions: _.mapValues(this.regions, (region) => {
                return region.export()
            }),
        }
    }
}

export default Page
