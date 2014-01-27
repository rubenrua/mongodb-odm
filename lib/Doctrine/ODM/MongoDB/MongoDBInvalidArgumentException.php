<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\ODM\MongoDB;

/**
 * Contains exception messages for all invalid lifecycle state exceptions inside UnitOfWork
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class MongoDBInvalidArgumentException extends \InvalidArgumentException
{
    /**
     * @param object $document
     *
     * @return MongoDBInvalidArgumentException
     */
    static public function scheduleInsertForManagedDocument($document)
    {
        return new self("A managed+dirty document " . self::objToStr($document) . " can not be scheduled for insertion.");
    }

    /**
     * @param object $document
     *
     * @return MongoDBInvalidArgumentException
     */
    static public function scheduleInsertForRemovedDocument($document)
    {
        return new self("Removed document " . self::objToStr($document) . " can not be scheduled for insertion.");
    }

    /**
     * @param object $document
     *
     * @return MongoDBInvalidArgumentException
     */
    static public function scheduleInsertTwice($document)
    {
        return new self("Document " . self::objToStr($document) . " can not be scheduled for insertion twice.");
    }

    /**
     * @param array  $assoc
     * @param object $entry
     *
     * @return MongoDBInvalidArgumentException
     */
    static public function newDocumentFoundThroughRelationship(array $assoc, $entry)
    {
        return new self("A new document was found through the relationship '"
                            . $assoc['sourceDocument'] . "#" . $assoc['fieldName'] . "' that was not"
                            . " configured to cascade persist operations for document: " . self::objToStr($entry) . "."
                            . " To solve this issue: Either explicitly call DocumentManager#persist()"
                            . " on this unknown document or configure cascade persist "
                            . " this association in the mapping for example @ReferenceOne(..,cascade={\"persist\"})."
                            . (method_exists($entry, '__toString') ?
                                "":
                                " If you cannot find out which document causes the problem"
                               ." implement '" . $assoc['targetDocument'] . "#__toString()' to get a clue."));
    }

    /**
     * @param array  $assoc
     * @param object $entry
     *
     * @return MongoDBInvalidArgumentException
     */
    static public function detachedDocumentFoundThroughRelationship(array $assoc, $entry)
    {
        return new self("A detached document of type " . $assoc['targetDocument'] . " (" . self::objToStr($entry) . ") "
                        . " was found through the relationship '" . $assoc['sourceDocument'] . "#" . $assoc['fieldName'] . "' "
                        . "during cascading a persist operation.");
    }

    /**
     * @param object $document
     *
     * @return MongoDBInvalidArgumentException
     */
    static public function documentNotManaged($document)
    {
        return new self("Document " . self::objToStr($document) . " is not managed. A document is managed if its fetched " .
                "from the database or registered as new through DocumentManager#persist");
    }

    /**
     * @param object $document
     * @param string $operation
     *
     * @return MongoDBInvalidArgumentException
     */
    static public function documentHasNoIdentity($document, $operation)
    {
        return new self("Document has no identity, therefore " . $operation ." cannot be performed. " . self::objToStr($document));
    }

    /**
     * @param object $document
     * @param string $operation
     *
     * @return MongoDBInvalidArgumentException
     */
    static public function documentIsRemoved($document, $operation)
    {
        return new self("Document is removed, therefore " . $operation ." cannot be performed. " . self::objToStr($document));
    }

    /**
     * @param object $document
     * @param string $operation
     *
     * @return MongoDBInvalidArgumentException
     */
    static public function detachedDocumentCannot($document, $operation)
    {
        return new self("A detached document was found during " . $operation . " " . self::objToStr($document));
    }

    /**
     * @param string $context
     * @param mixed  $given
     * @param int    $parameterIndex
     *
     * @return MongoDBInvalidArgumentException
     */
    public static function invalidObject($context, $given, $parameterIndex = 1)
    {
        return new self($context . ' expects parameter ' . $parameterIndex .
                    ' to be a document object, '. gettype($given) . ' given.');
    }

    /**
     * @param object $document
     *
     * @return MongoDBInvalidArgumentException
     */
    public static function invalidSingleDocumentFlush($document)
    {
        return new self("Document has to be managed or scheduled for removal for single computation " . self::objToStr($document));
    }

    /**
     * @param object $document
     *
     * @return MongoDBInvalidArgumentException
     */
    public static function dirtyDocumentScheduledForInsert($document)
    {
        return new self("Dirty document cannot be scheduled for insertion.");
    }

    /**
     * @param object $document
     *
     * @return MongoDBInvalidArgumentException
     */
    public static function noIdentifier($className)
    {
        return new self(sprintf('Class "%s" does not have an identifier.', $className));
    }

    /**
     * @param object $document
     *
     * @return MongoDBInvalidArgumentException
     */
    public static function cannotCreateDocumentDBRef($document)
    {
        return new self(
            sprintf('Cannot create a DBRef without an identifier. UnitOfWork::getDocumentIdentifier() did not return an identifier for %s', self::objToStr($document))
        );
    }

    /**
     * Helper method to show an object as string.
     *
     * @param object $obj
     *
     * @return string
     */
    private static function objToStr($obj)
    {
        return method_exists($obj, '__toString') ? (string)$obj : get_class($obj).'@'.spl_object_hash($obj);
    }
}
