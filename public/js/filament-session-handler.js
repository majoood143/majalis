/**
 * Filament Session Expiration Handler
 *
 * This script handles session expiration detection for Filament panels.
 * It listens for 419 responses (session expired) from Livewire requests
 * and automatically redirects users to the appropriate login page.
 *
 * Features:
 * - Detects 419 (Session Expired) responses
 * - Detects 401 (Unauthorized) responses
 * - Shows user-friendly notification before redirect
 * - Works with both admin and owner panels
 *
 * @package Majalis
 */

document.addEventListener('DOMContentLoaded', function () {
    // Store original fetch for intercepting responses
    const originalFetch = window.fetch;

    /**
     * Intercept fetch requests to detect session expiration
     */
    window.fetch = async function (...args) {
        try {
            const response = await originalFetch.apply(this, args);

            // Check for session expiration (419) or unauthorized (401)
            if (response.status === 419 || response.status === 401) {
                // Check for our custom header
                if (response.headers.get('X-Filament-Session-Expired') === 'true') {
                    handleSessionExpired(response);
                    return response;
                }

                // Also handle generic 419 errors (CSRF token mismatch / session expired)
                if (response.status === 419) {
                    handleSessionExpired(response);
                    return response;
                }
            }

            return response;
        } catch (error) {
            throw error;
        }
    };

    /**
     * Handle session expiration
     *
     * @param {Response} response - The fetch response object
     */
    async function handleSessionExpired(response) {
        let redirectUrl = null;

        try {
            // Try to parse redirect URL from response
            const data = await response.clone().json();
            redirectUrl = data.redirect;
        } catch (e) {
            // If parsing fails, determine redirect based on current URL
            redirectUrl = getLoginUrlFromCurrentPath();
        }

        // Show notification if Filament notifications are available
        showSessionExpiredNotification();

        // Redirect after a short delay to allow notification to show
        setTimeout(() => {
            window.location.href = redirectUrl || getLoginUrlFromCurrentPath();
        }, 1500);
    }

    /**
     * Determine the login URL based on the current path
     *
     * @returns {string} The appropriate login URL
     */
    function getLoginUrlFromCurrentPath() {
        const path = window.location.pathname;

        if (path.startsWith('/admin')) {
            return '/admin/login';
        }

        if (path.startsWith('/owner')) {
            return '/owner/login';
        }

        return '/login';
    }

    /**
     * Show a notification to the user about session expiration
     */
    function showSessionExpiredNotification() {
        // Try Filament notification system first
        if (window.Filament && window.Filament.notifications) {
            new FilamentNotification()
                .title('Session Expired')
                .body('Your session has expired. Redirecting to login page...')
                .warning()
                .send();
            return;
        }

        // Fallback: Create a simple notification overlay
        const overlay = document.createElement('div');
        overlay.id = 'session-expired-overlay';
        overlay.innerHTML = `
            <div style="
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            ">
                <div style="
                    background: white;
                    padding: 2rem;
                    border-radius: 0.5rem;
                    text-align: center;
                    max-width: 400px;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                ">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 48px; height: 48px; margin: 0 auto 1rem; color: #f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: #1f2937;">
                        Session Expired
                    </h3>
                    <p style="color: #6b7280; margin-bottom: 1rem;">
                        Your session has expired. Redirecting to login page...
                    </p>
                    <div style="
                        width: 100%;
                        height: 4px;
                        background: #e5e7eb;
                        border-radius: 2px;
                        overflow: hidden;
                    ">
                        <div style="
                            height: 100%;
                            background: #3b82f6;
                            animation: progress 1.5s ease-in-out;
                        "></div>
                    </div>
                </div>
            </div>
            <style>
                @keyframes progress {
                    from { width: 0%; }
                    to { width: 100%; }
                }
            </style>
        `;
        document.body.appendChild(overlay);
    }

    /**
     * Listen for Livewire response errors
     * This catches errors that might not go through fetch
     */
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, content }) => {
                if (status === 419 || status === 401) {
                    handleSessionExpired({ status });
                }
            });
        });
    });
});
