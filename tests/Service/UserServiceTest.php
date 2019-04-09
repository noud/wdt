<?php

namespace App\Tests\Service;

use App\Form\Data\UserAddData;
use App\Service\UserService;
use App\Tests\ServiceKernelTestCase;

class UserServiceTest extends ServiceKernelTestCase
{
    private const EMAIL = 'test@test.nl';

    /**
     * @var UserService
     */
    private $userService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->userService = self::$container->get(UserService::class);
    }

    /**
     * Test adding a user.
     */
    public function testAdd(): void
    {
        $data = new UserAddData();
        $data->email = self::EMAIL;
        $data->companyName = 'test';
        $data->firstName = 'test';
        $data->lastName = 'test';
        $data->password = 'test';
        $user = $this->userService->add($data);
        $email = $user->getEmail();

        $this->assertSame(self::EMAIL, $email);
    }

    /**
     * Test adding a user and sending an email.
     */
    public function testAddAndEmail(): void
    {
        $data = new UserAddData();
        $data->email = self::EMAIL;
        $data->companyName = 'test';
        $data->firstName = 'test';
        $data->lastName = 'test';
        $data->password = 'test';
        $user = $this->userService->addAndEmail($data);
        $email = $user->getEmail();

        $this->assertSame(self::EMAIL, $email);
    }

    /**
     * Test activating a user.
     */
    public function testActivate(): void
    {
        $data = new UserAddData();
        $data->email = self::EMAIL;
        $data->companyName = 'test';
        $data->firstName = 'test';
        $data->lastName = 'test';
        $data->password = 'test';
        $user = $this->userService->add($data);
        $user = $this->userService->activate($user);
        $status = $user->isActive();

        $this->assertTrue($status);
    }

    /**
     * Test activating a user and sending an email.
     */
    public function testActivateAndEmail(): void
    {
        $data = new UserAddData();
        $data->email = self::EMAIL;
        $data->companyName = 'test';
        $data->firstName = 'test';
        $data->lastName = 'test';
        $data->password = 'test';
        $user = $this->userService->add($data);
        $user = $this->userService->activateAndEmail($user);
        $status = $user->isActive();

        $this->assertTrue($status);
    }
}
