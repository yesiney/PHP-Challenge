<!DOCTYPE html>
<html>

<body>

    <?php

    $turkish = array("ı", "ğ", "ü", "ş", "ö", "ç", "Ç", "Ş", "İ", "Ü", "Ö"); //turkish letters
    $english   = array("i", "g", "u", "s", "o", "c", "C", "S", "I", "U", "O"); //english cooridinators letters

    //Get a list of file paths using the glob function.
    $fileList = glob('input/*');

    //Loop through the array that glob returned.
    foreach ($fileList as $filename) {
        if ($filename == "input/input.txt") {
            $myfile = fopen(__DIR__ . "./input/input.txt", "r") or die("Unable to open file!");
            break;
        }
    }

    $xml = new XMLWriter;
    $xml->openURI(__DIR__ . './output/output.xml');
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
        $final_title = str_replace($turkish, $english, $data[2]);
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

    ?>

</body>

</html>