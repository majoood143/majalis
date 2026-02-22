<?php

declare(strict_types=1);

namespace App\Services;

use Mpdf\Mpdf;
use Mpdf\Config\FontVariables;
use Mpdf\Config\ConfigVariables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

/**
 * PDF Export Service with Arabic/RTL Support (mPDF)
 *
 * Generates PDF documents using mPDF with full Arabic text support,
 * Tajawal font embedding, and proper RTL layout handling.
 *
 * CRITICAL NOTES FOR ARABIC RENDERING:
 * ────────────────────────────────────
 * 1. Font key MUST be lowercase ('tajawal') — mPDF normalizes CSS font-family
 *    names to lowercase internally, so the fontdata key must match.
 *
 * 2. useOTL = 0xFF enables OpenType Layout for ALL scripts. This is REQUIRED
 *    for Arabic letter shaping (initial/medial/final/isolated forms).
 *    Without it, Arabic letters appear disconnected.
 *
 * 3. Font cache in tempDir/ttfontdata/ must be cleared when changing fontdata
 *    config. Stale cache = old (broken) font metrics persist.
 *
 * 4. LTR content inside RTL (emails, URLs, numbers) must be wrapped with
 *    <span dir="ltr"> in the HTML template to prevent reversal.
 *
 * 5. Emoji characters (💰📈📅🏢📊) are NOT supported by mPDF — they produce
 *    empty boxes or corrupt the surrounding text. Use CSS icons instead.
 *
 * @package App\Services
 */
class PdfExportService
{
    /**
     * The mPDF instance.
     *
     * @var Mpdf
     */
    protected Mpdf $mpdf;

