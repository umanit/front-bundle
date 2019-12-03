###> imports ###
const PurgecssPlugin = require('purgecss-webpack-plugin');
let path = require('path');
###< imports ###

###> config ###
Encore.addPlugin(new PurgecssPlugin({
    paths: glob.sync([
            path.join(__dirname, 'templates/**/*.html.twig'),
            'vendor/umanit/front-bundle/src/UmanitFrontBundle/Resources/views/style_guide/**/*.html.twig',
            assetPath + '/js/**/*.js'
        ]
    ),
    extractors: [
        {
            extractor: class {
                static extract(content) {
                    return content.match(/[A-z0-9-:\/]+/g) || []
                }
            },
            extensions: ['twig','js'],
        }
    ],
    whitelistPatterns: [/slick/,/select2/,/link--i/]
}));
###< config ###
