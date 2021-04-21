<?php

namespace ConstanzeStandard\Container;

use AbstractTest;

require_once __DIR__ . '/AbstractTest.php';

class EntryTest extends AbstractTest
{
    public function testGetIdentifier()
    {
        $entry = new Entity('id1', '123');
        $id = $entry->getId();
        $this->assertEquals($id, 'id1');
    }

    public function testResolveWithDefinitionWithoutNew()
    {
        $entry = new Entity('id1', function($first, $second) {
            static $a = 3;
            $this->assertEquals($first, 1);
            $this->assertEquals($second, 2);
            return $a++;
        }, true);
        $entry->addArguments(1, 2);
        $result = $entry->resolve();
        $this->assertEquals($result, 3);
        $result = $entry->resolve();
        $this->assertEquals($result, 3);
    }

    public function testResolveWithoutDefinitionWithoutNew()
    {
        $func = function() {
            return 3;
        };
        $entry = new Entity('id1', $func, false);
        $entry->addArguments(1, 2);
        $result = $entry->resolve();
        $this->assertEquals($result, $func);
    }

    public function testResolveWithDefinitionWithNewWithArguments()
    {
        $entry = new Entity('id1', function($first, $second, $t, $f) {
            static $a = 3;
            $this->assertEquals($first, 1);
            $this->assertEquals($second, 2);
            $this->assertEquals($t, 5);
            $this->assertEquals($f, 6);
            return $a++;
        }, true);
        $entry->addArguments(1, 2);
        $result = $entry->resolve([5, 6], true);
        $this->assertEquals($result, 3);
        $result = $entry->resolve([5, 6], true);
        $this->assertEquals($result, 4);
    }
}
