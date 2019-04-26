<?php

namespace App\Zoho\Enum;

class TicketPriorityEnum extends AbstractEnum
{
    const NONE = '';
    const HIGH = 'high';
    const MEDIUM = 'medium';
    const LOW = 'low';

    /**
     * @return string[]
     */
    public static function getLabels(): array
    {
        return [
            self::NONE => 'enum.ticket_priority.none',
            self::HIGH => 'enum.ticket_priority.high',
            self::MEDIUM => 'enum.ticket_priority.medium',
            self::LOW => 'enum.ticket_priority.low',
        ];
    }

    public static function getChoices(): array
    {
        return array_flip(self::getLabels());
    }
}
