<?php
namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\ChangePasswordType;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AccountPasswordController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/account/password', name: 'app_account_password')]
    public function index(Request $request, UserPasswordHasherInterface $hasherUpdate): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $password = $hasherUpdate->hashPassword($user,$user->getPassword());
            $user->setPassword($password);
            //$this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('password', 'Your password has been changed !');
    
            return $this->redirectToRoute("app_homepage");

        }
        return $this->render('account/password.html.twig', [
            'form' =>  $form->createView(),
        ]);
    }
}