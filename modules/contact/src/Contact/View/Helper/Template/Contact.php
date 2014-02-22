<?php

namespace Contact\View\Helper\Template;

use Contact\Model\Contact as BaseContact;

use Smvc\View\Helper\Template\AbstractHelper;
use Smvc\View\View;

class Contact extends AbstractHelper
{
    public function __invoke($contact)
    {
        if (!$contact instanceof BaseContact) {
            return '';
        }

        return new View(array('contact' => $contact), 'contact/helper/contact');
    }
}
