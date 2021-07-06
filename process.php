<?php

/**
 * Template File Doc Comment
 *
 * PHP version 7
 *
 * @category Template_Class
 * @package  Template_Class
 * @author   Author <tugce.erkan@map.com.tr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://localhost/
 */

//deneme
const TR_CHARS = array("ı", "ğ", "ü", "ş", "ö", "ç", "Ç", "Ş", "İ", "Ü", "Ö"); //turkish letters
const EN_CHARS = array("i", "g", "u", "s", "o", "c", "C", "S", "I", "U", "O"); //english cooridinators letters

const INPUT_PATH = __DIR__ . '/input';
const OUTPUT_PATH = __DIR__ . '/output';
const ARCHIVE_PATH = __DIR__ . '/archive';
const ERROR_PATH = __DIR__ . '/error';

const INPUT_EXT = '.txt';

$fileList = glob(INPUT_PATH . '/*' . INPUT_EXT);

if (false === $fileList) {
    echo "There is no file!" . PHP_EOL;
    exit(0);
}

foreach ($fileList as $filename) {
    echo "current : " . basename($filename) . PHP_EOL;

    $myfile = fopen($filename, "r");

    if (!$myfile) {
        echo "unable to read file!" . PHP_EOL;
        continue;
    }

    $outputFilename = "output-" . str_replace('.', '', (string)microtime(true)) . '.xml';
    $outputPath = OUTPUT_PATH . "/$outputFilename";

    $headerTitles = fgetcsv($myfile, 0, ";");

    if (false === $headerTitles || null === $headerTitles || null === $headerTitles[0]) {
        echo "Header titles is empty!" . PHP_EOL;
        moveFile(ERROR_PATH);
        continue;
    }

    $headerValues = fgetcsv($myfile, 0, ";");

    if (false === $headerValues || null === $headerValues || null === $headerValues[0]) {
        echo "Header values are empty!" . PHP_EOL;
        moveFile(ERROR_PATH);
        continue;
    }

    $detailTitles = fgetcsv($myfile, 0, ";");

    if (false === $detailTitles || null === $detailTitles || null === $detailTitles[0]) {
        echo "Detail titles are empty!" . PHP_EOL;
        moveFile(ERROR_PATH);
        continue;
    }

    $xml = new XMLWriter();
    $xml->openURI($outputPath);
    $xml->setIndent(true);

    $xml->startElement('order');
    $xml->startElement('header');

    for ($i = 0; $i < 5; $i++) {
        $xml->writeElement($headerTitles[$i], in_array($i, [2, 3]) ? setDate($headerValues[$i]) : $headerValues[$i]);
    }
    $xml->endElement();

    $xml->startElement('lines');

    while ($headerValues = fgetcsv($myfile, 0, ";")) {
        $checkNumber = substr($headerValues[3], 1, 1);

        if (empty($headerValues[1]) || $checkNumber != "," && $checkNumber != "") {
            continue;
        }

        $xml->startElement('line');

        for ($i = 0; $i < 8; $i++) {
            $xml->writeElement($detailTitles[$i], $i == 2 ? convertChars($headerValues[$i]) :
            ($i == 7 ? setDateLatest($headerValues[$i]) :
            $headerValues[$i]));
        }
        $xml->endElement();
    }

    $xml->endElement();
    $xml->endElement();

    fclose($myfile);

    echo "output created : $outputFilename" . PHP_EOL;

    if (file_exists(OUTPUT_PATH . "/$outputFilename")) {
        moveFile(ARCHIVE_PATH);
    } else {
        echo "File does not exist" . PHP_EOL;
    }
}

function convertChars(string $temp): string
{
    return str_replace(TR_CHARS, EN_CHARS, $temp);
}

function setDate(string $temp): string
{
    $date = new DateTime($temp);
    return $date->format('Y-m-d H:i:s');
}

function setDateLatest(string $temp): string
{
    $date = new DateTime($temp);
    return $date->format('ymd');
}

function moveFile(string $path): void
{
    global $filename;
    $success = rename("$filename", $path . '/' . basename($filename));
    if ($success) {
        echo "Moved file!" . PHP_EOL;
    } else {
        echo "Failed to move file!" . PHP_EOL;
    }
}
