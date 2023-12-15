<?php

namespace App\Controller;

use App\Form\ChansonType;
use App\Entity\Chanson;
use App\Repository\ArtisteRepository;
use App\Repository\ChansonRepository;
use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

function calculateAge($dateOfBirth) {
    $dob = new DateTime($dateOfBirth);
    $now = new DateTime();
    $age = $dob->diff($now);
    return $age->y;
}

#[Route("/discotheque")]
class DiscoController extends AbstractController
{
    #[Route('/', name: 'app_discotheque')]
    public function index(ChansonRepository $chansonRepository, TypeRepository $typeRepository): Response
    {
        return $this->render('discotheque/index.html.twig', [
            'title' => 'Discotheque',
            'chansons' => $chansonRepository->findAll(),
            'types' => $typeRepository->findAll(),
        ]);
    }

    #[Route('/chanson/create/', name: 'app_chanson_create', methods: ['GET', 'POST'])]
    public function createChanson(Request $request, EntityManagerInterface $entityManager, TypeRepository $typeRepository, ArtisteRepository $artisteRepository): Response {
        $chanson = new Chanson();
        $form = $this->createForm(ChansonType::class, $chanson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chanson);
            $entityManager->flush();

            $this->addFlash('notice', "La chanson a bien été créée");

            return $this->redirectToRoute('app_discotheque');
        }


        return $this->render('chanson/create_chanson.html.twig', [
            'title' => 'Création d\'une chanson',
            'form' => $form->createView(),
            'types' => $typeRepository->findAll(),
            'artistes' => $artisteRepository->findAll(),
            'randomPhoto' => 'https://picsum.photos/360/360?image=' . rand(1, 50),
        ]);
    }

    #[Route('/chanson/{slug}', name: 'app_chanson_consult', methods: ['GET', 'POST'])]
    public function consultChanson(Request $request, TypeRepository $typeRepository, ChansonRepository $chansonRepository, string $slug, EntityManagerInterface $entityManager): Response {
        $chanson = $chansonRepository->findOneById($slug);
        $form = $this->createForm(ChansonType::class, $chanson);
        $form->handleRequest($request);
        $now = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('notice', "La chanson a bien été modifiée");
            
            return $this->redirectToRoute('app_discotheque', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chanson/consult_chanson.html.twig', [
            'title' => 'Consultation d\'une chanson',
            'form' => $form->createView(),
            'chanson' => $chanson,
            'types' => $typeRepository->findAll(),
            'now' => $now,
        ]);
    }

    #[Route('/{id}', name: 'app_chanson_delete', methods: ['POST'])]
    public function delete(Request $request, Chanson $chanson, EntityManagerInterface $entityManager): Response
    {
        if (true) {
            $entityManager->remove($chanson);
            $entityManager->flush();

            $this->addFlash('notice', "La chanson a bien été supprimée");

        }

        return $this->redirectToRoute('app_discotheque', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/artiste/filter/{slug}', name: 'app_artiste_filter')]
    public function filterChanson(TypeRepository $typeRepository, ChansonRepository $chansonRepository, string $slug): Response {
        $types = $typeRepository->findAll();
        $type = $typeRepository->findOneById($slug);

        $songs = $chansonRepository->findAll();

        $artistes = [];

        if ($type) $artistes = $type->getArtistes();

        $countSongPerArtist = [];

        foreach ($songs as $song) {
            $songArtistes = $song->getArtistes();
            foreach ($songArtistes as $artiste) {
                if (array_key_exists($artiste->getId(), $countSongPerArtist)) {
                    $countSongPerArtist[$artiste->getId()]++;
                } else {
                    $countSongPerArtist[$artiste->getId()] = 1;
                }
            }
        }

        return $this->render('artistes/filter_artiste.html.twig', [
            'title' => 'Artistes par type',
            'types' => $types,
            'artistes' => $artistes,
            'type' => $type,
            'countSongPerArtist' => $countSongPerArtist,
        ]);
        

    }

    #[Route('/artiste/consult/{slug}', name: 'app_artiste_consult')]
    public function consultArtiste(TypeRepository $typeRepository, ArtisteRepository $artisteRepository, ChansonRepository $chansonRepository, string $slug): Response {
        $types = $typeRepository->findAll();
        $artiste = $artisteRepository->findOneById($slug);
        $songs = $chansonRepository->findAll();
        $songFromArtiste = [];

        foreach ($songs as $song) {
            $songArtistes = $song->getArtistes();
            foreach ($songArtistes as $artiste) {
                if ($artiste->getId() == $slug) {
                    $songFromArtiste[] = $song;
                }
            }
        }

        $artiste = $artisteRepository->findOneById($slug);
        
        return $this->render('artistes/consult_artist.html.twig', [
            'title' => 'Consultation d\'un artiste',
            'types' => $types,
            'artiste' => $artiste,
            'songFromArtiste' => $songFromArtiste,
            'age' => calculateAge($artiste->getDateNaissance()->format('Y-m-d')),
        ]);
    }

}