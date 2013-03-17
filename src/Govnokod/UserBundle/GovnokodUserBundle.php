<?php

namespace Govnokod\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GovnokodUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}