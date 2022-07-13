<?php
 
namespace App\Service;

use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class UserProfileService {

    protected $session;

    /**
     * @var Security
     */
    private $security;

    protected $produitsRepository ;

    public function __construct(SessionInterface $session, Security $security)
    {
        $this->session=$session;
        $this->security = $security;
     }

     public function getProfileImage() {
        return $this->security->getUser()->getImageProfile();
     }
}
