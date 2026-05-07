<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    public function render(string $html, string $filename = 'laporan.pdf', string $paper = 'A4', string $orientation = 'portrait')
    {
        $dompdf = $this->makeDompdf($html, $paper, $orientation);

        return $this->pdfResponse($dompdf->output(), $filename, true);
    }

    public function preview(string $html, string $filename = 'laporan.pdf', string $paper = 'A4', string $orientation = 'portrait')
    {
        $dompdf = $this->makeDompdf($html, $paper, $orientation);

        return $this->pdfResponse($dompdf->output(), $filename, false);
    }

    private function makeDompdf(string $html, string $paper, string $orientation): Dompdf
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Times New Roman');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        return $dompdf;
    }

    private function pdfResponse(string $pdfContent, string $filename, bool $download)
    {
        $disposition = $download ? 'attachment' : 'inline';
        $safeFilename = str_replace('"', '', $filename);

        return service('response')
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', $disposition . '; filename="' . $safeFilename . '"')
            ->setHeader('Content-Length', (string) strlen($pdfContent))
            ->setBody($pdfContent);
    }
}
