<?php

/**
 * @file
 * Utility script to convert a CSV file into files suitable for using as input
 * to the Islandora Remote Resource Batch module.
 *
 * For help information, run php convert_csv.php --help
 */

use League\Csv\Reader;

require_once __DIR__ . '/vendor/autoload.php';

$cmd = new Commando\Command();
$cmd->option('csv')
    ->require(true)
    ->describedAs('Path to the input CSV file.')
    ->file();
$cmd->option('template')
    ->default('templates/DC.twig')
    ->describedAs('Optional. Path to the Twig template file. Defaults to "templates/DC.twig".');
$cmd->option('output_dir')
    ->require(true)
    ->describedAs('Path to the output directory.');
$cmd->option('metadata_prefix')
    ->default('dc_')
    ->describedAs('Optional. Path to the output directory. Defaults to "dc_".');

$input_csv = Reader::createFromPath($cmd['csv']);
$records = $input_csv->fetchAssoc();

if (!file_exists($cmd['output_dir'])) {
    mkdir($cmd['output_dir']);
}

foreach ($records as $record) {
    print "Generating files for CSV record " . $record['ID'] . "\n";

    foreach ($record as $key => $value) {
        // First, create the .txt file that contains the URL to the remote resource.
        if ($key == 'OBJ') {
            $dest_path = $cmd['output_dir'] . DIRECTORY_SEPARATOR . $record['ID'] . '.txt';
            file_put_contents($dest_path, $record['OBJ']);
        }

        // Copy files to the output directory.
        $metadata_prefix_pattern = '/^' . $cmd['metadata_prefix'] . '/';
        if ($key == 'ID' || $key == 'OBJ') {
            continue;
        }
        if (!preg_match($metadata_prefix_pattern, $key)) {
            $src_path = dirname($cmd['csv']) . DIRECTORY_SEPARATOR . $value;
            $dest_ext = pathinfo($value, PATHINFO_EXTENSION);
            $dest_path = $cmd['output_dir'] . DIRECTORY_SEPARATOR . $record['ID'] . '.' . $key . '.' . $dest_ext;
            if (file_exists($src_path)) {
                copy($src_path, $dest_path);
            }
            else {
                print "Source file $value does not exist\n";
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
 
