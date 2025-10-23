<?php
namespace App\Controller;
use App\Entity\Payment; 
use App\Entity\History;  
use App\Entity\Service;  
use App\Form\ServiceType; 
use App\Entity\BarberWallet; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Repository\BarberWalletRepository; 
use App\Repository\HistoryRepository;
use App\Repository\CategoryRepository;
use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse; 


use Symfony\Component\String\Slugger\AsciiSlugger; 
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class ServiceController extends AbstractController
{ 

 #[Route('/service/list', name: 'service_index', methods: ['GET'])]
public function list(EntityManagerInterface $em, SessionInterface $session): Response
{ 
    if (!$session->get('logged_in')) {
        return $this->redirectToRoute('app_login');
    }

    $services = $em->getRepository(Service::class)->findAll();
 
    $response = $this->render('service/index.html.twig', [
        'services' => $services,
    ]);
 
    $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
    $response->headers->set('Pragma', 'no-cache');
    $response->headers->set('Expires', '0');

    return $response;
}

 
    #[Route('/service/new', name: 'service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($service);
            $em->flush();

            $this->addFlash('success', 'Service created successfully.');
            return $this->redirectToRoute('service_index');
        }

        return $this->render('service/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

 
    #[Route('/service/{id}/edit', name: 'service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Service updated successfully.');
            return $this->redirectToRoute('service_index');
        }

        return $this->render('service/edit.html.twig', [
            'form' => $form->createView(),
            'service' => $service,
        ]);
    }

    #[Route('/serviceD/{id}', name: 'service_delete', methods: ['GET', 'POST'])]
public function delete(Request $request, int $id, EntityManagerInterface $em): RedirectResponse
{
    $service = $em->getRepository(Service::class)->find($id);
    if (!$service) {
        $this->addFlash('error', 'Service not found.');
        return $this->redirectToRoute('service_index');
    }

    if ($request->isMethod('POST') && $this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
        $em->remove($service);
        $em->flush();
        $this->addFlash('success', 'Service deleted successfully.');
    }

    return $this->redirectToRoute('service_index');
}

 
    #[Route('/service', name: 'app_service', methods: ['GET'])]
    public function index1(
        Request $request,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository
    ): Response
    {
        $id = (int) $request->query->get('id');
        $barber = $userRepository->find($id);

        return $this->render('service/service.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'invoiceId' => (int) $request->query->get('id'),
            'barber'     => $barber,
            'barberId'   => $id,
        ]);
    }

    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
public function profile(
    Request               $request,
    CategoryRepository    $categoryRepository,
    UserRepository        $userRepository,
    PaymentRepository     $paymentRepository,
    EntityManagerInterface $em
): Response {
    $id = (int) $request->query->get('id');
    $barber = $userRepository->find($id);
    if (!$barber) {
        throw $this->createNotFoundException('Barber not found');
    }

    $haircutCount = $paymentRepository->count(['user' => $barber]);
    $totalAmount = (float) $em
        ->createQueryBuilder()
        ->select('COALESCE(SUM(w.balance), 0)')
        ->from(BarberWallet::class, 'w')
        ->where('w.barber = :barber')
        ->setParameter('barber', $barber)
        ->getQuery()
        ->getSingleScalarResult();

    return $this->render('profile/profile.html.twig', [
        'categories'   => $categoryRepository->findAll(),
        'barber'       => $barber,
        'barberId'     => $id,
        'haircutCount' => $haircutCount,
        'totalAmount'  => $totalAmount,
    ]);
}
    #[Route('/service/pay', name: 'service_pay', methods: ['POST'])]
