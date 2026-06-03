import puppeteer from 'puppeteer';

(async () => {
    console.log("Launching browser...");
    const browser = await puppeteer.launch({ 
        headless: false,
        executablePath: '/usr/bin/google-chrome',
        defaultViewport: null,
        args: ['--start-maximized']
    });
    
    const page = await browser.newPage();
    
    console.log("Navigating to login page...");
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle2' });
    
    // Login
    console.log("Logging in...");
    await page.type('input[type="email"]', 'admin@turnament.com', { delay: 50 });
    await page.type('input[type="password"]', 'password', { delay: 50 });
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2' }),
        page.click('button[type="submit"]')
    ]);
    
    // Navigate to tournament details
    console.log("Navigating to tournament details...");
    await page.goto('http://127.0.0.1:8000/tournaments/turnamen-tenis-meja-hut-ri-ke-80-demo01', { waitUntil: 'networkidle2' });
    
    // Click Participants Tab
    console.log("Clicking Participants tab...");
    await page.click('#tab-participants');
    await new Promise(r => setTimeout(r, 500)); // small visual pause
    
    // Add participant
    console.log("Entering new participant name...");
    await page.waitForSelector('input[name="name"]', { visible: true });
    await page.type('input[name="name"]', 'Pemain Visual Otomatis', { delay: 100 });
    
    console.log("Submitting...");
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2' }),
        page.click('form[action*="participants"] button[type="submit"]')
    ]);
    
    console.log("Participant successfully added! Keeping browser open.");
})();
