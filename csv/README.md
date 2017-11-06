# Islandora Remote Resource CSV Conversion Tool

Utility script to convert a CSV file into files suitable for using as input to the Islandora Remote Resource Batch module.

## System requirements and installation

To install and run this proof of concept indexer, you will need:

* PHP 5.5.0 or higher command-line interface
* [Composer](https://getcomposer.org)

To install the Bagit Indexer:

* Clone the Git repo
* `cd islandora_remote_resource_batch_csv_tool`
* `php composer.phar install` (or equivalent on your system, e.g., `./composer install`)

## Running the tool

`php convert_csv.php --csv sample_data/test.csv --output_dir /tmp/output

```
--help
     Show the help page for this command.

--csv <argument>
     Required. Path to the input CSV file.

--output_dir <argument>
     Required. Path to the output directory.

--template <argument>
     Required. Path to the Twig template file.

--metadata_prefix <argument>
     Optional. Path to the output directory. Defaults to "dc_".
```

## The CSV file

Reserved column headings: `ID` and `OBJ`.

Headings for columns that contain values to be inserted into the metadata template should have a "metadata prefix". This defaults to `dc_`.

Headings for columns that contain filenames that are to be copied into the output directory for loading as datastreams should contains datastream IDs, e.g., `TN`.

## The metadata template

Twig.

## Maintainer

* [Mark Jordan](https://github.com/mjordan)

## Development and feedback

See the README in [Islandora Remote Resource Solution Pack](https://github.com/mjordan/islandora_solution_pack_remote_resource).

## License

* [GPLv3](http://www.gnu.org/licenses/gpl-3.0.txt)
