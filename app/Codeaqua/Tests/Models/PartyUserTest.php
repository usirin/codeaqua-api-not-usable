<?php namespace Codeaqua\Tests\Models;

use Codeaqua\Tests\TestCase;
use Codeaqua\Models\PartyUser;
use Codeaqua\Tests\Helpers\ModelHelpers;
use Codeaqua\Models\User;

use Way\Tests\Factory;

use Mockery as m;

class PartyUserTest extends TestCase {
    use ModelHelpers;

    public function testIsInvalidWithoutPartyId()
    {
        $partyUser = Factory::make(PartyUser::class, ['partyId' => null]);

        $this->assertNotValid($partyUser);
    }

    public function testIsInvalidWithoutUserId()
    {
        $partyUser = Factory::make(PartyUser::class, ['userId' => null]);
        $this->assertNotValid($partyUser);
    }

    public function testIsInvalidWithoutRoleId()
    {
        $partyUser = Factory::make(PartyUser::class, ['roleId' => null]);
        $this->assertNotValid($partyUser);
    }

    public function testIsInvalidWithoutStatusId()
    {
        $partyUser = Factory::make(PartyUser::class, ['statusId' => null]);
        $this->assertNotValid($partyUser);
    }
}