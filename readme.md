<a href="https://www.karriere.at/" target="_blank"><img width="200" src="http://www.karriere.at/images/layout/katlogo.svg"></a>
<span>&nbsp;&nbsp;&nbsp;</span>
![](https://github.com/karriereat/pdf-merge/workflows/test/badge.svg)
![](https://github.com/karriereat/pdf-merge/workflows/lint/badge.svg)

# Pdf Merge Solution based on Imagick

This package is a wrapper for the `Imagick` class that provides an elegant API for merging PDF files.

## Installation

You can install the package via composer:

```bash
composer require karriere/pdf-merge
```

## Usage

```php
$pdfMerge = new PdfMerge();

$pdfMerge->add('/path/to/file1.pdf');
$pdfMerge->add('/path/to/file2.pdf');

$pdfMerge->merge('/path/to/output.pdf');
```

Please note, that the `merge` method will throw an `NoFilesDefinedException` if no files where added.

### Setting custom Imagick Options
To be able to set options in the `Imagick` instance used you can pass an array on constructing the `PdfMerge` instance.

```php
$pdfMerge = new PdfMerge([
    'density' => '200',
]);
```

To find all possible options please see the [Imagick class documentation](https://www.php.net/manual/en/imagick.setoption.php).

### Check for file existence
You can check if a file was already added for merging by calling:

```php
$pdfMerge->contains('/path/to/file.pdf');
```

## License

Apache License 2.0 Please see [LICENSE](LICENSE) for more information.
