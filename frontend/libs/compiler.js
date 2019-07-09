const webpack = require('webpack');
const rimraf = require('rimraf');
const { globalSettings } = require('../settings');

// execute webpack compiler on array of configurations
// and nicely handle the console output
const multiCompile = configs => {
    if (!configs || !configs.length) {
        return console.error('Nothing to build. Build aborted.');
    }

    configs.forEach((config) => {
        console.log(`${config.namespace} building for ${config.webpack.mode}...`);

        if (config.webpack.watch) {
            console.log(`${config.namespace} watch mode: ON`);
        }
    });

    const webpackConfigs = configs.map(item => item.webpack);
    webpack(webpackConfigs, (err, multiStats) => {
        if (err) {
            console.error(err.stack || err);

            if (err.details) {
                console.error(err.details);
            }

            return;
        }

        multiStats.stats.forEach(
            (stat, index) => {
                console.log(`${configs[index].namespace} namespace building statistics:`);
                console.log(`Components entry points: ${configs[index].componentEntryPointsLength}`);
                console.log(`Components styles: ${configs[index].stylesLength}`);
                console.log(stat.toString(webpackConfigs[index].stats), '\n')
            }
        );
    });
};

module.exports = {
    multiCompile
};
