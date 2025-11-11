const TARTEAUCITRON_CDN = 'https://cdn.jsdelivr.net/npm/tarteaucitronjs@1.15.0/tarteaucitron.min.js';

interface TarteaucitronGlobal extends Record<string, unknown> {
    init: (options: Record<string, unknown>) => void;
    job: string[];
    user: Record<string, unknown>;
}

declare global {
    interface Window {
        tarteaucitron?: TarteaucitronGlobal;
        tarteaucitronForceLanguage?: string;
        tarteaucitronCustomText?: Record<string, string>;
        dataLayer?: unknown[];
    }
}

const loadTarteaucitron = (): Promise<void> => {
    if (typeof window === 'undefined') {
        return Promise.resolve();
    }

    if (window.tarteaucitron) {
        return Promise.resolve();
    }

    return new Promise((resolve, reject) => {
        const existingScript = document.querySelector<HTMLScriptElement>(`script[src="${TARTEAUCITRON_CDN}"]`);

        if (existingScript) {
            existingScript.addEventListener('load', () => resolve(), { once: true });
            existingScript.addEventListener('error', () => reject(new Error('Unable to load tarteaucitron.js')), { once: true });
            return;
        }

        const script = document.createElement('script');
        script.src = TARTEAUCITRON_CDN;
        script.async = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Unable to load tarteaucitron.js'));
        document.head.appendChild(script);
    });
};

const initTarteaucitron = () => {
    if (!window.tarteaucitron) {
        return;
    }

    window.dataLayer = window.dataLayer || [];

    window.tarteaucitronForceLanguage = document.documentElement.lang || 'en';

    window.tarteaucitronCustomText = {
        alertBigPrivacy: 'We value your privacy and only activate optional cookies after your consent.',
        acceptAll: 'Accept all cookies',
        personalize: 'Manage preferences',
        close: 'Close banner',
        moreInfo: 'Learn more',
        disclaimer: 'Some services need your consent to load. Adjust your preferences whenever you like.',
        readmoreLink: 'View our cookie policy',
    };

    window.tarteaucitron.init({
        privacyUrl: '/privacy-policy',
        hashtag: '#cookies',
        cookieName: 'levelup-cookies',
        orientation: 'bottom',
        groupServices: false,
        showAlertSmall: false,
        cookieslist: true,
        showIcon: false,
        adblocker: false,
        DenyAllCta: true,
        AcceptAllCta: true,
        highPrivacy: true,
        handleBrowserDNTRequest: true,
        removeCredit: true,
        moreInfoLink: true,
        mandatory: true,
        bodyPosition: 'bottom',
        readmoreLink: '/privacy-policy',
        serviceDefaultState: 'wait',
    });

    window.tarteaucitron.user.googletagmanagerId = 'GTM-TGD3JTXZ';

    const services = ['googletagmanager'];
    const job = Array.isArray(window.tarteaucitron.job) ? window.tarteaucitron.job : [];

    services.forEach((service) => {
        if (!job.includes(service)) {
            job.push(service);
        }
    });

    window.tarteaucitron.job = job;
};

export const setupCookieConsent = async () => {
    if (typeof window === 'undefined') {
        return;
    }

    try {
        await loadTarteaucitron();
        initTarteaucitron();
    } catch (error) {
        console.error(error);
    }
};

if (typeof window !== 'undefined') {
    void setupCookieConsent();
}

export default setupCookieConsent;
