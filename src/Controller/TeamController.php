<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class TeamController extends AbstractController
{
    #[Route('/team', name: 'app_team')]
    public function index(SessionInterface $session, UserRepository $userRepository): Response
    {
        // ✅ Check if user is logged in and admin
        if (!$session->get('logged_in') || $session->get('role') !== 'admin') {
            return $this->redirectToRoute('app_login');
        }

        // ✅ Fetch only barbers from the database
        $barbers = $userRepository->findBy(['role' => 'barber']);

        return $this->render('team/team.html.twig', [
            'barbers' => $barbers,
        ]);
    }
 
}
