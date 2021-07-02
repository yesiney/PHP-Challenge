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

const TR_CHARS = array("ı", "ğ", "ü", "ş", "ö", "ç", "Ç", "Ş", "İ", "Ü", "Ö"); //turkish letters
const EN_CHARS = array("i", "g", "u", "s", "o", "c", "C", "S", "I", "U", "O"); //english cooridinators letters

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

const INPUT_PATH = __DIR__ . '/input';
const OUTPUT_PATH = __DIR__ . '/output';
const ARCHIVE_PATH = __DIR__ . '/archive';

const INPUT_EXT = '.txt';

//Get a list of file paths using the glob function.
$fileList = glob(INPUT_PATH . '/*' . INPUT_EXT);

if (false === $fileList) {
    echo "There is no file!" . PHP_EOL;
    exit(0);
}

//Loop through the array that glob returned.
foreach ($fileList as $filename) {
    echo "current : " . basename($filename) . PHP_EOL;

    $myfile = fopen($filename, "r");

    if (!$myfile) {
        echo "unable to read file!" . PHP_EOL;
        continue;
    }

    $outputFilename = "output-" . str_replace('.', '', (string)microtime(true)) . '.xml';
    $outputPath = OUTPUT_PATH . "/$outputFilename";

    $xml = new XMLWriter();
    $xml->openURI($outputPath);
    $xml->setIndent(true);

    $xml->startElement('order');
    $xml->startElement('header');

    function checkArray(bool $temp): void
    {
        if (false === $temp) {
            echo "There is no data!" . PHP_EOL;
            exit(0);
        }
    }

    $line = fgetcsv($myfile, 0, ";");
    array_map("checkArray", (array)$line);

    $data = fgetcsv($myfile, 0, ";");
    array_map("checkArray", (array)$data);


    $xml->writeElement($line[0], $data[0]);
    $xml->writeElement($line[1], $data[1]);
    $xml->writeElement($line[2], setDate($data[2]));
    $xml->writeElement($line[3], setDate($data[3]));
    $xml->writeElement($line[4], $data[4]);
    $xml->endElement();

    $line = fgetcsv($myfile, 0, ";");

    $xml->startElement('lines');

    while ($data = fgetcsv($myfile, 0, ";")) {
        $checkNumber = substr($data[3], 1, 1);

        if (empty($data[1]) || $checkNumber != "," && $checkNumber != "") {
            continue;
        }

        $xml->startElement('line');
        $xml->writeElement($line[0], $data[0]);
        $xml->writeElement($line[1], $data[1]);
        $xml->writeElement($line[2], convertChars($data[2]));
        $xml->writeElement($line[3], $data[3]);
        $xml->writeElement($line[4], $data[4]);
        $xml->writeElement($line[5], $data[5]);
        $xml->writeElement($line[6], $data[6]);
        $xml->writeElement($line[7], setDateLatest($data[7]));
        $xml->endElement();
    }

    $xml->endElement();
    $xml->endElement();

    fclose($myfile);

    echo "output created : $outputFilename" . PHP_EOL;

    if (file_exists(OUTPUT_PATH . "/$outputFilename")) {
        $success = rename("$filename", ARCHIVE_PATH . '/' . basename($filename));
        if ($success) {
            echo "Moved file!" . PHP_EOL;
        } else {
            echo "Failed to move file!" . PHP_EOL;
        }
    } else {
        echo "File does not exist" . PHP_EOL;
    }
}
