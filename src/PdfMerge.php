<?php

namespace Karriere\PdfMerge;

use Karriere\PdfMerge\Exceptions\FileNotFoundException;
use Karriere\PdfMerge\Exceptions\NoFilesDefinedException;
use TCPDI;

class PdfMerge
{
    /**
     * @var array
     */
    private $files = [];
    /**
     * @var TCPDI
     */
    private $pdf;

    /**
     * Passed parameters overrides settings for header and footer by calling tcpdf.php methods:
     * setHeaderData($ln='', $lw=0, $ht='', $hs='', $tc=array(0,0,0), $lc=array(0,0,0))
     * setFooterData($tc=array(0,0,0), $lc=array(0,0,0))
     * For more info about tcpdf, please read https://tcpdf.org/docs/
     *
     * @param array $headerConfig only values for keys 'ln', 'lw', 'ht', 'hs', 'tc', 'lc' are taken into account
     * @param array $footerConfig only values for keys 'tc', 'lc' are taken into account
     */
    public function __construct(array $headerConfig = [], array $footerConfig = [])
    {
        $this->pdf = new TCPDI();
        $this->configureHeaderAndFooter($headerConfig, $footerConfig);
    }

    /**
     * Adds a file to merge
     * @param string $file the file to merge
     * @return void
     * @throws FileNotFoundException when the given file does not exist
     */
    public function add(string $file): void
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException($file);
        }

        $this->files[] = $file;
    }

    /**
     * Checks if the given file is already registered for merging
     * @param string $file the file to check
     * @return bool
     */
    public function contains(string $file): bool
    {
        return in_array($file, $this->files);
    }

    /**
     * Resets the stored files
     * @return void
     */
    public function reset(): void
    {
        $this->files = [];
    }

    /**
     * Generates a merged PDF file from the already stored pdf files
     * @param string $outputFilename the file to write to
     * @return bool
     * @throws NoFilesDefinedException
     */
    public function merge(string $outputFilename): bool
    {
        if (count($this->files) === 0) {
            throw new NoFilesDefinedException();
        }

        foreach ($this->files as $file) {
            $pageCount = $this->pdf->setSourceFile($file);

            for ($i = 1; $i <= $pageCount; $i++) {
                $pageId = $this->pdf->ImportPage($i);
                $size = $this->pdf->getTemplateSize($pageId);

                $this->pdf->AddPage('P', $size);
                $this->pdf->useTemplate($pageId);
            }
        }

        $this->pdf->Output($outputFilename, 'F');

        return true;
    }

    private function configureHeaderAndFooter(array $headerConfig, array $footerConfig): void
    {
        if (count($headerConfig)) {
            $ln = $headerConfig['ln'] ?? '';
            $lw = $headerConfig['lw'] ?? 0;
            $ht = $headerConfig['ht'] ?? '';
            $hs = $headerConfig['hs'] ?? '';
            $tc = $headerConfig['tc'] ?? [0, 0, 0];
            $lc = $headerConfig['lc'] ?? [0, 0, 0];
            $this->pdf->setHeaderData($ln, $lw, $ht, $hs, $tc, $lc);
        }

        if (count($footerConfig)) {
            $tc = $footerConfig['tc'] ?? [0, 0, 0];
            $lc = $footerConfig['lc'] ?? [0, 0, 0];
            $this->pdf->setFooterData($tc, $lc);
        }
    }
}
