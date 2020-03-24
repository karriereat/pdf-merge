<?php

namespace Karriere\PdfMerge;

use Karriere\PdfMerge\Exceptions\FileNotFoundException;
use Karriere\PdfMerge\Exceptions\NoFilesDefinedException;

class PdfMerge
{
    /**
     * @var array
     */
    private $files = [];

    /**
     * @var array
     */
    private $imagickOptions;

    /**
     * Creates a new PdfMerge instance
     * @param array $imagickOptions additional options for Imagick
     */
    public function __construct(array $imagickOptions = [])
    {
        $this->imagickOptions = $imagickOptions;
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
     */
    public function generate(string $outputFilename): bool
    {
        if (count($this->files) === 0) {
            throw new NoFilesDefinedException();
        }

        $pdf = new \Imagick();

        foreach ($this->imagickOptions as $key => $value) {
            $pdf->setOption($key, $value);
        }

        foreach ($this->files as $file) {
            $pdf->readImage($file);
        }

        $pdf->setImageFormat('pdf');

        return $pdf->writeImages($outputFilename, true);
    }
}
