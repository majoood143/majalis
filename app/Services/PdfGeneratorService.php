<?php

declare(strict_types=1);

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

/**
 * PDF Generator Service with Arabic/RTL Support
 *
 * This service handles PDF generation with proper support for:
 * - Arabic text rendering
 * - RTL (Right-to-Left) layout
 * - UTF-8 encoding
 * - Custom fonts embedding
 */
class PdfGeneratorService
{
    /**
     * Generate PDF from Blade view with Arabic support
     *
     * @param string $view Blade view name
     * @param array $data Data to pass to the view
     * @param string $filename Output filename
     * @param bool $download Whether to download or display inline
     * @return \Illuminate\Http\Response
     */
    public function generateFromView(
        string $view,
        array $data = [],
        string $filename = 'document.pdf',
        bool $download = true
    ): \Illuminate\Http\Response {
        // Configure PDF options for Arabic support
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isFontSubsettingEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans'); // Supports Arabic
        $options->set('enable_font_subsetting', true);
        $options->set('dpi', 96);
        $options->set('defaultMediaType', 'screen');
        $options->set('defaultPaperSize', 'a4');

        // Set encoding to UTF-8 for proper Arabic character support
        $options->set('defaultEncoding', 'UTF-8');

        // Initialize Dompdf with options
        $dompdf = new Dompdf($options);

        // Render the Blade view to HTML
        $html = View::make($view, $data)->render();

        // Ensure UTF-8 encoding
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        // Load HTML into Dompdf
        $dompdf->loadHtml($html, 'UTF-8');

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF (generates the PDF)
        $dompdf->render();

        // Output PDF
        return response($dompdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', ($download ? 'attachment' : 'inline') . '; filename="' . $filename . '"');
    }

    /**
     * Generate PDF and save to storage
     *
     * @param string $view Blade view name
     * @param array $data Data to pass to the view
     * @param string $path Storage path
     * @return string Path to saved file
     */
    public function generateAndSave(
        string $view,
        array $data = [],
        string $path = 'pdfs/document.pdf'
    ): string {
        // Configure PDF options
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isFontSubsettingEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('enable_font_subsetting', true);
        $options->set('defaultEncoding', 'UTF-8');

        // Initialize Dompdf
        $dompdf = new Dompdf($options);

        // Render view
        $html = View::make($view, $data)->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        // Generate PDF
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Save to storage
        $fullPath = storage_path('app/' . $path);

        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save PDF content
        file_put_contents($fullPath, $dompdf->output());

        return $path;
    }
}
