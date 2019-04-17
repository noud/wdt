<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResetPasswordRequestRepository")
 */
class ResetPasswordRequest
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=23, unique=true)
     */
    private $token;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime")
     */
    private $expireDate;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $username;

    public function getId(): int
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpireDate(): \DateTimeImmutable
    {
        return $this->expireDate;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setToken(string $token)
    {
        $this->token = $token;
    }

    public function setExpireDate(\DateTimeImmutable $expireDate): void
    {
        $this->expireDate = $expireDate;
    }
}
