<?php

/**
 * @file
 * Utility script to convert an RIS file into files suitable for using as input
 * to the Islandora Remote Resource Batch module.
 *
 * For help information, run php convert_ris.php --help
 */

use \LibRIS\RISReader;

require_once __DIR__ . '/vendor/autoload.php';

$cmd = new Commando\Command();
$cmd->option('ris')
    ->require(true)
    ->describedAs('Path to the input RIS file.')
    ->file();
$cmd->option('template')
    ->default('templates/DC.twig')
    ->describedAs('Optional. Path to the Twig template file. Defaults to "templates/DC.twig".');
$cmd->option('output_dir')
    ->require(true)
    ->describedAs('Path to the output directory.');
$cmd->option('metadata_prefix')
    ->default('dc_')
    ->describedAs('Optional. Prefix used in the CSV column heading for metadata values. Also used in the metadata XML filenames. Defaults to "dc_".');

$input_ris = new RISReader();
$records = $input_ris->parseFile($cmd['ris']);

$records = $input_ris->getRecords();

if (!file_exists($cmd['output_dir'])) {
    mkdir($cmd['output_dir']);
}

$record_num = 0;
foreach ($records as $record) {
    $record_num++;
    // print "Generating files for CSV record " . $record['ID'] . "\n";

/*
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
        // Other datastreams, e.g. TN.
        if (!preg_match($metadata_prefix_pattern, $key)) {
            $src_path = dirname($cmd['ris']) . DIRECTORY_SEPARATOR . $value;
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
*/

    $twig_loader = new \Twig_Loader_Filesystem(dirname($cmd['template']));
    $twig = new \Twig_Environment($twig_loader);
    $xml_from_template = $twig->render(basename($cmd['template']), $record);
    $dsid = strtoupper(rtrim($cmd['metadata_prefix'], '_'));
    $output_file = $cmd['output_dir'] . DIRECTORY_SEPARATOR . $record_num . '.' . $dsid . '.xml';
    file_put_contents($output_file, $xml_from_template);
}

print "Output is in " . $cmd['output_dir'] . "\n";
 
