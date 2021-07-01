var path = require('path')

module.exports = {
    target: 'node',
    entry: './src/js/index.js',
    output: {
        path: path.resolve(__dirname, 'src/js/'),
        filename: 'frontastic-common.js',
        library: 'frontastic-common',
        libraryTarget: 'umd',
    },
}
