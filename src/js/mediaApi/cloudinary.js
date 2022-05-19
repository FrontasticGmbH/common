const cloudinaryEncodeURI = (string) => {
    return encodeURI(string).replace(/[?=]/g, function (character) {
        return '%' + character.charCodeAt(0).toString(16).toUpperCase()
    })
}

const cloudinaryUrl = (imageIdentifier, resourceType, configuration, options) => {
    options = {
        resourceType: resourceType,
        type: 'upload',
        ...options,
    }

    let transformations = []
    for (let [transformation, value] of Object.entries(options)) {
        switch (transformation) {
        case 'secure':
        case 'resourceType':
        case 'type':
            // Ignore, because they are part of the URL
            break
        case 'background':
        case 'crop':
        case 'fetch_format':
        case 'gravity':
        case 'height':
        case 'quality':
        case 'width':
        case 'x':
        case 'y':
            if (value) {
                transformations.push(transformation[0] + '_' + value)
            }
            break
        default:
            throw new Error('Unhandled image transformation ' + transformation)
        }
    }
    transformations.sort()

    return `https://res.cloudinary.com/${configuration.cloudName}/${options.resourceType}/${options.type}/${transformations.join(',')}/${cloudinaryEncodeURI(imageIdentifier)}`
}

class Cloudinary {
    constructor (configuration) {
        this.configuration = {
            cloudName: configuration.cloudName,
        }
    }

    getImageUrl (media, width, height, options = {}) {
        return cloudinaryUrl(
            media.mediaId,
            typeof media.resourceType === 'string' && media.resourceType !== '' ? media.resourceType : 'image',
            this.configuration,
            {
                fetch_format: media.format && media.format === 'svg' ? undefined : 'auto',
                width: width,
                height: height,
                secure: true,
                ...this.getQuality(options),
                ...this.getFetchFormat(options),
                ...this.getGravityOptions(options),
                ...this.cropOptions(options),
            },
        )
    }

    getFetchImageUrl (url, width, height, options = {}) {
        if (url.startsWith('//')) {
            // Cloudinary cannot cope with non-schemed URLs, assume HTTPS
            url = 'https:' + url
        }

        return cloudinaryUrl(
            url,
            'image',
            this.configuration,
            {
                fetch_format: 'auto',
                type: 'fetch',
                width: width,
                height: height,
                secure: true,
                ...this.getQuality(options),
                ...this.getFetchFormat(options),
                ...this.getGravityOptions(options),
                ...this.cropOptions(options),
            },
        )
    }

    getImageUrlWithoutDefaults (media, width, height, options = {}) {
        return cloudinaryUrl(
            media.mediaId,
            typeof media.resourceType === 'string' && media.resourceType !== '' ? media.resourceType : 'image',
            this.configuration,
            {
                width: width,
                height: height,
                ...options,
            },
        )
    }

    /**
     * @param imageOptions
     * @returns {{gravity: string}}
     * @private
     */
    getGravityOptions (imageOptions) {
        if (imageOptions.crop) {
            return {}
        }

        let options = {
            gravity: 'faces:auto',
        }

        if (imageOptions.gravity) {
            options.gravity = imageOptions.gravity.mode === 'custom' ? 'xy_center' : imageOptions.gravity.mode

            if (imageOptions.gravity.coordinates) {
                options.x = imageOptions.gravity.coordinates.x
                options.y = imageOptions.gravity.coordinates.y
            }
        }

        return options
    }

    /**
     * @param imageOptions
     * @returns {{crop: string}}
     * @private
     */
    cropOptions (imageOptions) {
        let options = {
            crop: 'fill',
        }

        if (imageOptions.crop) {
            options.crop = imageOptions.crop
        }

        if (imageOptions.background) {
            options.background = imageOptions.background
        }

        return options
    }

    /**
     * @param imageOptions
     * @returns {{quality: string|number}}
     * @private
     */
    getQuality (imageOptions) {
        let options = {
            quality: 'auto',
        }

        if (imageOptions.quality) {
            options.quality = imageOptions.quality
        }

        return options
    }

    /**
     * @param imageOptions
     * @returns {{fetch_format: string}}
     * @private
     */
    getFetchFormat (imageOptions) {
        let options = {}

        if (imageOptions.fetch_format) {
            options.fetch_format = imageOptions.fetch_format
        }

        if (imageOptions.fetchFormat) {
            options.fetch_format = imageOptions.fetchFormat
        }

        return options
    }
}

export default Cloudinary
