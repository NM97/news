<?php

namespace App\Controller;

use App\Form\DeleteAccountFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/dashboard/profile', name: 'app_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $this->getUser();
        $deleteAccountForm = $this->createForm(DeleteAccountFormType::class, $user);
        $deleteAccountForm->handleRequest($request);

        if ($deleteAccountForm->isSubmitted() && $deleteAccountForm->isValid()) {
            $security->logout(false);
            $entityManager->remove($user);
            $entityManager->flush();
            $request->getSession()->invalidate();
            return $this->redirectToRoute('articles.index');
        }

        return $this->render('dashboard/edit.html.twig', [
            'deleteAccountForm' => $deleteAccountForm,
        ]);
    }
}
