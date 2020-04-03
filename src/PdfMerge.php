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
     */
    public function merge(string $outputFilename): bool
    {
        if (count($this->files) === 0) {
            throw new NoFilesDefinedException();
        }

        $pdf = new TCPDI();

        foreach ($this->files as $file) {
            $pageCount = $pdf->setSourceFile($file);

            for ($i = 1; $i <= $pageCount; $i++) {
                $pageId = $pdf->ImportPage($i);
                $size = $pdf->getTemplateSize($pageId);

                $pdf->AddPage('P', $size);
                $pdf->useTemplate($pageId);
            }
        }

        $pdf->Output($outputFilename, 'F');

        return true;
    }
}
