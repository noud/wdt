<?php

namespace App\Service\Zoho;

class ContactsService extends Webservice
{
    public function hasAccessToPortal(string $email)
    {
        $this->init();
        $rest = \ZCRMModule::getInstance('Contacts');
        $criteria = 'Email:equals:'.$email;
        try {
            $contacts = $rest->searchRecordsByCriteria($criteria)->getData();

            return $contacts[0]->getFieldValue('Toegang_tot_portal');
        } catch (\Exception $e) {
            return false;
        }
    }
}
