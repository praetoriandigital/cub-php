<?php
class Cub_Test_UserTest extends Cub_Test_TestCase
{
    public function testGetUser()
    {
        $user = Cub_User::get(array(
            'email' => $this->credentials['username'],
            'id' => 'usr_upfrcJvCTyXCVBj8'
        ));
        $this->assertEquals($this->credentials['username'], $user->email);
        $this->assertEquals($this->details['first_name'], $user->first_name);
        $this->assertEquals($this->details['last_name'], $user->last_name);
    }

    public function testLoginAndGetByToken()
    {
        $user = Cub_User::login(
            $this->credentials['username'],
            $this->credentials['password']
        );
        $this->assertEquals($this->credentials['username'], $user->email);
        $this->assertEquals($this->details['first_name'], $user->first_name);
        $this->assertEquals($this->details['last_name'], $user->last_name);

        $this->assertNotNull($user->token);

        $user_by_token = Cub_User::get(array(), $api_key = $user->token);
        $this->assertEquals($user_by_token->email, $user->email);
        $this->assertEquals($user_by_token->date_joined, $user->date_joined);
        $this->assertEquals($user_by_token->registration_site, $user->registration_site);
    }

    /**
     * @expectedException Cub_MethodNotAllowed
     * @expectedExceptionMessageRegExp /DELETE method is not allowed for \/v1\/users?\w+/
     */
    public function testCantDeleteById()
    {
        Cub_User::deleteById('usr_upfrcJvCTyXCVBj8');
    }

    public function testReload()
    {
        $user = Cub_User::login(
            $this->credentials['username'],
            $this->credentials['password']
        );
        $user->reload(array('expand' => 'membership__organization'));
        $this->assertTrue(sizeof($user->membership) > 0, 'Should have membership');
        $member = $user->membership[0];
        $this->assertTrue($member instanceof Cub_Member);
        $this->assertTrue($member->organization instanceof Cub_Organization);
        $this->assertEquals('!! Test Dept', $member->organization->name);
    }
}

