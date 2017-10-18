const data = require('./data.json');
const fs = require('fs');
const util = require('util');

const outputDir = 'script/scraper/';

const writeFile = util.promisify(fs.writeFile);

const whiteList = require('./pages.json');

const cssCollection = {};

for (const datum of data) {
	if (!whiteList.includes(datum.url)) {
		continue;
	}
	for (const className of datum.classNames) {
		if (!cssCollection[className]) {
			cssCollection[className] = [];
		}
		cssCollection[className].push(datum.url);
	}
}

for (const className of Object.keys(cssCollection)) {
	if (cssCollection[className].length === whiteList.length) {
		cssCollection[className] = ['all'];
	}
}

writeFile(`${outputDir}css.json`, JSON.stringify(cssCollection, null, '\t')).then(() => console.log(`🎉 Output: "${outputDir}css.json"`));
