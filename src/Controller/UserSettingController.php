<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UserSettingController extends AbstractController
{

 
    private $security;


    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/user/setting", name="app_user_setting")
     */
    public function index(SessionInterface $session): Response
    {
        return $this->render('user_setting/index.html.twig', [
            'controller_name' => 'UserSettingController',
            'lastName' => $this->security->getUser()->getNom(),
            'firstName' => $this->security->getUser()->getPrenom(),
            'hasAdminRole' => $this->hasAdminRole($this->security->getUser()->getRoles() ?? []),
            'shoppingCartCount' => count($session->get('panier') ?? []),
            'infos' => $this->getInfos(),
            'preferences' => $this->getPreferences(),
            'securityInfos' => $this->getSecurityInfos(),
            'imageProfile'  => !is_null($this->security->getUser()) ?  $this->security->getUser()->getImageProfile() : ' ',

        ]);
    }

    public  function hasAdminRole($roles = [])
    {
        return $roles[array_search('ROLE_ADMIN', $roles)] == 'ROLE_ADMIN';
    }

    public  function getInfos()
    {
        return [
            [
                'name' => 'Modifier votre profil',
                'urlPath' => '',
                'imageUrl' => 'https://img.icons8.com/external-flaticons-lineal-color-flat-icons/64/000000/external-profile-web-flaticons-lineal-color-flat-icons-2.png'
            ]
        ];
    }

    public  function getPreferences()
    {
        return [
            [
                'name' => 'Choisir une nouvelle langue',
                'urlPath' => '',
                'imageUrl' => 'https://img.icons8.com/external-flaticons-lineal-color-flat-icons/64/000000/external-language-movie-theater-flaticons-lineal-color-flat-icons-3.png'
            ],
            [
                'name' => 'Changer de thème',
                'urlPath' => '',
                'imageUrl' => 'https://img.icons8.com/external-flaticons-flat-flat-icons/64/000000/external-theme-no-code-flaticons-flat-flat-icons.png'
            ],
            [
                'name' => 'Changer votre méthode de paiement',
                'urlPath' => '',
                'imageUrl' => 'https://img.icons8.com/external-kiranshastry-lineal-color-kiranshastry/64/000000/external-payment-economy-kiranshastry-lineal-color-kiranshastry.png'
            ]
        ];
    }

    public  function getSecurityInfos()
    {
        return [
            [
                'name' => 'Modifier votre mot de passe',
                'urlPath' => 'app_password_reset',
                'imageUrl' => 'https://img.icons8.com/fluency/64/000000/password-window.png'
            ],
            [
                'name' => 'Authentification à double facteur',
                'urlPath' => 'app_password_reset',
                'imageUrl' => 'https://img.icons8.com/external-flaticons-flat-flat-icons/64/000000/external-password-privacy-flaticons-flat-flat-icons.png'
            ]
        ];
    }

    /**
     * @Route("/user/profile/edit", name="app_user_profile_edit")
     */
    public function editUserProfile(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(EditUserProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userToUpdate = $entityManager->getRepository(User::class)->find($this->security->getUser()->getId());

            if (!$userToUpdate) {
                throw $this->createNotFoundException(
                    'No user found for id ' . $this->security->getId()
                );
            }


            $imageProfileFile = $form->get('imageProfile')->getData();

            if ($imageProfileFile) {
                $originalFilename = pathinfo($imageProfileFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageProfileFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {

                    $base_url  = $_SERVER['DOCUMENT_ROOT'] . "image\\";
                    $imageProfileFile->move(
                        $base_url,
                        $newFilename
                    );
                } catch (FileException $e) {
                    return  $e->getMessage();
                }
                $userToUpdate->setImageProfile($newFilename);
            }

            $userToUpdate->setPortable($user->getPortable());
            $userToUpdate->setNom($user->getNom());
            $userToUpdate->setPrenom($user->getPrenom());
            $userToUpdate->setAdresse($user->getAdresse());


            $entityManager->flush();
            //  $entityManager->persist($user);
            // $entityManager->flush();

        }

        return $this->render('user_setting/edit-profile.html.twig', [
            'controller_name' => 'UserSettingController',
            'lastName' => $this->security->getUser()->getNom(),
            'firstName' => $this->security->getUser()->getPrenom(),
            'email' => $this->security->getUser()->getEmail(),
            'address' => $this->security->getUser()->getAdresse(),
            'portable' => $this->security->getUser()->getPortable(),
            'imageProfile'  => $this->security->getUser()->getImageProfile(),
            'hasAdminRole' => $this->hasAdminRole($this->security->getUser()->getRoles() ?? []),
            'shoppingCartCount' => count($session->get('panier') ?? []),
            'infos' => $this->getInfos(),
            'preferences' => $this->getPreferences(),
            'securityInfos' => $this->getSecurityInfos(),
            'editUserProfileForm' => $form->createView()
        ]);
    }
}
