<?php
declare(strict_types=1);

namespace App\Tests\unit;

use PHPUnit\Framework\TestCase;
use App\Entity\User;

class testUser extends TestCase
{

    public function testGetEmail(): void
    {
        $user = new User();
        $value = "test@test.fr";
        $response = $user->setEmail($value);
        $getEmail = $user->getEmail();
        self::assertInstanceOf(User::class,$response);
        self::assertEquals($value,$getEmail);
    }
}