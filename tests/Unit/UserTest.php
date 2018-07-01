<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tests\Traits\UsersAdmins;

/**
 * @group user
 */
class UserTest extends TestCase
{
    use DatabaseMigrations;
    use UsersAdmins;

    public function testGenerateUuid()
    {
        $faker = $this->fetchFaker();
        $user = new \App\User();

        $this->assertEquals(0, strlen($user->uuid));

        $user->name = $faker->name;
        $user->email = $faker->email;
        $user->password = Hash::make('secret');

        $user->save();

        $uuid = $user->uuid;

        $this->assertEquals(36, strlen($user->uuid));

        $user->name = $faker->name;

        $user->save();

        $this->assertEquals($uuid, $user->uuid);

        $u = \App\User::withUuid($uuid)->get()->first();

        $this->assertEquals($user->id, $u->id);
    }

    public function testValidationRules()
    {
        $rules = \App\User::validationRules();
        $rules2 = \App\User::validationRules(['email']);

        $this->assertTrue(count($rules) > 0);

        $this->assertTrue(key_exists('email', $rules));

        $this->assertFalse(key_exists('email', $rules2));
    }

    public function testGetNewAvatarFilename()
    {
        $user = factory(\App\User::class)->create();

        $this->assertTrue(strlen($user->newAvatarFileName()) > 0);
    }

    public function testGetLinkToUserProfile()
    {
        $user = $this->fetchUser();

        $link = sprintf('<a href="%s">%s</a>', route('profile.show', ['uuid' => $user->uuid]), $user->name);
        $this->assertEquals($link, $user->link());

        $link = sprintf('<a href="%s">%s</a>', route('profile.show', ['uuid' => $user->uuid]), 'Test');
        $this->assertEquals($link, $user->link('Test'));
    }
}
