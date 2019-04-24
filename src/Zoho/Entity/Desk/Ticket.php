<?php

namespace App\Zoho\Entity\Desk;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity("email")
 */
class Ticket
{
    /**
     * @var string
     */
    private $ticketNumber;

    /**
     * @var int
     * @Assert\NotBlank()
     */
    private $departmentId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $contactId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $subject;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var string
     */
    private $priority;

    /**
     * @var string
     */
    private $status;

    public function setTicketNumber(string $ticketNumber): void
    {
        $this->ticketNumber = $ticketNumber;
    }

    public function setDepartmentId(int $departmentId): void
    {
        $this->departmentId = $departmentId;
    }

    public function setContactId(string $contactId): void
    {
        $this->contactId = $contactId;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setPriority(string $priority): void
    {
        $this->priority = $priority;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getTicketNumber(): string
    {
        return $this->ticketNumber;
    }

    public function getDepartmentId(): int
    {
        return $this->departmentId;
    }

    public function getContactId(): string
    {
        return $this->contactId;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
