<?php

namespace App\Service\Zoho;

use App\Entity\User;

class ContactsWebservice extends Webservice
{
    public function hasAccessToPortal(User $user)
    {
        $email = $user->getEmail();

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
