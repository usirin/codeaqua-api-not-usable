<?php namespace Codeaqua\Tests\Models;

use Codeaqua\Tests\TestCase;

use Codeaqua\Models\User;

class UserTest extends TestCase {

    public function setUp()
    {
        $this->user = new User;
    }

    public function testRequiresValidation()
    {
        $this->assertFalse($this->user->validate());
    }


}