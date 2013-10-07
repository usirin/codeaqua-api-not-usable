<?php namespace Codeaqua\Tests\Models;

use Codeaqua\Tests\TestCase;

use Codeaqua\Models\Photo;
use Way\Tests\ModelHelpers;

class PhotoTest extends TestCase {

    use ModelHelpers;

    public function setUp()
    {
        $this->photo = new Photo;
    }

    public function testDeneme()
    {
        $this->assertTrue(true);
    }

    public function testRequiresItsDatabaseTableToBeSetToPhotos()
    {
        $this->assertEquals($this->photo->getTable(), 'photos');
    }

    public function testRequiresItsRulesArrayToBeSet()
    {
        $this->assertNotEquals($this->photo->rules, []);
    }

    public function testIsInvalidWithoutADescription()
    {
        $photo = new Photo;
        $this->assertNotValid($photo);
    }

    public function testIsInvalidWithoutAnUserId()
    {
        $photo = new Photo;
        $photo->description = 'Foo Bar';

        $this->assertNotValid($photo);
    }
}