/**
 * Filament Session Expiration Handler
 *
 * Intercepts 419 (CSRF/session expired) and 401 (unauthenticated) responses
 * from Livewire/fetch and redirects to the correct panel login page without
 * ever rendering the raw Laravel error page.
 */

document.addEventListener('DOMContentLoaded', function () {

    /**
     * Determine the login URL from the current browser path.
     */
    function getLoginUrl() {
        const path = window.location.pathname;
        if (path.startsWith('/admin')) return '/admin/login';
        if (path.startsWith('/owner')) return '/owner/login';
        return '/login';
    }

    /**
     * Show an overlay and redirect after a short delay.
     */
    function handleSessionExpired(redirectUrl) {
        // Guard against firing multiple times (e.g. concurrent polling requests)
        if (document.getElementById('session-expired-overlay')) return;

        const loginUrl = redirectUrl || getLoginUrl();

        showOverlay();

        setTimeout(() => {
            window.location.href = loginUrl;
        }, 1500);
    }

    /**
     * Render a full-screen overlay with a progress bar.
     */
    function showOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'session-expired-overlay';
        overlay.innerHTML = `
            <div style="
                position:fixed;inset:0;background:rgba(0,0,0,.5);
                display:flex;align-items:center;justify-content:center;z-index:9999;
            ">
                <div style="
                    background:#fff;padding:2rem;border-radius:.5rem;
                    text-align:center;max-width:400px;width:90%;
                    box-shadow:0 25px 50px -12px rgba(0,0,0,.25);
                ">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         style="width:48px;height:48px;margin:0 auto 1rem;color:#f59e0b;"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                                 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34
                                 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h3 style="font-size:1.25rem;font-weight:600;margin-bottom:.5rem;color:#1f2937;">
                        Session Expired
                    </h3>
                    <p style="color:#6b7280;margin-bottom:1rem;">
                        Your session has expired. Redirecting to login&hellip;
                    </p>
                    <div style="width:100%;height:4px;background:#e5e7eb;border-radius:2px;overflow:hidden;">
                        <div style="height:100%;background:#3b82f6;animation:filament-progress 1.5s ease-in-out forwards;"></div>
                    </div>
                </div>
            </div>
            <style>
                @keyframes filament-progress { from{width:0} to{width:100%} }
            </style>
        `;
        document.body.appendChild(overlay);
    }

    // -------------------------------------------------------------------------
    // 1. Intercept raw fetch — catches requests made outside Livewire
    // -------------------------------------------------------------------------
    const originalFetch = window.fetch;

    window.fetch = async function (...args) {
        const response = await originalFetch.apply(this, args);

        if (response.status === 419 || response.status === 401) {
            let redirectUrl = null;

            try {
                const data = await response.clone().json();
                redirectUrl = data.redirect ?? null;
            } catch (_) {
                // Non-JSON 419 (e.g. full-page HTML) — fall back to path-based URL
            }

            handleSessionExpired(redirectUrl);
        }

        return response;
    };

    // -------------------------------------------------------------------------
    // 2. Livewire v3 request hook — catches Livewire component requests
    //    preventDefault() stops Livewire rendering the error in the component.
    // -------------------------------------------------------------------------
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, content, preventDefault }) => {
                if (status === 419 || status === 401) {
                    preventDefault(); // stop Livewire's default error handling

                    let redirectUrl = null;

                    try {
                        const data = JSON.parse(content);
                        redirectUrl = data.redirect ?? null;
                    } catch (_) {
                        // content is not JSON — use path-based fallback
                    }

                    handleSessionExpired(redirectUrl);
                }
            });
        });
    });
});
