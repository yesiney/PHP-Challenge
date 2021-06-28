<!DOCTYPE html>
<html>

<body>

    <?php
    $myfile = fopen("./input/input.txt", "r") or die("Unable to open file!");

    $xml = new XMLWriter;
    $xml->openURI('file:///C:/xampp/htdocs/PHP-Challenge/output/output.xml');
    $xml->setIndent(true); // makes output cleaner

    $xml->startElement('order');
    $xml->startElement('header');

    $line = fgetcsv($myfile, 100,";");

    $data = fgetcsv($myfile, 100,";");
    
    $xml->writeElement($line[0], $data[0]);
    $xml->writeElement($line[1], $data[1]);
    $xml->writeElement($line[2], $data[2]);
    $xml->writeElement($line[3], $data[3]);
    $xml->writeElement($line[4], $data[4]);
    $xml->endElement();

    $line = fgetcsv($myfile, 1000,";");
    
    $xml->startElement('lines');
    // Convert each line into the local $data variable
    while ($data = fgetcsv($myfile, 100, ";")) {
        // Read the data from a single line
        $xml->startElement('line');
        
        $xml->writeElement($line[0], $data[0]);
        $xml->writeElement($line[1], $data[1]);
        $xml->writeElement($line[2], $data[2]);
        $xml->writeElement($line[3], $data[3]);
        $xml->writeElement($line[4], $data[4]);
        $xml->writeElement($line[5], $data[5]);
        $xml->writeElement($line[6], $data[6]);
        $xml->writeElement($line[7], $data[7]);
        $xml->endElement();

    }

    $xml->endElement();
    $xml->endElement();

    fclose($myfile);

    ?>

</body>

</html>