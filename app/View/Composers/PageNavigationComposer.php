<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Page Navigation Composer
 *
 * Provides navigation data for header and footer menus
 * Uses caching for better performance
 */
class PageNavigationComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view): void
    {
        // Cache navigation data for 1 hour
        $headerPages = Cache::remember('pages.header', 3600, function () {
            return Page::active()
                ->inHeader()
                ->ordered()
                ->get();
        });

        $footerPages = Cache::remember('pages.footer', 3600, function () {
            return Page::active()
                ->inFooter()
                ->ordered()
                ->get();
        });

        $view->with('headerPages', $headerPages);
        $view->with('footerPages', $footerPages);
    }
}
