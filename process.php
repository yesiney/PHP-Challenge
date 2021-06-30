<?php

const TR_CHARS = array("ı", "ğ", "ü", "ş", "ö", "ç", "Ç", "Ş", "İ", "Ü", "Ö"); //turkish letters
const EN_CHARS = array("i", "g", "u", "s", "o", "c", "C", "S", "I", "U", "O"); //english cooridinators letters

const INPUT_PATH = __DIR__ . '/input';
const OUTPUT_PATH = __DIR__ . '/output';
const ARCHIVE_PATH = __DIR__ . '/archive';

const INPUT_EXT = '.txt';

//Get a list of file paths using the glob function.
$fileList = glob(INPUT_PATH . '/*' . INPUT_EXT);

//Loop through the array that glob returned.
foreach ($fileList as $filename) {
    echo "current : " . basename($filename) . PHP_EOL;

    $myfile = fopen($filename, "r");

    if (!$myfile) {
        echo "unable to read file!" . PHP_EOL;
        continue;
    }

    $outputFilename = "output-" . str_replace('.', '', microtime(true)) . '.xml';
    $outputPath = OUTPUT_PATH . "/$outputFilename";

    $xml = new XMLWriter;
    $xml->openURI($outputPath);
    $xml->setIndent(true);

    $xml->startElement('order');
    $xml->startElement('header');

    $line = fgetcsv($myfile, 0, ";");

    $data = fgetcsv($myfile, 0, ";");

    $xml->writeElement($line[0], $data[0]);
    $xml->writeElement($line[1], $data[1]);
    $date = new DateTime($data[2]);
    $dateToSave = $date->format('Y-m-d H:i:s');
    $xml->writeElement($line[2], $dateToSave);
    $date = new DateTime($data[3]);
    $dateToSave = $date->format('Y-m-d H:i:s');
    $xml->writeElement($line[3], $dateToSave);
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
        $final_title = str_replace(TR_CHARS, EN_CHARS, $data[2]);
        $xml->writeElement($line[2], $final_title);
        $xml->writeElement($line[3], $data[3]);
        $xml->writeElement($line[4], $data[4]);
        $xml->writeElement($line[5], $data[5]);
        $xml->writeElement($line[6], $data[6]);
        $date = new DateTime($data[7]);
        $dateToSave = $date->format('ymd');
        $xml->writeElement($line[7], $dateToSave);
        $xml->endElement();
    }

    $xml->endElement();
    $xml->endElement();

    fclose($myfile);

    echo "output created : $outputFilename" . PHP_EOL;

    if (file_exists(OUTPUT_PATH . "/$outputFilename")) {
        echo"Fİle exist" . PHP_EOL;;
        $success = rename("$filename", ARCHIVE_PATH . '/' .basename($filename));
        if ($success){
            echo "Moved file!" . PHP_EOL;
        }
        else{
            echo "Failed to move file!" . PHP_EOL;
        }
    }
    else{
        echo "File does not exist" . PHP_EOL;
    }
}
