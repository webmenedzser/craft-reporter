const pkg = require('./package.json');

module.exports = {
    files: "composer.json",
    from: '/"version": "(.*?)"/g',
    to: '"version": "' + pkg.version + '"',
    isRegex: true,
    dry: false
}
