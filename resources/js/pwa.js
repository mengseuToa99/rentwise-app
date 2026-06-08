/*
 * PWA bootstrap: registers the service worker and provides a custom
 * "Install app" affordance (beforeinstallprompt on Android/desktop, and a
 * Share-sheet hint on iOS where no install API exists).
 */

const DISMISS_KEY = 'rentwise-pwa-install-dismissed';
const isLocalhost = ['localhost', '127.0.0.1', '[::1]'].includes(location.hostname);
const isStandalone = () =>
    window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
const isIos = () => /iphone|ipad|ipod/i.test(window.navigator.userAgent);

// --- Service worker registration ----------------------------------------
function registerServiceWorker() {
    if (!('serviceWorker' in navigator)) return;
    if (location.protocol !== 'https:' && !isLocalhost) return; // SW requires a secure context

    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').then((reg) => {
            // When a new SW is installed and there's already a controller, activate it then reload once.
            reg.addEventListener('updatefound', () => {
                const sw = reg.installing;
                if (!sw) return;
                sw.addEventListener('statechange', () => {
                    if (sw.state === 'installed' && navigator.serviceWorker.controller) {
                        sw.postMessage('SKIP_WAITING');
                    }
                });
            });
        }).catch(() => {});

        let reloaded = false;
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (reloaded) return;
            reloaded = true;
            window.location.reload();
        });
    });
}

// --- Custom install UI ----------------------------------------------------
let deferredPrompt = null;

function buildBanner(innerHtml) {
    const bar = document.createElement('div');
    bar.id = 'pwa-install-banner';
    bar.style.cssText = [
        'position:fixed', 'left:50%', 'transform:translateX(-50%)', 'bottom:16px', 'z-index:2147483000',
        'display:flex', 'align-items:center', 'gap:12px',
        'max-width:calc(100vw - 24px)', 'padding:10px 12px 10px 14px',
        'background:#ffffff', 'color:#111827', 'border:1px solid #e5e7eb', 'border-radius:12px',
        'box-shadow:0 10px 25px rgba(0,0,0,0.15)', 'font-family:inherit', 'font-size:14px'
    ].join(';');
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        bar.style.background = '#18181b';
        bar.style.color = '#fafafa';
        bar.style.borderColor = '#3f3f46';
    }
    bar.innerHTML = innerHtml;
    document.body.appendChild(bar);
    return bar;
}

function dismiss(bar) {
    try { localStorage.setItem(DISMISS_KEY, '1'); } catch (e) {}
    bar.remove();
}

function showInstallButton() {
    if (document.getElementById('pwa-install-banner')) return;
    const bar = buildBanner(
        `<img src="/icons/icon-192.png" alt="" style="width:32px;height:32px;border-radius:6px">
         <span style="flex:1;min-width:0">Install RentWise for quick access</span>
         <button id="pwa-install-go" style="appearance:none;border:0;cursor:pointer;font:inherit;font-weight:600;background:#2563eb;color:#fff;padding:7px 14px;border-radius:8px">Install</button>
         <button id="pwa-install-x" aria-label="Dismiss" style="appearance:none;border:0;cursor:pointer;background:transparent;color:inherit;font-size:20px;line-height:1;padding:0 4px;opacity:.6">&times;</button>`
    );
    bar.querySelector('#pwa-install-x').addEventListener('click', () => dismiss(bar));
    bar.querySelector('#pwa-install-go').addEventListener('click', async () => {
        if (!deferredPrompt) { bar.remove(); return; }
        deferredPrompt.prompt();
        await deferredPrompt.userChoice.catch(() => {});
        deferredPrompt = null;
        bar.remove();
    });
}

function showIosHint() {
    if (document.getElementById('pwa-install-banner')) return;
    const bar = buildBanner(
        `<img src="/icons/icon-192.png" alt="" style="width:32px;height:32px;border-radius:6px">
         <span style="flex:1;min-width:0">Install: tap <strong>Share</strong> then <strong>Add to Home Screen</strong></span>
         <button id="pwa-install-x" aria-label="Dismiss" style="appearance:none;border:0;cursor:pointer;background:transparent;color:inherit;font-size:20px;line-height:1;padding:0 4px;opacity:.6">&times;</button>`
    );
    bar.querySelector('#pwa-install-x').addEventListener('click', () => dismiss(bar));
}

function initInstallUi() {
    let dismissed = false;
    try { dismissed = localStorage.getItem(DISMISS_KEY) === '1'; } catch (e) {}
    if (dismissed || isStandalone()) return;

    // Android / desktop Chromium: capture the prompt and show our own button.
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        showInstallButton();
    });

    window.addEventListener('appinstalled', () => {
        deferredPrompt = null;
        const bar = document.getElementById('pwa-install-banner');
        if (bar) bar.remove();
    });

    // iOS Safari has no prompt API — show the manual hint instead.
    if (isIos() && !isLocalhost) {
        window.addEventListener('load', () => setTimeout(showIosHint, 1200));
    }
}

registerServiceWorker();
initInstallUi();
