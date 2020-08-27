const Apify = require('apify');

const args = process.argv.slice(2);

if(args[0] === undefined || args[1] === undefined){
    console.log('node crawler.js <url> <max>');
    process.exit();
}

Apify.main(async () => {
    const requestQueue = await Apify.openRequestQueue();
    await requestQueue.addRequest({ url: args[0] });
    await requestQueue.addRequest({ url: args[0] + '/robots.txt' });
    const pseudoUrls = [new Apify.PseudoUrl(args[0] + '[.*]')];

    const crawler = new Apify.PuppeteerCrawler({
        requestQueue,
        handlePageFunction: async ({ request, response, page }) => {
            const content = await page.content();

            await Apify.pushData({
                'url': request.url,
                'status': response.status(),
                'content': content,
            });

            await Apify.utils.enqueueLinks({
                page,
                selector: 'a',
                pseudoUrls,
                requestQueue,
            });
        },
        maxRequestsPerCrawl: parseInt(args[1]),
        maxConcurrency: 10,
    });

    await crawler.run();
});