import Cloudinary from './mediaApi/cloudinary'

class MediaApi {
    constructor () {
        this.imageSizes = [16, 32, 64, 128, 256, 512, 1024, 2048]
    }

    getImageDimensions (media, inputWidth, inputHeight, inputCropRatio = null, factor = 1) {
        let cropRatio = this.getFloatRatio(media, inputCropRatio)
        let width = inputWidth && Math.round(+inputWidth * factor)
        let height = inputHeight && Math.round(+inputHeight * factor)

        if (width && inputCropRatio) {
            height = Math.round(width * cropRatio)
        }

        if (height && !width && inputCropRatio) {
            width = Math.round(height / cropRatio)
        }

        return [width, height]
    }

    getFloatRatio (media = null, cropRatio = null) {
        if (!cropRatio && media && media.width && media.height) {
            return media.height / media.width
        }

        const ratioStringMatches = String(cropRatio).match(/([0-9]+):([0-9]+)/)

        if (!ratioStringMatches) {
            // Float ratio parameter is deprecated but we can cope with it
            return cropRatio
        }

        return ratioStringMatches[2] / ratioStringMatches[1]
    }

    getImageLink (media, configuration, inputWidth, inputHeight, inputCropRatio, options = {}, factor = 1) {
        let mediaApi = this.getMediaApi(configuration)
        let [width, height] = this.getImageDimensions(media, inputWidth, inputHeight, inputCropRatio, factor)
        let ratio = width / height

        // Because we do not want the image provider to cache far too many
        // image sizes we bind ourselves to a certain set of sizes and choose
        // the next larger one. Thus we rely on the browser to resize the image
        // slightly. This only works for resized images, obviously and not if
        // the image is supposed to be in a defined size.
        if (['fill', 'pad'].includes(options.crop)) {
            for (let i = 0; i < this.imageSizes.length; ++i) {
                if (this.imageSizes[i] >= width) {
                    width = this.imageSizes[i]
                    break
                }
            }
            height = !options.autoHeight ? Math.ceil(width / ratio) : null
        }

        if (typeof media === 'string') {
            return mediaApi.getFetchImageUrl(media, width, height, options)
        } else {
            return mediaApi.getImageUrl(media, width, height, options)
        }
    }

    getMediaApi (configuration) {
        switch (configuration.media.engine) {
        case 'cloudinary':
            return new Cloudinary(configuration.media)
        default:
            throw new Error('No valid media API found.')
        }
    }

    static getElementDimensions (element) {
        let padding = 0
        if (getComputedStyle) {
            let computedStyle = getComputedStyle(element)
            padding += parseFloat(computedStyle.paddingLeft) + parseFloat(computedStyle.paddingRight)
        }

        return {
            width: element.clientWidth - padding,
            height: element.clientHeight,
        }
    }
}

export default MediaApi
