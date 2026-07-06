<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Admin extends User
{
    public function __construct()
    {
        parent::__construct();
        $this->addAssignedRole('ROLE_ADMIN');
        $this->addAssignedRole('ROLE_MANAGER');
    }
}
