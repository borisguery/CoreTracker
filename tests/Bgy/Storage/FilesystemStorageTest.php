<?php

namespace Bgy\CoreTracker\Storage;

use Bgy\CoreTracker\CoreDump;

class FilesystemStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testFetchFromInexistentPathReturnAEmptyCoreDump()
    {
        $path = tempnam(sys_get_temp_dir(), uniqid());
        $s = new FilesystemStorage($path);
        $c = $s->fetch();

        $this->assertInstanceOf('Bgy\CoreTracker\CoreDump', $c);
        @unlink($path);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFetchWithABadSerializationFormatThrowsAnException()
    {
        $path = tempnam(sys_get_temp_dir(), uniqid());
        file_put_contents($path, 'it is not a proper php serializer format');
        $s = new FilesystemStorage($path);
        $s->fetch();
        @unlink($path);
    }

    public function testStoreToAnInextantPath()
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid() . DIRECTORY_SEPARATOR . uniqid();
        $s = new FilesystemStorage($path);
        $s->store(new CoreDump());

        $this->assertFileExists($path);
        @unlink($path);
    }
}
