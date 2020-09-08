import Cloudinary from '../../../src/js/mediaApi/cloudinary'

// WARNING: All test URLs were generated with the original Cloudinary API, so
// DO NOT change the expected URLs!

test.each([
    [
        [{ mediaId: 'media-id' }, 1024, 768],
        'https://res.cloudinary.com/my-cloud/image/upload/c_fill,f_auto,g_faces:auto,h_768,q_auto,w_1024/media-id',
    ],
    [
        [{ mediaId: 'media-id', format: 'svg' }, 1024, 768],
        'https://res.cloudinary.com/my-cloud/image/upload/c_fill,g_faces:auto,h_768,q_auto,w_1024/media-id',
    ],
    [
        [{ mediaId: 'media-id' }, 1024, 768, { crop: 'fill', background: 'transparent' }],
        'https://res.cloudinary.com/my-cloud/image/upload/b_transparent,c_fill,f_auto,h_768,q_auto,w_1024/media-id',
    ],
    [
        [{ mediaId: 'media-id' }, 1024, 768, { background: 'transparent', gravity: { mode: 'custom', coordinates: { x: 12, y: 12 } } }],
        'https://res.cloudinary.com/my-cloud/image/upload/b_transparent,c_fill,f_auto,g_xy_center,h_768,q_auto,w_1024,x_12,y_12/media-id',
    ],
    [
        [{ mediaId: 'media-id' }, 1024, 768, { background: 'transparent', gravity: { mode: 'center' } }],
        'https://res.cloudinary.com/my-cloud/image/upload/b_transparent,c_fill,f_auto,g_center,h_768,q_auto,w_1024/media-id',
    ],
])('cloudinary URL is generated for media objects', (parameters, url) => {
    let cloudinary = new Cloudinary({ cloudName: 'my-cloud' })

    expect(cloudinary.getImageUrl(...parameters)).toEqual(url)
})

test.each([
    [
        ['http://k023.de/image.png', 1024, 768],
        'https://res.cloudinary.com/my-cloud/image/fetch/c_fill,f_auto,g_faces:auto,h_768,q_auto,w_1024/http://k023.de/image.png',
    ],
    [
        ['http://k023.de/image.png?foo=%20bar', 1024, 768],
        'https://res.cloudinary.com/my-cloud/image/fetch/c_fill,f_auto,g_faces:auto,h_768,q_auto,w_1024/http://k023.de/image.png%3Ffoo%3D%2520bar',
    ],
    [
        ['http://k023.de/image.png', 1024, 768, { crop: 'fill', background: 'transparent' }],
        'https://res.cloudinary.com/my-cloud/image/fetch/b_transparent,c_fill,f_auto,h_768,q_auto,w_1024/http://k023.de/image.png',
    ],
    [
        ['http://k023.de/image.png', 1024, 768, { background: 'transparent', gravity: { mode: 'custom', coordinates: { x: 12, y: 12 } } }],
        'https://res.cloudinary.com/my-cloud/image/fetch/b_transparent,c_fill,f_auto,g_xy_center,h_768,q_auto,w_1024,x_12,y_12/http://k023.de/image.png',
    ],
    [
        ['http://k023.de/image.png', 1024, 768, { background: 'transparent', gravity: { mode: 'center' } }],
        'https://res.cloudinary.com/my-cloud/image/fetch/b_transparent,c_fill,f_auto,g_center,h_768,q_auto,w_1024/http://k023.de/image.png',
    ],
])('cloudinary URL is generated for media objects', (parameters, url) => {
    let cloudinary = new Cloudinary({ cloudName: 'my-cloud' })

    expect(cloudinary.getFetchImageUrl(...parameters)).toEqual(url)
})
