<?php

# src/Service/PdfGenerator.php
namespace App\Service;

use Knp\Snappy\Pdf;

class PdfGenerator
{
    private $pdf;

    public function __construct(Pdf $pdf)
    {
        $this->pdf = $pdf;
    }

    public function generateFromHtml(string $html)
    {
        return $this->pdf->getOutputFromHtml($html);
    }
}