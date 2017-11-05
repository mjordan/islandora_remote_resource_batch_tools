# BagIt Indexer


## System requirements and installation

To install and run this proof of concept indexer, you will need:

* PHP 5.5.0 or higher command-line interface
* [Composer](https://getcomposer.org)

To install the Bagit Indexer:

* Clone the Git repo
* `cd bagit_indexer`
* `php composer.phar install` (or equivalent on your system, e.g., `./composer install`)

## Running the tool

`php convert_csv.php --csv sample_data/test.csv --template templates/DC.twig`

## License

![This work is in the Public Domain](http://i.creativecommons.org/p/mark/1.0/88x31.png)

To the extent possible under law, Mark Jordan has waived all copyright and related or neighboring rights to this work. This work is published from: Canada. 

## Contributing

Since this is proof-of-concept code, I don't intend to add a lot more features. However, this proof of concept could be used as the basis for a production application. Fork and enjoy!

That said, if you have any questions or suggestions, feel free to open an issue.
