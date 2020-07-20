import cloudinary from 'cloudinary-core'

import _ from 'lodash'

class Cloudinary {
    constructor(configuration) {
        this.cloudinary = new cloudinary.Cloudinary({
            cloud_name: configuration.cloudName,
        })
    }

    getImageUrl(media, width, height, options = {}) {
        return this.cloudinary.url(
            media.mediaId,
            _.extend(
                {
                    fetch_format: media.format && media.format === 'svg' ? undefined : 'auto',
                    width: width,
                    height: height,
                    quality: 'auto',
                    secure: true,
                },
                this.getGravityOptions(options),
                this.cropOptions(options)
            )
        )
    }

    getFetchImageUrl(url, width, height, options = {}) {
        if (url.startsWith('//')) {
            // Cloudinary cannot cope with non-schemed URLs, assume HTTPS
            url = 'https:' + url
        }

        return this.cloudinary.url(
            url,
            _.extend(
                {
                    fetch_format: 'auto',
                    type: 'fetch',
                    width: width,
                    height: height,
                    quality: 'auto',
                    secure: true,
                },
                this.getGravityOptions(options),
                this.cropOptions(options)
            )
        )
    }

    getImageUrlWithoutDefaults(media, width, height, options = {}) {
        return this.cloudinary.url(
            media.mediaId,
            _.extend(
                {
                    width: width,
                    height: height,
                },
                options
            )
        )
    }

    /**
     * @param imageOptions
     * @returns {{gravity: string}}
     * @private
     */
    getGravityOptions(imageOptions) {
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
    cropOptions(imageOptions) {
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
}

export default Cloudinary
