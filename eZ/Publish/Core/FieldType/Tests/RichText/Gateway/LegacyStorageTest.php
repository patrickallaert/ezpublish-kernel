<?php
/**
 * File containing the LegacyStorageTest for RichText FieldType
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Repository\Tests\FieldType\RichText\Gateway;

use eZ\Publish\Core\FieldType\RichText\RichTextStorage\Gateway\LegacyStorage;
use eZ\Publish\Core\Persistence\Legacy\Tests\TestCase;

/**
 * Tests the RichText LegacyStorage
 * @package eZ\Publish\Core\Repository\Tests\FieldType\RichText\Gateway
 */
class LegacyStorageTest extends TestCase
{
    public function testGetLinkUrls()
    {
        $this->insertDatabaseFixture( __DIR__ . "/_fixtures/urls.php" );

        $gateway = $this->getStorageGateway();

        $this->assertEquals(
            array(
                23 => "/content/view/sitemap/2",
                24 => "/content/view/tagcloud/2"
            ),
            $gateway->getIdUrls(
                array( 23, 24, "fake" )
            )
        );
    }

    public function testGetLinkIds()
    {
        $this->insertDatabaseFixture( __DIR__ . "/_fixtures/urls.php" );

        $gateway = $this->getStorageGateway();

        $this->assertEquals(
            array(
                "/content/view/sitemap/2" => 23,
                "/content/view/tagcloud/2" => 24
            ),
            $gateway->getUrlIds(
                array(
                    "/content/view/sitemap/2",
                    "/content/view/tagcloud/2",
                    "fake"
                )
            )
        );
    }

    public function testGetContentIds()
    {
        $this->insertDatabaseFixture( __DIR__ . "/_fixtures/contentobjects.php" );

        $gateway = $this->getStorageGateway();

        $this->assertEquals(
            array(
                "f5c88a2209584891056f987fd965b0ba" => 4,
                "faaeb9be3bd98ed09f606fc16d144eca" => 10
            ),
            $gateway->getContentIds(
                array(
                    "f5c88a2209584891056f987fd965b0ba",
                    "faaeb9be3bd98ed09f606fc16d144eca",
                    "fake"
                )
            )
        );
    }

    public function testInsertUrl()
    {
        $this->insertDatabaseFixture( __DIR__ . "/_fixtures/urls.php" );

        $gateway = $this->getStorageGateway();

        $url = "one/two/three";
        $time = time();
        $id = $gateway->insertUrl( $url );

        $query = $this->getDatabaseHandler()->createSelectQuery();
        $query
            ->select( "*" )
            ->from( 'ezurl' )
            ->where(
                $query->expr->eq(
                    $this->handler->quoteColumn( 'id' ),
                    $query->bindValue( $id )
                )
            );
        $statement = $query->prepare();
        $statement->execute();

        $result = $statement->fetchAll( \PDO::FETCH_ASSOC );

        $expected = array(
            array(
                'id' => $id,
                'is_valid' => "1",
                'last_checked' => "0",
                'original_url_md5' => md5( $url ),
                'url' => $url
            )
        );

        $this->assertGreaterThanOrEqual( $time, $result[0]["created"] );
        $this->assertGreaterThanOrEqual( $time, $result[0]["modified"] );

        unset( $result[0]["created"] );
        unset( $result[0]["modified"] );

        $this->assertEquals( $expected, $result );
    }

    public function testLinkUrl()
    {
        $gateway = $this->getStorageGateway();

        $urlId = 12;
        $fieldId = 10;
        $versionNo = 1;
        $gateway->linkUrl( $urlId, $fieldId, $versionNo );

        $query = $this->getDatabaseHandler()->createSelectQuery();
        $query->select( "*" )->from( 'ezurl_object_link' );
        $statement = $query->prepare();
        $statement->execute();

        $result = $statement->fetchAll( \PDO::FETCH_ASSOC );

        $expected = array(
            array(
                'contentobject_attribute_id' => $fieldId,
                'contentobject_attribute_version' => $versionNo,
                'url_id' => $urlId,
            ),
        );

        $this->assertEquals( $expected, $result );
    }

    /**
     * @var \eZ\Publish\Core\FieldType\RichText\RichTextStorage\Gateway\LegacyStorage
     */
    protected $storageGateway;

    /**
     * Returns a ready to test LegacyStorage gateway
     *
     * @return \eZ\Publish\Core\FieldType\RichText\RichTextStorage\Gateway\LegacyStorage
     */
    protected function getStorageGateway()
    {
        if ( !isset( $this->storageGateway ) )
        {
            $this->storageGateway = new LegacyStorage();
            $this->storageGateway->setConnection( $this->getDatabaseHandler() );
        }
        return $this->storageGateway;
    }
}
