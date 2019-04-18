<?php

namespace App\Zoho\Entity\Books;

class Invoice
{
    private $invoiceId;
    private $achPaymentInitiated;
    private $zcrmPotentialId;
    private $zcrmPotentialName;
    private $customerName;
    private $customerId;
    private $companyName;
    private $status;
    private $colorCode;
    private $currentSubStatusId;
    private $currentSubStatus;
    private $invoiceNumber;
    private $referenceNumber;
    private $date;
    private $dueDate;
    private $dueDays;
    private $scheduleTime;
    private $currencyCode;
    private $templateType;
    private $noOfCopies;
    private $showNoOfCopies;
    private $isViewedByClient;
    private $hasAttachment;
    private $clientViewedTime;
    private $total;
    private $balance;
    private $createdTime;
    private $lastModifiedTime;
    private $isEmailed;
    private $remindersSent;
    private $lastReminderSentDate;
    private $paymentExpectedDate;
    private $lastPaymentDate;
    private $customFields;
    private $customFieldHash;
    private $documents;
    private $salesPeronId;
    private $salesPersonName;
    private $adjustment;
    private $writeOffAmount;
    private $exchangeRate;

    public function camelCase($str)
    {
        $i = ['-', '_'];
        /** @var array|string $str */
        $str = preg_replace('/([a-z])([A-Z])/', '\\1 \\2', $str);
        /** @var array|string $str */
        $str = preg_replace('@[^a-zA-Z0-9\-_ ]+@', '', $str);
        /** @var string $str */
        $str = str_replace($i, ' ', $str);
        /** @var string $str */
        $str = str_replace(' ', '', ucwords(mb_strtolower($str)));
        $str = mb_strtolower(mb_substr($str, 0, 1)).mb_substr($str, 1);

        return $str;
    }

    /**
     * Invoice constructor.
     */
    public function __construct(array $data = null)
    {
        /** @var array $data */
        $data = $data;

        foreach ($data as $key => $value) {
            $key = $this->camelCase($key);
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
