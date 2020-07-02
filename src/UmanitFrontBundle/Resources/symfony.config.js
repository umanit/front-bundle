###> imports ###
const PurgecssPlugin = require('purgecss-webpack-plugin');
let path = require('path');
###< imports ###

###> config ###
if(Encore.isProduction())
{
    Encore
      .addPlugin(
        new PurgecssPlugin({
            paths: glob.sync([
                  path.join(__dirname, 'templates/{*.html.twig,!(style_guide)/**/*.html.twig}'),
                  assetPath + '/js/**/*.js',
              ],
            ),
            extractors: [
                {
                    extractor: (content) => {
                        return content.match(/[A-z0-9-:\/]+/g) || [];
                    },
                    extensions: ['twig', 'js'],
                },
            ],
            whitelistPatterns: [/choices/, /splide/],
        }))
}
###< config ###
