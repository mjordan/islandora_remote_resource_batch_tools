<?php

/**
 * @file
 */

use League\Csv\Reader;

require_once __DIR__ . '/vendor/autoload.php';

$cmd = new Commando\Command();
$cmd->option('csv')
    ->require(true)
    ->describedAs('Path to the input CSV file.')
    ->file();
$cmd->option('template')
    ->require(true)
    ->describedAs('Path to the Twig template file.');
$cmd->option('output_dir')
    ->require(true)
    ->describedAs('Path to the output directory.');

$input_csv = Reader::createFromPath($cmd['csv']);
$records = $input_csv->fetchAssoc();

if (!file_exists($cmd['output_dir'])) {
    mkdir($cmd['output_dir']);
}

foreach ($records as $record) {
    print "Generating files for CSV record " . $record['ID'] . "\n";

    foreach ($record as $key => $value) {
        if ($key == 'OBJ') {
            $dest_path = $cmd['output_dir'] . DIRECTORY_SEPARATOR . $record['ID'] . '.txt';
            file_put_contents($dest_path, $record['OBJ']);
        }

        if ($key != 'ID' || $key!= 'OBJ') {
            if (!preg_match('/^dc_/', $key)) {
                $src_path = dirname($cmd['csv']) . DIRECTORY_SEPARATOR . $value;
                $dest_ext = pathinfo($value, PATHINFO_EXTENSION);
                $dest_path = $cmd['output_dir'] . DIRECTORY_SEPARATOR . $record['ID'] . '.' . $key . '.' . $dest_ext;
                if (file_exists($src_path)) {
                    copy($src_path, $dest_path);
                    // print "Dest path is $dest_path\n";
                }
                else {
                    print "Source file $value does not exist\n";
                }
            }
        }
    }

    $twig_loader = new \Twig_Loader_Filesystem(dirname($cmd['template']));
    $twig = new \Twig_Environment($twig_loader);
    $xml_from_template = $twig->render(basename($cmd['template']), $record);
    $output_file = $cmd['output_dir'] . DIRECTORY_SEPARATOR . $record['ID'] . '.DC.xml';
    file_put_contents($output_file, $xml_from_template);
}

print "Output is in " . $cmd['output_dir'] . "\n";
 
