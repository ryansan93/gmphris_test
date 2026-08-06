const puppeteer = require('puppeteer');

(async () => {

    console.log("Start Puppeteer...");

    const browser = await puppeteer.launch({
        headless: true
    });

    console.log("Browser berhasil dibuka");

    const page = await browser.newPage();

    console.log("Buka halaman...");

    await page.goto(
        'http://localhost/gmphris_test/hris/StrukturOrganisasi',
        {
            waitUntil: 'networkidle0',
            timeout: 60000
        }
    );

    console.log("Halaman berhasil dibuka");


    await page.pdf({
        path: 'struktur-organisasi.pdf',
        landscape: true,
        printBackground: true
    });

    console.log("PDF berhasil dibuat");


    await browser.close();

    console.log("Selesai");

})();