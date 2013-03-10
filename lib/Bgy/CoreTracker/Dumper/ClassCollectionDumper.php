<?php

namespace Bgy\CoreTracker\Dumper;

use Bgy\CoreTracker\CoreDump;
use Symfony\Component\ClassLoader\ClassCollectionLoader;

class ClassCollectionDumper implements DumperInterface
{
    public function dump(CoreDump $coredump)
    {
        $classCollection = array();

        foreach ($coredump->getCollectedData() as $collectedClass) {
            $classCollection[] = $collectedClass->className;
        }

        $tmpname = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid();

        ClassCollectionLoader::load($classCollection, dirname($tmpname), basename($tmpname), false, false, '');

        $content = file_get_contents($tmpname);
        unlink($tmpname);

        return $content;
    }
}
