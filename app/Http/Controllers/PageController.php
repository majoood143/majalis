<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Page Controller
 *
 * Handles displaying static content pages to customers
 * Supports bilingual content with automatic locale detection
 */
class PageController extends Controller
{
    /**
     * Display the specified page.
     *
     * @param string $slug The page slug
     * @return View|RedirectResponse
     */
    public function show(string $slug): View|RedirectResponse
    {
        // Find the page by slug, only active pages
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Return the page view with localized content
        return view('pages.show', [
            'page' => $page,
            'title' => $page->title,
            'content' => $page->content,
            'metaTitle' => $page->metaTitle,
            'metaDescription' => $page->metaDescription,
        ]);
    }

    /**
     * Display the About Us page.
     *
     * @return View|RedirectResponse
     */
    public function aboutUs(): View|RedirectResponse
    {
        return $this->show('about-us');
    }

    /**
     * Display the Contact Us page.
     *
     * @return View|RedirectResponse
     */
    public function contactUs(): View|RedirectResponse
    {
        return $this->show('contact-us');
    }

    /**
     * Display the Terms and Conditions page.
     *
     * @return View|RedirectResponse
     */
    public function terms(): View|RedirectResponse
    {
        return $this->show('terms-and-conditions');
    }

    /**
     * Display the Privacy Policy page.
     *
     * @return View|RedirectResponse
     */
    public function privacy(): View|RedirectResponse
    {
        return $this->show('privacy-policy');
    }
}
