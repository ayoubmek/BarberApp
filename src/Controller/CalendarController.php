<?php
namespace App\Controller;
use App\Entity\CalendarEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;



use Symfony\Component\String\Slugger\AsciiSlugger; 
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CalendarController extends AbstractController
{
#[Route('/calendar', name: 'calendar_page', methods: ['GET'])]
public function calendar(EntityManagerInterface $em): Response
{
    $events = $em->getRepository(CalendarEvent::class)->findAll();

    $calendarEvents = array_map(function(CalendarEvent $e) {
        return [
            'id' => $e->getId(),
            'title' => $e->getTitle(),
            'description' => $e->getDescription(),
            'location' => $e->getLocation(),
            'start' => $e->getStart()->format('Y-m-d H:i:s'),
            'end' => $e->getEnd()->format('Y-m-d H:i:s'),
            'allDay' => $e->isAllDay(),
        ];
    }, $events);

    return $this->render('calendar/calendar.html.twig', [
        'events' => $calendarEvents
    ]);
}

#[Route('/calendar/list', name: 'calendar_index', methods: ['GET'])]
public function list(EntityManagerInterface $em, SessionInterface $session): Response
{ 
    if (!$session->get('logged_in')) {
        return $this->redirectToRoute('app_login');
    }

    // Fetch events
    $events = $em->getRepository(\App\Entity\CalendarEvent::class)->findAll();

    // Convert entities to array for FullCalendar
    $calendarEvents = [];
    foreach ($events as $event) {
       $calendarEvents[] = [
            'id' => $event->getId(),
            'title' => $event->getTitle(),
            'start' => $event->getStart()->format('Y-m-d\TH:i:s'),
            'end' => $event->getEnd() ? $event->getEnd()->format('Y-m-d\TH:i:s') : null,
            'description' => $event->getDescription(),
            'location' => $event->getLocation(),
        ];

    }

    // 🔥 Notice: we do NOT JSON-encode here. Let Twig handle it safely.
    $response = $this->render('calendar/liste.html.twig', [
        'events' => $calendarEvents,
    ]);

    $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
    $response->headers->set('Pragma', 'no-cache');
    $response->headers->set('Expires', '0');

    return $response;
}


#[Route('/add', name: 'calendar_add', methods: ['POST'])]
public function add(Request $request, EntityManagerInterface $em): JsonResponse
{
    try {
        $title = $request->request->get('title', 'No Title');
        $description = $request->request->get('description', '');
        $location = $request->request->get('location', '');
        $allDay = $request->request->get('allDay', 0);

        $startStr = $request->request->get('start');
        $endStr = $request->request->get('end');

        if (!$startStr || !$endStr) {
            return $this->json(['success' => false, 'message' => 'Start and End date required']);
        }

        $start = new \DateTime($startStr);
        $end = new \DateTime($endStr);

        $event = new CalendarEvent();
        $event->setTitle($title);
        $event->setDescription($description);
        $event->setLocation($location);
        $event->setAllDay((bool)$allDay);
        $event->setStart($start);
        $event->setEnd($end);

        $em->persist($event);
        $em->flush();

        return $this->json([
            'success' => true,
            'id' => $event->getId(),
            'title' => $title,
            'description' => $description,
            'location' => $location,
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $end->format('Y-m-d H:i:s'),
            'allDay' => (bool)$allDay
        ]);
    } catch (\Exception $e) {
        return $this->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}


    #[Route('/edit/{id}', name: 'calendar_edit', methods: ['POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $event = $em->getRepository(CalendarEvent::class)->find($id);
        if (!$event) return $this->json(['success' => false, 'message' => 'Event not found'], 404);

        $data = $request->request->all();
        $event->setTitle($data['title'] ?? $event->getTitle());
        $event->setDescription($data['description'] ?? $event->getDescription());
        $event->setLocation($data['location'] ?? $event->getLocation());
        $event->setAllDay(!empty($data['allDay']));
        $event->setStart(new \DateTime($data['start']));
        $event->setEnd(new \DateTime($data['end']));

        $em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/delete/{id}', name: 'calendar_delete', methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $event = $em->getRepository(CalendarEvent::class)->find($id);
        if (!$event) return $this->json(['success' => false, 'message' => 'Event not found'], 404);

        $em->remove($event);
        $em->flush();

        return $this->json(['success' => true]);
    }
}
