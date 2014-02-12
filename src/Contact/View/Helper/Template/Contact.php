<?php

namespace Contact\View\Helper\Template;

use Contact\Model\Contact as BaseContact;

use Smvc\View\Helper\Template\AbstractHelper;

class Contact extends AbstractHelper
{
    public function __invoke($contact)
    {
        if (!$contact instanceof BaseContact) {
            return '';
        }

        return $contact->getDisplayName();
    }
}
