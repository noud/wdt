<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LockedIpAddressRepository")
 * @ORM\Table(name="cms_locked_ip_address")
 */
class LockedIpAddress
{
    const DURATION_ACTIVE = 'PT15M';

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
    private $lockedSince;

    /**
     * LockedIpAddress constructor.
     */
    public function __construct(string $ipAddress, DateTimeImmutable $lockedSince)
    {
        $this->ipAddress = $ipAddress;
        $this->lockedSince = $lockedSince;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function setLockedSince(DateTimeImmutable $lockedSince): void
    {
        $this->lockedSince = $lockedSince;
    }

    /**
     * @throws \Exception
     */
    public function isActiveAt(DateTimeImmutable $now): bool
    {
        $lockedUntil = $this->lockedSince->add(new \DateInterval(self::DURATION_ACTIVE));

        return $now < $lockedUntil;
    }
}
