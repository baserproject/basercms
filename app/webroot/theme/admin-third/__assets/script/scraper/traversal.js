const puppeteer = require('puppeteer');
const fs = require('fs');
const util = require('util');

const config = require('./config.json');

const writeFile = util.promisify(fs.writeFile);
const outputDir = 'script/scraper/';
const ssDir = 'script/scraper/ss/';
const user = config.user;
const pass = config.pass;

const adminURL = 'http://localhost/admin';
const list = require('./pages.json');

const data = [];

(async (t) => {
	// ブラウザ起動
	const browser = await puppeteer.launch({ headless: true });
	const page = await browser.newPage();

	// ログイン
	const r = await page.goto(adminURL);
	const url = await page.url();
	console.log(`🚀 Login (user: ${user}, pass: xxxx) to ${url}`);
	await page.evaluate((user) => document.querySelector('[name="data[User][name]"]').value = user, user);
	await page.evaluate((pass) => document.querySelector('[name="data[User][password]"]').value = pass, pass);
	const $submit = await page.$('#BtnLogin');
	await $submit.click();
	await page.waitForNavigation();
	const newURL = await page.url();
	await page.screenshot({ path: `${ssDir}${encodeURIComponent(newURL.replace(/\//g, '_').replace(/:/g, '-'))}.png`, fullPage: true });
	console.log(`🎉 Logged in! ${newURL}`);

	// ページ解析
	for (const pageURL of list) {
		const info = await go(page, pageURL);
		data.push(info);
	}

	// 終了
	browser.close();

	// JSON
	await writeFile(`${outputDir}data.json`, JSON.stringify(data, null, '\t'), { encoding: 'utf-8' });

	// CSV
	const csv = data.map((datum) => {
		return '"' + [
			(datum.url || '').replace(/"/g, '\\"'),
			(datum.title || '').replace(/"/g, '\\"'),
			(datum.capture || '').replace(/"/g, '\\"'),
			datum.classNames.map(c => (c || '').replace(/"/g, '\\"')).join('\n')
		].join('", "') + '"';
	}).join('\n');

	await writeFile(`${outputDir}data.csv`, csv, { encoding: 'utf-8' });
})();

async function go (page, url) {
	process.stdout.write(`🔗 fetch "${url}" ...`);
	await page.goto(url, { waitUntil: 'load' });
	await page.setViewport({ width: 1400, height: 800 });
	process.stdout.write(` 🎉 fetched ... printing ...`);
	const capturePath = `${ssDir}${encodeURIComponent(url.replace(/\//g, '_').replace(/:/g, '-'))}.png`;
	await page.screenshot({ path: capturePath, fullPage: true });
	process.stdout.write(` 🎨 ${capturePath}\n`);
	const classNames = await scrapeClassNames(page);
	const title = await page.title();
	return {
		url,
		title,
		classNames,
		capture: capturePath,
	};
}

/**
 * すべてのタグからクラスを取得
 *
 * @param {Page} page
 */
async function scrapeClassNames (page) {
	/**
	 * @type {Set<string>}
	 */
	const cs = await page.evaluate(() => {
		const cs = [];
		for (const el of Array.from(document.querySelectorAll('*'))) {
			for (const className of Array.from(el.classList)) {
				cs.push(className);
			}
		};
		return cs;
	}, );
	return [...new Set(cs)].sort();
}
