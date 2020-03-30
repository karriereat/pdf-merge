<?php

namespace Karriere\PdfMerge\Tests;

use Karriere\PdfMerge\Exceptions\FileNotFoundException;
use Karriere\PdfMerge\Exceptions\NoFilesDefinedException;
use Karriere\PdfMerge\PdfMerge;
use PHPUnit\Framework\TestCase;
use TCPDI;

class PdfMergeTest extends TestCase
{
    /** @test */
    public function it_fails_on_adding_a_not_existing_file()
    {
        $this->expectException(FileNotFoundException::class);
        $pdfMerge = new PdfMerge();

        $pdfMerge->add('/foo.pdf');
    }

    /** @test */
    public function it_can_check_if_a_file_was_already_added()
    {
        $pdfMerge = new PdfMerge();
        $file = __DIR__ . '/files/dummy.pdf';

        $this->assertFalse($pdfMerge->contains($file));
        $pdfMerge->add($file);
        $this->assertTrue($pdfMerge->contains($file));
    }

    /** @test */
    public function it_can_reset_the_files_to_merge()
    {
        $pdfMerge = new PdfMerge();
        $file = __DIR__ . '/files/dummy.pdf';
        $pdfMerge->add($file);
        $pdfMerge->reset();

        $this->assertFalse($pdfMerge->contains($file));
    }

    /** @test */
    public function it_can_generate_a_merged_file()
    {
        $pdfMerge = new PdfMerge();
        $file = __DIR__ . '/files/dummy.pdf';
        $outputFile = sys_get_temp_dir() . '/output.pdf';
        $outputFile = __DIR__ . '/output.pdf';

        $pdfMerge->add($file);
        $pdfMerge->add($file);

        $this->assertTrue($pdfMerge->merge($outputFile));
        $this->assertPDFEquals(__DIR__ . '/files/expected/output.pdf', $outputFile);
    }

    /** @test */
    public function it_fails_on_generate_when_no_files_were_added()
    {
        $this->expectException(NoFilesDefinedException::class);

        $pdfMerge = new PdfMerge();
        $pdfMerge->merge('/foo.pdf');
    }

    private static function assertPDFEquals(string $expected, string $actual): void
    {
        self::assertEquals(
            filesize($expected),
            filesize($actual),
            'The file size of the PDF does not equal the file size from the expected output.'
        );

        $pdf = new TCPDI();

        $expectedPageCount = $pdf->setSourceFile($expected);
        $actualPageCount = $pdf->setSourceFile($actual);

        self::assertEquals(
            $expectedPageCount,
            $actualPageCount,
            'The page count of the PDF does not equal the page count from the expected output.'
        );
    }
}
