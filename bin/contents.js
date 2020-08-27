const Apify = require('apify');
const fs = require('fs');
const readline = require('readline');

const args = process.argv.slice(2);

if(args[0] === undefined){
    console.log('node contents.js <urls.txt>');
    process.exit();
}

Apify.main(async () => {
    const requestQueue = await Apify.openRequestQueue();

    const fileStream = fs.createReadStream(args[0]);
    const rl = readline.createInterface({
        input: fileStream,
        crlfDelay: Infinity
    });

    for await (const line of rl) {
        await requestQueue.addRequest({ url: line });
    }

    const crawler = new Apify.PuppeteerCrawler({
        requestQueue,
        handlePageFunction: async ({ request, response, page }) => {
            const content = await page.content();

            await Apify.pushData({
                'url': request.url,
                'status': response.status(),
                'content': content,
            });
        },
        maxConcurrency: 10,
    });

    await crawler.run();
});