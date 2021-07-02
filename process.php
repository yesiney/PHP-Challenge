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

    $line = fgetcsv($myfile, 0, ";");
    if (is_array($line)) {
        foreach ($line as $key => $value) {
            if (!isset($value)) {
                echo "$key empty" . PHP_EOL;
                exit(0);
            }
        }
    } else {
        exit(0);
    }
    $data = fgetcsv($myfile, 0, ";");
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            if (!isset($value)) {
                echo "$key empty" . PHP_EOL;
            }
        }
    } else {
        exit(0);
    }

    for ($i = 0; $i < 5; $i++) {
        if ($i == 2) {
            $xml->writeElement($line[2], setDate($data[2]));
        } elseif ($i == 3) {
            $xml->writeElement($line[3], setDate($data[3]));
        } else {
            $xml->writeElement($line[$i], $data[$i]);
        }
    }
    $xml->endElement();

    $line = fgetcsv($myfile, 0, ";");
    if (is_array($line)) {
        foreach ($line as $key => $value) {
            if (!isset($value)) {
                echo "$key empty" . PHP_EOL;
                exit(0);
            }
        }
    } else {
        exit(0);
    }

    $xml->startElement('lines');

    while ($data = fgetcsv($myfile, 0, ";")) {
        $checkNumber = substr($data[3], 1, 1);

        if (empty($data[1]) || $checkNumber != "," && $checkNumber != "") {
            continue;
        }
        $xml->startElement('line');
        for ($i = 0; $i < 8; $i++) {
            if ($i == 2) {
                $xml->writeElement($line[2], convertChars($data[2]));
            } elseif ($i == 7) {
                $xml->writeElement($line[7], setDateLatest($data[7]));
            } else {
                $xml->writeElement($line[$i], $data[$i]);
            }
        }
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