public function pay(
    Request $request,
    EntityManagerInterface $em,
    UserRepository $userRepository,
    HistoryRepository $historyRepository,
    BarberWalletRepository $walletRepo
): JsonResponse {
    if ($this->container->has('profiler')) {
        $this->container->get('profiler')->disable();
    }

    $data     = json_decode($request->getContent(), true);
    $amount   = $data['amount']   ?? null;
    $barberId = $data['barberId'] ?? null;

    if (!$amount || !is_numeric($amount) || !$barberId) {
        return new JsonResponse(['error' => 'Invalid data'], 400);
    }

    /* ---- selected barber ---- */
    $barber = $userRepository->find($barberId);
    if (!$barber) {
        return new JsonResponse(['error' => 'Barber not found'], 404);
    }

    /* ---- admin barber (id = 5) ---- */
    $admin = $userRepository->find(5);
    if (!$admin || $admin->getRole() !== 'admin') {
        return new JsonResponse(['error' => 'Admin barber (id 5) not found or not admin'], 404);
    }

    /* ================================================================
     |  1.  PAYMENT + HISTORY FOR SELECTED BARBER
     * ================================================================ */
    $paymentBarber = new Payment();
    $paymentBarber->setUser($barber);
    $paymentBarber->setAmount((string) $amount);
    $paymentBarber->setPaymentType('cash');
    $em->persist($paymentBarber);

    $historyBarber = new History();
    $historyBarber->setUser($barber);
    $historyBarber->setType('payment');
    $historyBarber->setContenu(
        sprintf('Cash payment received: %s DT from barber %s', $amount, $barber->getName())
    ); 
    $em->persist($historyBarber);

    /* ================================================================
     |  2.  PAYMENT + HISTORY FOR ADMIN
     * ================================================================ */
    $paymentAdmin = new Payment();
    $paymentAdmin->setUser($admin);
    $paymentAdmin->setAmount((string) $amount);
    $paymentAdmin->setPaymentType('cash');
    $em->persist($paymentAdmin);

 

    /* ================================================================
     |  3.  WALLET UPDATE FOR SELECTED BARBER
     * ================================================================ */
    $walletBarber = $walletRepo->findOneBy(['barber' => $barber]);
    if (!$walletBarber) {
        $walletBarber = new BarberWallet();
        $walletBarber->setBarber($barber);
        $walletBarber->setBalance((string) $amount);
        $walletBarber->setTotalEarned((string) $amount);
        $em->persist($walletBarber);
    } else {
        $newBalanceBarber     = (float) $walletBarber->getBalance() + (float) $amount;
        $newTotalEarnedBarber = (float) $walletBarber->getTotalEarned() + (float) $amount;
        $walletBarber->setBalance(number_format($newBalanceBarber, 2, '.', ''));
        $walletBarber->setTotalEarned(number_format($newTotalEarnedBarber, 2, '.', ''));
    }
    $paymentBarber->setWallet($walletBarber);

    /* ================================================================
     |  4.  WALLET UPDATE FOR ADMIN
     * ================================================================ */
    $walletAdmin = $walletRepo->findOneBy(['barber' => $admin]);
    if (!$walletAdmin) {
        $walletAdmin = new BarberWallet();
        $walletAdmin->setBarber($admin);
        $walletAdmin->setBalance((string) $amount);
        $walletAdmin->setTotalEarned((string) $amount);
        $em->persist($walletAdmin);
    } else {
        $newBalanceAdmin     = (float) $walletAdmin->getBalance() + (float) $amount;
        $newTotalEarnedAdmin = (float) $walletAdmin->getTotalEarned() + (float) $amount;
        $walletAdmin->setBalance(number_format($newBalanceAdmin, 2, '.', ''));
        $walletAdmin->setTotalEarned(number_format($newTotalEarnedAdmin, 2, '.', ''));
    }
    $paymentAdmin->setWallet($walletAdmin);

    /* --------  flush everything  -------- */
    $em->flush();

    return new JsonResponse([
        'ok'                => true,
        'paymentIdBarber'   => $paymentBarber->getId(),
        'paymentIdAdmin'    => $paymentAdmin->getId(),
        'walletIdBarber'    => $walletBarber->getId(),
        'walletIdAdmin'     => $walletAdmin->getId(),
        'newBalanceBarber'  => $walletBarber->getBalance(),
        'totalEarnedBarber' => $walletBarber->getTotalEarned(),
        'newBalanceAdmin'   => $walletAdmin->getBalance(),
        'totalEarnedAdmin'  => $walletAdmin->getTotalEarned(),
    ]);
}
    #[Route('/payments', name: 'app_payments')]
    public function index(
        UserRepository      $userRepository,
        HistoryRepository   $historyRepository,
        BarberWalletRepository   $barberWalletRepository,
        PaymentRepository   $paymentRepository
    ): Response {
        $barbers   = $userRepository->findBy(['role' => 'barber']);
        $histories = $historyRepository->findBy([], ['date' => 'DESC']);
        $barberWallets = $barberWalletRepository->findBy([], ['date' => 'DESC']);
        $payments  = $paymentRepository->findBy([], ['paymentDate' => 'DESC']);

        return $this->render('payment/liste.html.twig', [
            'barbers'   => $barbers,
            'histories' => $histories,
            'payments'  => $payments,
            'barberWallets'  => $barberWallets,

        ]);
    }

    #[Route('/service/zero-balance', name: 'service_zero_balance', methods: ['POST'])]
    public function zeroBalance(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepo,
        BarberWalletRepository $walletRepo
    ): JsonResponse {
        $data     = json_decode($request->getContent(), true);
        $barberId = $data['barberId'] ?? null;

        if (!$barberId) {
            return new JsonResponse(['error' => 'Missing barberId'], 400);
        }

        $barber = $userRepo->find($barberId);
        if (!$barber) {
            return new JsonResponse(['error' => 'Barber not found'], 404);
        }

        $wallet = $walletRepo->findOneBy(['barber' => $barber]);
        if (!$wallet) {
            $wallet = new BarberWallet();
            $wallet->setBarber($barber);
            $wallet->setTotalEarned('0');   
        }

        $wallet->setBalance('0.00');        
        $em->persist($wallet);
        $em->flush();

        return new JsonResponse(['ok' => true, 'newBalance' => $wallet->getBalance()]);
    }


    #[Route('/service/delete-all-history', name: 'history_delete_all', methods: ['DELETE'])]
    public function deleteAllHistory(HistoryRepository $historyRepo): JsonResponse
    {
        $historyRepo->createQueryBuilder('h')
                    ->delete()
                    ->getQuery()
                    ->execute();

        return new JsonResponse(['ok' => true]);
    }
 
}