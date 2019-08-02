const commandLineParser = require('commander');
const { join } = require('path');
const { globalSettings } = require('../settings');
const { scripts } = require('../../package.json');
let mode = null;

const collectArguments = (argument, argumentCollection) => {
    argumentCollection.push(argument);
    return argumentCollection;
};

const isModeAvailable = requestedMode => {
    const { modes } = globalSettings;

    return Object.values(modes).find(mode => mode === requestedMode);
};

const checkIsModeAvailable = requestedMode => {
    const mode = isModeAvailable(requestedMode);

    if (!mode) {
        throw new Error(`Mode "${requestedMode}" is not available`);
    }
}

const collectDataOfAllowedFlags = (parserObject, configData) => {
    const allowedFlagsData = {};
    parserObject.options.forEach(option => {
        const {short, long, required} = option;

        allowedFlagsData[short] = {
            required: required,
        };

        allowedFlagsData[long] = {
            required: required,
        };
    });

    Object.values(configData).forEach(value => {
        const flagsData = value.match(/--[a-z]{1,}/g);

        if (flagsData) {
            flagsData.forEach( flag => {
                allowedFlagsData[flag] = {
                    required: false,
                };
            })
        }
    });

    return allowedFlagsData;
};

const checkValidFlag = (flag, allowedFlagsData) => {
    if (!flag.indexOf('-') && !allowedFlagsData[flag]) {
        throw new Error(`Flag "${flag}" is not available`);
    }
};

const checkCommand = (allowedFlagsData, args, index) => {
    const previousParam = args[index - 1];
    const currentParam = args[index];
    const isParameterAFlag = !currentParam.indexOf('-');
    const isParameterAValueOfFlag = !previousParam.indexOf('-') && allowedFlagsData[previousParam].required;
    const isParameterAValidCommand = !!scripts[currentParam] || currentParam === 'node';

    if (isParameterAFlag || isParameterAValueOfFlag) {
        return '';
    };

    if (isParameterAValidCommand) {
        return currentParam;
    };

    throw new Error(`Command "${args[index]}" is not available`);
};

commandLineParser
    .option('-n, --namespace <namespace name>', 'build the requested namespace. Multiple arguments are allowed.', collectArguments, [])
    .option('-t, --theme <theme name>', 'build the requested theme. Multiple arguments are allowed.', collectArguments, [])
    .option('-i, --info', 'information about all namespaces and available themes')
    .option('-c, --config <path>', 'path to JSON file with namespace config', globalSettings.paths.namespaceConfig)
    .arguments('<mode>')
    .action(function (modeValue) {
        const { argv, env } = process;
        const modeIndexInArgs = process.argv.findIndex(element => element === modeValue);
        const allowedFlagsData = collectDataOfAllowedFlags(this, scripts);

        if (env && env.npm_config_argv) {
            const originalArgumentsString = env.npm_config_argv;
            const {original: originalArguments} = JSON.parse(originalArgumentsString);

            originalArguments.forEach(argument => {
                if (!argument.indexOf('-') && !originalArguments.includes('--')) {
                    throw new Error('It is not possible to use flags without "--" indentifier if you use "npm" package');
                }
            })
        }

        argv.forEach((arg, index) => {
            if (index <= modeIndexInArgs) {
                return
            };

            checkIsModeAvailable(modeValue);
            checkValidFlag(arg, allowedFlagsData);

            const nextAvailableCommand = checkCommand(allowedFlagsData, argv, index);

            if (nextAvailableCommand) {
                console.warn('It is not possible to use few commands. All commands and parameters will be ignored after second command');

                return;
            }
        });

        mode = modeValue;
    })
    .parse(process.argv);

const namespaces = commandLineParser.namespace;
const themes = commandLineParser.theme;
const pathToConfig = join(globalSettings.context, commandLineParser.config);

const namespaceJson = require(pathToConfig);
if (commandLineParser.info === true) {
    console.log('Namespaces with available themes:');
    namespaceJson.namespaces.forEach(namespaceConfig => {
        console.log(`- ${namespaceConfig.namespace}`);
        console.log(`  ${namespaceConfig.defaultTheme}`);
        if (namespaceConfig.themes.length) {
            namespaceConfig.themes.forEach(theme => console.log(`  ${theme}`));
        }
    });
    console.log('');
    process.exit();
}

module.exports = {
    mode,
    namespaces,
    themes,
    pathToConfig
};
