<?php


namespace Bgy\CoreTracker\Storage;

use Bgy\CoreTracker\CoreDump;

class FilesystemStorage implements StorageInterface {

    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return \Bgy\CoreTracker\CoreDump
     * @throws \RuntimeException
     */
    public function fetch()
    {
        if (!file_exists($this->path)) {
            $coredump = new CoreDump();
        } else {
            if (!is_readable($this->path)) {

                throw new \RuntimeException(sprintf("'%s' is not readable.", $this->path));
            }

            $serializedContent = file_get_contents($this->path);
            if (!empty($serializedContent)) {
                if (false === ($coredump = @unserialize($serializedContent))) {

                    throw new \RuntimeException(sprintf("Something went wrong when unserializing Core Dump"));
                }
            } else {
                $coredump = new CoreDump();
            }
        }

        return $coredump;
    }

    public function store(CoreDump $coredump)
    {
        if (false === ($realpath = realpath(dirname($this->path)))) {
            if (!mkdir(dirname($this->path), 0777, true)) {

                throw new \RuntimeException(sprintf('Unable to create directory: "%s"', dirname($this->path)));
            }


            if (!touch($this->path)) {
                throw new \RuntimeException(sprintf('Unable to Core Dump file: "%s"', $this->path));
            }
        }

        file_put_contents($this->path, serialize($coredump));
    }
}
