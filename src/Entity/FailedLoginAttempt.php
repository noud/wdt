<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FailedLoginAttemptRepository")
 * @ORM\Table(name="cms_failed_login_attempt")
 */
class FailedLoginAttempt
{
    public const NUMBER_OF_ALLOWED_FAILURES = 3;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @ORM\Id()
     */
    private $ipAddress;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime")
     */
    private $lastFailure;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $numberOfFailures;

    /**
     * FailedLoginAttempt constructor.
     */
    public function __construct(string $ipAddress, DateTimeImmutable $lastFailure)
    {
        $this->ipAddress = $ipAddress;
        $this->lastFailure = $lastFailure;
        $this->numberOfFailures = 1;
    }

    public function addFailure(DateTimeImmutable $lastFailure): void
    {
        $this->lastFailure = $lastFailure;
        ++$this->numberOfFailures;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getNumberOfFailures(): int
    {
        return $this->numberOfFailures;
    }

    public function isTooMany(): bool
    {
        return $this->numberOfFailures > self::NUMBER_OF_ALLOWED_FAILURES;
    }
}
