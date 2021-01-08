<?php

namespace App\Manager;

use App\Entity\Offer;
use App\Entity\Resume;
use Spatie\PdfToText\Pdf;

class ResumeManager
{
    public const PDFTOTEXT_BIN_PATH = '/usr/bin/pdftotext';

    public function extractKeywords(Resume $resume): array
    {
        // An error will be thrown if cv file is not readable, make sure to upload valid cv for current user and to remove fake ones
        $text = (new Pdf(self::PDFTOTEXT_BIN_PATH))
            ->setPdf('uploads/' . $resume->getCv())
            ->text();

        $result = [];

        foreach(Offer::KEYWORDS as $keyword) {
            if (str_contains(strtolower($text), $keyword)) $result[] = $keyword;
        }

        return $result;
    }
}