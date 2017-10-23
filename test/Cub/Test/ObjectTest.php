<?php
class Cub_Test_ObjectTest extends Cub_Test_TestCase
{
    public function testUserFromJson()
    {
        $user = new Cub_User(array(
            'email' => 'superemail@sobaka.lol',
            'username' => 'darth_vader_1996',
            'password' => 'super_safe',
        ));
        $this->assertEquals('superemail@sobaka.lol', $user->email);
        $this->assertEquals('darth_vader_1996', $user->username);
        $this->assertEquals('super_safe', $user->password);
        $user_constructed = Cub_Object::fromJson(
            '{
            "object": "user",
            "email": "superemail@sobaka.lol",
            "username": "darth_vader_1996",
            "password": "super_safe"
             }'
        );
        $this->assertEquals($user, $user_constructed);
    }

    public function testClassUrl()
    {
        $this->assertEquals(
            'members',
            Cub_Object::classUrl('Cub_Member')
        );
    }

    public function testInstanceUrl()
    {
        $user = new Cub_User(array(
            'id' => 'usr_realseriousuid',
            'email' => 'lold@myseriousbusiness666.lol',
        ));
        $this->assertEquals('users/usr_realseriousuid', $user->instanceUrl());
    }

    public function testCanGetList()
    {
        $orgs = Cub_Organization::getList(array('q' => 'test'));
        $this->assertTrue(is_array($orgs));
        $this->assertGreaterThan(5, sizeof($orgs));
        $this->assertTrue($orgs[2] instanceof Cub_Organization);
    }

    public function testStudlyCapsFromJson()
    {
        $groupMember = new Cub_GroupMember(array(
            'group' => 'grp_OCbAxHoWq62ghcB8',
            'member' => 'mbr_hOMQw7n9k6QZMkf0',
            'is_admin' => false,
        ));
        $this->assertEquals('grp_OCbAxHoWq62ghcB8', $groupMember->group);
        $this->assertEquals('mbr_hOMQw7n9k6QZMkf0', $groupMember->member);
        $this->assertFalse($groupMember->is_admin);
        $groupMember_constructed = Cub_Object::fromJson(
            '{
            "object": "groupmember",
            "group": "grp_OCbAxHoWq62ghcB8",
            "member": "mbr_hOMQw7n9k6QZMkf0",
            "is_admin": false
             }'
        );
        $this->assertEquals($groupMember, $groupMember_constructed);
    }
}
