import puppeteer from 'puppeteer';

(async () => {
    // Launch browser in non-headless mode so the user can see it
    const browser = await puppeteer.launch({ 
        headless: false,
        executablePath: '/usr/bin/google-chrome',
        defaultViewport: null, // Full size
        args: ['--start-maximized']
    });
    
    const page = await browser.newPage();
    
    console.log("Navigating to login page...");
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle2' });
    
    console.log("Typing email...");
    await page.type('input[type="email"]', 'admin@turnament.com', { delay: 100 });
    
    console.log("Typing password...");
    await page.type('input[type="password"]', 'password', { delay: 100 });
    
    console.log("Clicking login button...");
    await page.click('button[type="submit"]');
    
    console.log("Login submitted! Keeping browser open for you to view.");
    
    // We intentionally do not close the browser so the user can continue using it.
    // The script will stay alive or exit, but the browser will remain open.
})();
