<?php

namespace App\Service\Zoho\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity("email")
 */
class Ticket
{
    /**
     * @var string
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

    public function setDepartmentId(string $departmentId): void
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

    public function getDepartmentId(): string
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
}
