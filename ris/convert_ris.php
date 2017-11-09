<?php

/**
 * @file
 * Utility script to convert an RIS file into files suitable for using as input
 * to the Islandora Remote Resource Batch module.
 *
 * For help information, run php convert_ris.php --help
 */

use \LibRIS\RISReader;
use \LibRIS\RISTags;

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

$reference_types = new RISTags();
$reference_type_names = $reference_types::$typeMap;

$records = $input_ris->getRecords();

if (!file_exists($cmd['output_dir'])) {
    mkdir($cmd['output_dir']);
}

$record_num = 0;
foreach ($records as $record) {
    $record_num++;

    print "Generating files for RIS record " . $record_num . "\n";
    if (array_key_exists('UR', $record)) {
        $dest_path = $cmd['output_dir'] . DIRECTORY_SEPARATOR . $record_num . '.txt';
        file_put_contents($dest_path, $record['UR']);
    }
    else {
        print "Record $record_num has no URL, skipping\n";
        continue;
    }

    // Convert abbreviated types to full type names.
    foreach ($record['TY'] as &$type) {
        $type = $reference_type_names[$type];
    }

    // Convert yyyy/mm/dd dates to yyyy-mm-dd dates.
    if (array_key_exists('DA', $record)) {
        foreach ($record['DA'] as &$date) {
            $date = preg_replace('#/#', '-', $date);
        }
    }

    $twig_loader = new \Twig_Loader_Filesystem(dirname($cmd['template']));
    $twig = new \Twig_Environment($twig_loader);
    $xml_from_template = $twig->render(basename($cmd['template']), $record);
    $dsid = strtoupper(rtrim($cmd['metadata_prefix'], '_'));
    $output_file = $cmd['output_dir'] . DIRECTORY_SEPARATOR . $record_num . '.' . $dsid . '.xml';
    file_put_contents($output_file, $xml_from_template);
}

print "Output is in " . $cmd['output_dir'] . "\n";
 
