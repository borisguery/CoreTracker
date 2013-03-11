CoreTracker
===========

Benchmarks
----------

1. Install dependencies

    ```
    $ composer install --dev
    ```

2. Run the benchmark script

    ```
    $ php benchmarks/bench.php -k 5000 10
    ```

3. Benchmark with APC using a web server

Run the benchmark script, when the iterations are done, it will copy the `autoload.php` and `coreload.php` to
the `public` folder.

Configure a web-server to access the `public` folder.

The scripts is provided with the `apc.php` which is a simple web gui, a `phpinfo.php` file to get the current php
configuration.

The script will return a prepared command like :

`siege -t2M -c50 http://core/513dedf64d1f8/autoload.php`

`siege -t2M -c50 http://core/513dedf64d1f8/coreload.php`

Restart your webserver between each sieges (the [`siege`](http://www.joedog.org/siege-home/) utility).
