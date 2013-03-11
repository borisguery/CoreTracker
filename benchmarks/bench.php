#!/usr/bin/env php
<?php

function usage($appName) {
    printf("usage: %s [-k] [classCount] [iterations]\n", $appName);
}

if ($argc<3) {
    usage($argv[0]);
    exit(-1);
} elseif (4===$argc) {
    $appName = array_shift($argv);
    if ('-k' !== ($option = array_shift($argv))) {
        printf("Unknown option: %s\n", $option);
        usage($appName);
        exit(-1);
    }

    list(, , $classCount, $iterations) = $argv;
    $keepFiles = true;
} else {
    $keepFiles = false;
    list(, $classCount, $iterations) = $argv;
}


$file = __DIR__.'/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies to run the benchmark.');
}

require_once __DIR__ . '/../vendor/symfony/class-loader/Symfony/Component/ClassLoader/ClassCollectionLoader.php';

$classTemplate =<<<EOF
<?php

class %className%
{}

EOF;

$tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid();
mkdir($tempDir, 0777, true);

printf("Generating %d classes in %s", $classCount, $tempDir);

$classNames = array();
for($total = $classCount;$classCount--;) {

    $className = 'BgyCoreTrackerBenchMark' . uniqid();
    $classNames[] = $className;

    file_put_contents(
        $tempDir . DIRECTORY_SEPARATOR . $className . '.php',
        str_replace('%className%', $className, $classTemplate)
    );

    printf("\rGenerating %d/%d classes in %s (%d%%)", $total - $classCount, $total, $tempDir, 100 - 100 * $classCount / $total );
}

printf("\n");

$coreLoad = $autoloaded =<<<EOF
<?php
spl_autoload_register(function(\$class) {
    include "$tempDir" . DIRECTORY_SEPARATOR . \$class . '.php';
});
EOF;

$core = $tempDir . DIRECTORY_SEPARATOR . 'core.php';
$coreLoad .=<<<EOF
require "$core";
EOF;

foreach ($classNames as $c) {
    $autoloaded .=<<<EOF
new $c();
EOF;

    $coreLoad .=<<<EOF
new $c();
EOF;
}

$autoloadedFile = $tempDir . DIRECTORY_SEPARATOR . 'autoload.php';
file_put_contents($autoloadedFile, $autoloaded);
$coreloadFile = $tempDir . DIRECTORY_SEPARATOR . 'coreload.php';
file_put_contents($tempDir . DIRECTORY_SEPARATOR . 'coreload.php', $coreLoad);

$tmpname = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid();

spl_autoload_register(function($class) use ($tempDir) {
    $path = $tempDir . DIRECTORY_SEPARATOR . $class . '.php';
    include $path;
});
printf("Generating core (it may take a while)\n");
\Symfony\Component\ClassLoader\ClassCollectionLoader::load($classNames, dirname($core), basename($core), false, false, '');

$stats = array(
    'autoload' => array(),
    'coreload' => array(),
);

printf("\nIterations: 1/%d", $iterations);

for($total = $iterations;$iterations--;) {
    printf("\rIterations: %d/%d", $total - $iterations, $total);
    $start = microtime(true);
    exec(sprintf('php %s', $coreloadFile));
    $elapsed = microtime(true) - $start;
    $stats['coreload'][] = $elapsed;

    $start = microtime(true);
    exec(sprintf('php %s', $autoloadedFile));
    $elapsed = microtime(true) - $start;
    $stats['autoload'][] = $elapsed;
}

printf("\n\n");

printf("┌────────────────────────────────────────────────────┐\n");
printf("│                      Results                       │\n");
printf("├──────────────┬──────────────────┬──────────────────┤\n");
printf("│              |    Autoload      |    Coreload      │\n");
printf("├──────────────┼──────────────────┼──────────────────┤\n");
printf("│  Best        |  % 10f      |  % 10f      │\n", min($stats['autoload']), min($stats['coreload']));
printf("├──────────────┼──────────────────┼──────────────────┤\n");
printf("│  Worst       |  % 10f      |  % 10f      │\n", max($stats['autoload']), max($stats['coreload']));
printf("├──────────────┼──────────────────┼──────────────────┤\n");
printf("│  Average     |  % 10f      |  % 10f      │\n",
    array_sum($stats['autoload']) / count($stats['autoload']),
    array_sum($stats['coreload']) / count($stats['coreload']));
printf("└──────────────┴──────────────────┴──────────────────┘\n");

// web

printf("\nCopying file to public folder...\n");
$pathToFiles = trim(str_replace(sys_get_temp_dir(), '', $tempDir), ' /');
mkdir('public/'.$pathToFiles);
exec('cp ' . $tempDir . '/autoload.php public/' . $pathToFiles);
exec('cp ' . $tempDir . '/coreload.php public/' . $pathToFiles);

printf("\n");

printf("phpinfo() http://coretracker/phpinfo.php\n", $pathToFiles);
printf("\n");
printf("Run the tests: \n");
printf("sudo service apache2 restart && siege -t5M -c50 http://coretracker/%s/autoload.php && sudo service apache2 restart && siege -t5M -c50 http://coretracker/%s/coreload.php", $pathToFiles, $pathToFiles);

printf("\n\n");

if (!$keepFiles) {
    printf("\nCleaning files...\n\n");

    foreach(
        new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tempDir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
        $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
    }

    rmdir($tempDir);
}
