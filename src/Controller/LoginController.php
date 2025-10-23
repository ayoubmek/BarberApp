<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class LoginController extends AbstractController
{
   #[Route('/', name: 'app_login', methods: ['GET', 'POST'])]
public function login(Request $request, SessionInterface $session): Response
{
    if ($request->isMethod('POST')) {
        $username = $request->request->get('username');
        $password = $request->request->get('password');
 
        if ($username === 'admin' && $password === '4321') {
            $session->set('logged_in', true);
            $session->set('role', 'admin');
            return $this->redirectToRoute('app_team');
        } 
        
        $this->addFlash('error', 'Access restricted to admin only!');
    }

    return $this->render('login/index.html.twig');
}

#[Route('/logout', name: 'app_logout')]
public function logout(SessionInterface $session): Response
{
    $session->clear();

    $response = $this->redirectToRoute('app_login');
    $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    $response->headers->set('Pragma', 'no-cache');
    $response->headers->set('Expires', '0');

    return $response;
}


}
