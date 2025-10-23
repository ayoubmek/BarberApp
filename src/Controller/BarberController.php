<?php

namespace App\Controller;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BarberController extends AbstractController
{
    #[Route('/barber', name: 'app_barber')]
    public function index(UserRepository $repo): Response
    {
          return $this->render('barber/barber.html.twig', [
        'barbers' => $repo->findBy(['role' => 'barber']),
    ]);
    }
}
