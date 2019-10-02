<?php

namespace ConstanzeStandard\Container;

use AbstractTest;
use ConstanzeStandard\Container\Interfaces\ContainerInterface;

require_once __DIR__ . '/AbstractTest.php';

class AbstractEntryProviderTest extends AbstractTest
{
    public function testHas()
    {
        $entryProvider = new class() extends AbstractEntryProvider {
            protected $provides = [
                'id1', 'id2'
            ];

            public function register(ContainerInterface $container) { }
        };

        $r1 = $entryProvider->has('id1');
        $r2 = $entryProvider->has('id3');
        
        $this->assertTrue($r1);
        $this->assertFalse($r2);
    }
}