    /**
     * Create a new PdfExportService instance.
     *
     * Initializes mPDF with Tajawal font, Arabic OTL, and RTL direction.
     *
     * @param array $config Optional mPDF config overrides
     * @param bool $clearFontCache Force clear font cache (useful after config changes)
     */
    public function __construct(array $config = [], bool $clearFontCache = false)
    {
        // ------------------------------------------------------------------
        // Step 1: Load mPDF's DEFAULT font directories and font data.
        // We must preserve the defaults so DejaVu Sans (Arabic fallback) and
        // other built-in fonts remain available.
        // ------------------------------------------------------------------
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $defaultFontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $defaultFontData = $defaultFontConfig['fontdata'];

        // ------------------------------------------------------------------
        // Step 2: Define our custom font directory and verify font files.
        // ------------------------------------------------------------------
        $customFontDir = storage_path('fonts');
        $fontRegular = $customFontDir . '/Tajawal-Regular.ttf';
        $fontBold = $customFontDir . '/Tajawal-Bold.ttf';

        if (!file_exists($fontRegular)) {
            Log::error('PdfExportService: Tajawal-Regular.ttf NOT FOUND at: ' . $fontRegular);
        }
        if (!file_exists($fontBold)) {
            Log::error('PdfExportService: Tajawal-Bold.ttf NOT FOUND at: ' . $fontBold);
        }

        // ------------------------------------------------------------------
        // Step 3: Set temp directory for mPDF font cache.
        //
        // mPDF caches parsed font metrics in tempDir/ttfontdata/.
        // If you change fontdata config and DON'T clear this cache,
        // the old (broken) metrics persist and fonts won't render correctly.
        // ------------------------------------------------------------------
        $tempDir = storage_path('app/mpdf-tmp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Clear font cache if requested (after config changes)
        if ($clearFontCache) {
            $this->clearFontCache($tempDir);
        }

        // ------------------------------------------------------------------
        // Step 4: Build mPDF configuration.
        //
        // CRITICAL FIX: Use array_merge() for fontdata, NOT the '+' operator.
        //
        // PHP's '+' operator: $a + $b keeps $a's keys and ignores $b's
        //   duplicates. This means if mPDF defaults ever include 'tajawal',
        //   our config would be silently DROPPED.
        //
        // array_merge(): $b's keys overwrite $a's duplicates.
        //   This ensures our Tajawal config ALWAYS takes precedence.
        // ------------------------------------------------------------------
        $mpdfConfig = array_merge([
            // Core mode: UTF-8 for full Unicode support
            'mode'              => 'utf-8',
            'format'            => 'A4',
            'default_font'      => 'tajawal',
            'default_font_size' => 12,

            // RTL direction for Arabic layout
            'direction' => 'rtl',

            // Font directories: mPDF built-in + our custom path
            'fontDir' => array_merge($defaultFontDirs, [
                $customFontDir,
            ]),

            // Font data: merge Tajawal INTO defaults (array_merge, NOT '+')
            'fontdata' => array_merge($defaultFontData, [
                'tajawal' => [
                    'R'          => 'Tajawal-Regular.ttf',
                    'B'          => 'Tajawal-Bold.ttf',
                    'I'          => 'Tajawal-Regular.ttf',   // Italic → Regular fallback
                    'BI'         => 'Tajawal-Bold.ttf',      // Bold-Italic → Bold fallback
                    'useOTL'     => 0xFF,   // Enable ALL OpenType Layout features
                    'useKashida' => 75,     // Arabic Kashida justification (0-100%)
                ],
            ]),

            // Arabic text shaping (global level)
            'autoScriptToLang' => true,    // Auto-detect script → language tag
            'autoLangToFont'   => true,    // Auto-select font based on language
            'useOTL'           => 0xFF,    // Global OTL: enable for all scripts
            'useKashida'       => 75,      // Global Kashida justification

            // Temp directory for font cache and internal files
            'tempDir' => $tempDir,

            // Bidirectional text support
            'biDirectional' => true,
        ], $config);

        // ------------------------------------------------------------------
        // Step 5: Create the mPDF instance.
        // ------------------------------------------------------------------
        $this->mpdf = new Mpdf($mpdfConfig);

        // Reinforce Arabic processing flags on the instance
        $this->mpdf->autoScriptToLang = true;
        $this->mpdf->autoLangToFont = true;

        // Enable bidirectional text algorithm
        // This helps with mixed Arabic/English content (emails, URLs, etc.)
        $this->mpdf->biDirectional = true;

        // Set document metadata
        $this->mpdf->SetTitle('Majalis Report');
        $this->mpdf->SetAuthor(config('app.name', 'Majalis'));
    }

    /**
     * Generate PDF from a Blade view.
     *
     * Flow: Blade template → HTML string → mPDF → PDF binary
     *
     * @param string $view  Blade view name (e.g. 'pdf.reports.owner-dashboard')
     * @param array  $data  Data to pass to the Blade view
     * @return static For method chaining
     */
    public function generateFromView(string $view, array $data = []): static
    {
        $html = View::make($view, $data)->render();

        return $this->generateFromHtml($html);
    }

    /**
     * Generate PDF from raw HTML string.
     *
     * IMPORTANT: The HTML is passed to mPDF AS-IS. Do NOT:
     *   - Inject extra <head> tags (breaks document structure)
     *   - Add @font-face CSS (mPDF uses fontdata config, not CSS @font-face)
     *   - Include emoji characters (mPDF can't render them)
     *
     * @param string $html The complete HTML document string
     * @return static For method chaining
     */
    public function generateFromHtml(string $html): static
    {
        // Write HTML directly — NO wrapping, NO debug injection
        $this->mpdf->WriteHTML($html);

        return $this;
    }

    /**
     * Stream the PDF as a download response.
     *
     * @param string $filename The download filename (e.g. 'report.pdf')
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(
            fn() => print($this->mpdf->Output('', 'S')),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Get the raw PDF output as a binary string.
     *
     * @return string Raw PDF binary content
     */
    public function output(): string
    {
        return $this->mpdf->Output('', 'S');
    }

    /**
     * Save the PDF to a file on disk.
     *
     * @param string $path Full file path to save to
     * @return string The path where the file was saved
     */
    public function save(string $path): string
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->mpdf->Output($path, 'F');

        return $path;
    }

    /**
     * Save raw HTML to a debug file for browser inspection.
     *
     * @param string $html     The HTML content
     * @param string $filename The filename (e.g. 'report-debug.html')
     * @return string The full path where the file was saved
     */
    public function saveHtmlForDebug(string $html, string $filename): string
    {
        $path = storage_path('debug/' . $filename);

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $html);
        Log::info('PdfExportService: Debug HTML saved to ' . $path);

        return $path;
    }

    /**
     * Clear the mPDF font cache.
     *
     * MUST be called after changing fontdata configuration, otherwise
     * mPDF will use cached (stale) font metrics from the old config.
     *
     * The cache is stored in: {tempDir}/ttfontdata/
     *
     * @param string|null $tempDir Override temp directory path
     * @return void
     */
    public function clearFontCache(?string $tempDir = null): void
    {
        $cacheDir = ($tempDir ?? storage_path('app/mpdf-tmp')) . '/ttfontdata';

        if (!is_dir($cacheDir)) {
            return;
        }

        $files = glob($cacheDir . '/*');
        if ($files === false) {
            return;
        }

        $count = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }

        Log::info("PdfExportService: Cleared {$count} font cache files from {$cacheDir}");
    }

    /**
     * Get the underlying mPDF instance for advanced customization.
     *
     * @return Mpdf
     */
    public function getMpdf(): Mpdf
    {
        return $this->mpdf;
    }
}
