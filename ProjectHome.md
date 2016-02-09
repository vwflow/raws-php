# Summary #
Rambla Web Services (RAWS) is the common denominator for a number of web services which expose a programmatic interface to different parts of [Rambla's CDN](http://www.rambla.eu) and related services.
  * **Rambla Storage Service (= RASS)** : access to the CDN.
  * **Rambla META Service (= META)** : get/set metadata.
  * **Rambla Transcoding Service (= RATS)** : media transcoding engine with extended functionality (import, export, notification...).
  * **Rambla Monitoring Service (= RAMS)** : retrieval of statistic data about media items and end-user traffic.
  * **Rambla Stream Enabler (= RASE)** : automates the set up of live streams.

Please note that you must have a Rambla account before you can use these services. For more information, [contact Rambla](mailto:info@rambla.be) or see the [Rambla Wiki Pages](http://rampubwiki.wiki.rambla.be/RAWS).

The client libraries are currently being developed; new features will be added. If you need a certain feature that is not yet available, please let us know.

# Installation #
You can [checkout](http://code.google.com/p/raws-php/source/checkout) the latest version from the svn repository. It contains the 'raws\_json' client libraries and a directory with 'samples' that should help you get started using them.

To install the library, copy the 'raws\_json' directory to a location that is part of your PHP path. The library requires [cURL](http://php.net/manual/en/book.curl.php) support for PHP to be enabled. There are no further dependencies.