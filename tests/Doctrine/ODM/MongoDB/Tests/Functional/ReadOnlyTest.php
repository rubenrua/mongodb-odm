<?php

namespace Doctrine\Tests\ODM\MongoDB\Functional;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

class ReadOnlyTest extends \Doctrine\ODM\MongoDB\Tests\BaseTest
{
    public function testReadOnlyDocumentNeverChangeTracked()
    {
        $readOnly = new ReadOnlyDocument("Test1", 1234);
        $this->dm->persist($readOnly);
        $this->dm->flush();

        $readOnly->name = "Test2";
        $readOnly->numericValue = 4321;

        $this->dm->flush();
        $this->dm->clear();

        $dbReadOnly = $this->dm->find('Doctrine\Tests\ODM\MongoDB\Functional\ReadOnlyDocument', $readOnly->id);
        $this->assertEquals("Test1", $dbReadOnly->name);
        $this->assertEquals(1234, $dbReadOnly->numericValue);
    }

    public function testReadOnlyEmbedOneNeverChangeTracked()
    {
        $write = new WriteDocument();
        $write->readOnlyEmbedOne = new ReadOnlyEmbeddedDocument("Test1", 1234);
        $this->dm->persist($write);
        $this->dm->flush();

        $write->readOnlyEmbedOne->name = "Test2";
        $write->readOnlyEmbedOne->numericValue = 4321;

        $this->dm->flush();
        $this->dm->clear();

        $write = $this->dm->find('Doctrine\Tests\ODM\MongoDB\Functional\WriteDocument', $write->id);

        $this->assertEquals("Test1", $write->readOnlyEmbedOne->name);
        $this->assertEquals(1234, $write->readOnlyEmbedOne->numericValue);
    }

    public function testReadOnlyEmbedManyNeverChangeTracked()
    {
        $write = new WriteDocument();
        $write->readOnlyEmbedMany[0] = new ReadOnlyEmbeddedDocument("Test1", 1234);
        $this->dm->persist($write);
        $this->dm->flush();

        $write->readOnlyEmbedMany[0]->name = "Test2";
        $write->readOnlyEmbedMany[0]->numericValue = 4321;

        $this->dm->flush();
        $this->dm->clear();

        $write = $this->dm->find('Doctrine\Tests\ODM\MongoDB\Functional\WriteDocument', $write->id);

        $this->assertEquals("Test1", $write->readOnlyEmbedMany[0]->name);
        $this->assertEquals(1234, $write->readOnlyEmbedMany[0]->numericValue);
    }
}

/**
 * @ODM\Document(readOnly=true)
 */
class ReadOnlyDocument
{
    /**
     * @ODM\Id
     */
    public $id;

    /** @ODM\String */
    public $name;

    /** @ODM\Int */
    public $numericValue;

    public function __construct($name, $number)
    {
        $this->name = $name;
        $this->numericValue = $number;
    }
}

/** @ODM\Document */
class WriteDocument
{
    /**
     * @ODM\Id
     */
    public $id;

    /**
     * @ODM\EmbedOne(targetDocument="EmbeddedDocument")
     */
    public $embedOne;

    /**
     * @ODM\EmbedMany(targetDocument="EmbeddedDocument")
     */
    public $embedMany;

    /**
     * @ODM\EmbedOne(targetDocument="ReadOnlyEmbeddedDocument")
     */
    public $readOnlyEmbedOne;

    /**
     * @ODM\EmbedMany(targetDocument="ReadOnlyEmbeddedDocument")
     */
    public $readOnlyEmbedMany = array();
}

/**
 * @ODM\EmbeddedDocument(readOnly=true)
 */
class ReadOnlyEmbeddedDocument
{
    /** @ODM\String */
    public $name;

    /** @ODM\Int */
    public $numericValue;

    public function __construct($name, $number)
    {
        $this->name = $name;
        $this->numericValue = $number;
    }
}
