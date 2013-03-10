<?php


namespace Bgy\CoreTracker\Storage;


use Bgy\CoreTracker\CoreDump;

interface StorageInterface {

    public function fetch();

    public function store(CoreDump $coredump);
}
