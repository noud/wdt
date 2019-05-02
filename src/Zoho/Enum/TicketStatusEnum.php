<?php

namespace App\Zoho\Enum;

class TicketStatusEnum extends AbstractEnum
{
    const OPEN = 'open';
    const STAGING = 'staging';
    const CODE_REVIEW = 'code_review';
    const IN_PROGRESS = 'in_progress';
    const ESCALATED = 'escalated';
    const READY_FOR_LIVE = 'ready_for_live';
    const WAIT_ON_FEEDBACK = 'wait_on_feedback';
    const ESTIMATED = 'estimated';
    const SIGNED_OFF_LIVE = 'signed_off_live';
    const SIGNED_OFF_CANCELED = 'signed_off_canceled';
    const SIGNED_OFF_PROCESSED = 'signed_off_processed';
    
    /**
     * @return string[]
     */
    public static function getLabels(): array
    {
        return [
            self::OPEN => 'enum.ticket_status.open',
            self::STAGING => 'enum.ticket_status.staging',
            self::CODE_REVIEW => 'enum.ticket_status.code_review',
            self::IN_PROGRESS => 'enum.ticket_status.in_progress',
            self::ESCALATED => 'enum.ticket_status.escalated',
            self::READY_FOR_LIVE => 'enum.ticket_status.ready_for_live',
            self::WAIT_ON_FEEDBACK => 'enum.ticket_status.wait_on_feedback',
            self::ESTIMATED => 'enum.ticket_status.estimated',
            self::SIGNED_OFF_LIVE => 'enum.ticket_status.signed_off_live',
            self::SIGNED_OFF_CANCELED => 'enum.ticket_status.signed_off_canceled',
            self::SIGNED_OFF_PROCESSED => 'enum.ticket_status.signed_off_processed',
        ];
    }

    public static function getChoices(): array
    {
        return array_flip(self::getLabels());
    }
}
