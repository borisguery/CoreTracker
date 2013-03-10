<?php

namespace Bgy\CoreTracker\Dumper;

use Bgy\CoreTracker\CoreDump;

interface DumperInterface
{
    public function dump(CoreDump $coredump);
}
