<?php

namespace App\Zoho\Enum;

class TicketStatusEnum extends AbstractEnum
{
    const OPEN = 'Open';
    const STAGING = 'Staging / acceptatie';
    const CODE_REVIEW = 'Code review';
    const IN_PROGRESS = 'In behandeling';
    const ESCALATED = 'Escalated';
    const READY_FOR_LIVE = 'Klaar voor live';
    const WAIT_ON_FEEDBACK = 'Wacht op feedback klant';
    const ESTIMATED = 'Ingeschat';
    const SIGNED_OFF_LIVE = 'Afgemeld - staat live';
    const SIGNED_OFF_CANCELED = 'signed_off_canceled';
    const SIGNED_OFF_PROCESSED = 'Afgemeld - administratief verwerkt';

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
