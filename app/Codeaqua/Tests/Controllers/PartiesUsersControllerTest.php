<?php namespace Codeaqua\Tests\Controllers;

use Codeaqua\Tests\TestCase;
use Codeaqua\Controllers\PartiesUsersController;
use Codeaqua\Models\PartyUser;

use Illuminate\Support\Facades\Auth;

use Way\Tests\Factory;

use Mockery as m;

class PartiesUsersControllerTest extends TestCase {

    public function setUp()
    {
        parent::setUp();
        Auth::shouldReceive('user')->andReturn($user = m::mock('Codeaqua\Models\User', ['id' => 1]));

        $this->app->instance('Codeaqua\Models\User', $user);
    }

    public function tearDown()
    {
        \Mockery::close();
    }

    public function testJoinRouteCallsJoinFunctionOnModel()
    {
        $mock = m::mock(PartyUser::class);

        $mock->shouldReceive('joinParty')
            ->once()
            ->andReturn(true);

        $this->app->instance(PartyUser::class, $mock);

        $response = $this->post('v0/parties/1/join');

        $this->assertResponseOk($response);
    }

    public function testJoinRouteReturnsErrorsPartyUserInsertionFails()
    {
        $mock = m::mock(PartyUser::class);

        $mock->shouldReceive('joinParty')
            ->once()
            ->andReturn(false);

        $this->app->instance(PartyUser::class, $mock);

        $response = $this->post('v0/parties/1/join');

        $this->assertResponseStatus(400);
    }

    public function testUnjoinRouteCallsJoinFunctionOnModel()
    {
        $mock = m::mock(PartyUser::class);

        $mock->shouldReceive('unjoinParty')
            ->once()
            ->andReturn(true);

        $this->app->instance(PartyUser::class, $mock);

        $response = $this->post('v0/parties/1/unjoin');

        $this->assertResponseOk($response);
    }

    public function testUnjoinRouteReturnsErrorsPartyUserInsertionFails()
    {
        $mock = m::mock(PartyUser::class);

        $mock->shouldReceive('joinParty')
            ->once()
            ->andReturn(false);

        $this->app->instance(PartyUser::class, $mock);

        $response = $this->post('v0/parties/1/join');

        $this->assertResponseStatus(400);
    }

    public function testCheckinRouteCallsJoinFunctionOnModel()
    {
        $mock = m::mock(PartyUser::class);

        $mock->shouldReceive('checkin')
            ->once()
            ->andReturn(true);

        $this->app->instance(PartyUser::class, $mock);

        $response = $this->post('v0/parties/1/checkin');

        $this->assertResponseOk($response);
    }

    public function testCheckinRouteReturnsErrorsPartyUserInsertionFails()
    {
        $mock = m::mock(PartyUser::class);

        $mock->shouldReceive('checkin')
            ->once()
            ->andReturn(false);

        $this->app->instance(PartyUser::class, $mock);

        $response = $this->post('v0/parties/1/checkin');

        $this->assertResponseStatus(400);
    }

    public function testCheckoutRouteCallsJoinFunctionOnModel()
    {
        $mock = m::mock(PartyUser::class);

        $mock->shouldReceive('checkout')
            ->once()
            ->andReturn(true);

        $this->app->instance(PartyUser::class, $mock);

        $response = $this->post('v0/parties/1/checkout');

        $this->assertResponseOk($response);
    }

    public function testCheckoutRouteReturnsErrorsPartyUserInsertionFails()
    {
        $mock = m::mock(PartyUser::class);

        $mock->shouldReceive('checkout')
            ->once()
            ->andReturn(false);

        $this->app->instance(PartyUser::class, $mock);

        $response = $this->post('v0/parties/1/checkout');

        $this->assertResponseStatus(400);
    }

    public function testCheckinsRouteReturnsUserList()
    {
        $mock = m::mock(PartyUser::class);

        $users = Factory::make('Codeaqua\Models\User');

        $mock->shouldReceive('checkins')->once()->andReturn($users);

        $this->app->instance(PartyUser::class, $mock);

        $response = $this->get('v0/parties/1/checkins');

        $this->assertResponseOk($response);
    }

    public function testCheckinsRouteReturnsErrorResponse()
    {
        $mock = m::mock(PartyUser::class);

        $mock->shouldReceive('checkins')->once()->andReturn([]);

        $this->app->instance(PartyUser::class, $mock);

        $response = $this->get('v0/parties/1/checkins');

        $this->assertResponseStatus(404);
    }

    public function testJoiningRouteReturnsUserList()
    {
        $mock = m::mock(PartyUser::class);

        $users = Factory::make('Codeaqua\Models\User');

        $mock->shouldReceive('joining')->once()->andReturn($users);

        $this->app->instance(PartyUser::class, $mock);

        $response = $this->get('v0/parties/1/joining');

        $this->assertResponseOk($response);
    }

    public function testJoiningRouteReturnsErrorResponse()
    {
        $mock = m::mock(PartyUser::class);

        $mock->shouldReceive('joining')->once()->andReturn([]);

        $this->app->instance(PartyUser::class, $mock);

        $response = $this->get('v0/parties/1/joining');

        $this->assertResponseStatus(404);
    }
}