<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
use App\Service\FileUploader;
use App\Repository\FilmRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\DataProvider\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("/film")
 */
class FilmController extends AbstractController
{

    public function __invoke(Request $request, FileUploader $fileUploader): Film
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }
 
        // create a new entity and set its values
        $film = new Film();
        $film->name = $request->get('name');
        $film->description = $request->get('description');
        $film->note = $request->get('note');
        $film->released = $request->get('released');
        // upload the file and save its filename
        $film->cover = $fileUploader->upload($uploadedFile);
 
        return $film;
    }
    /**
     * @Route("/", name="app_film_index", methods={"GET"})
     */
    public function index(FilmRepository $filmRepository, PaginatorInterface $pagination, string $search): Response
    {
        if($search != null){
            return $this->render('film/index.html.twig', [
                'films' => $filmRepository->findBy($search),
            ]);
        }
        return $this->render('film/index.html.twig', [
            'films' => $filmRepository->findAll(),
           
        ]);
    }

    /**
     * @Route("/new", name="app_film_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FilmRepository $filmRepository, FileUploader $fileUploader): Response
    {
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }
 
        // create a new entity and set its values
        // upload the file and save its filename
        $film = new Film();
        $film->name = $request->get('name');
        $film->description = $request->get('description');
        $film->note = $request->get('note');
        $film->released = $request->get('released');
        $film->cover = $fileUploader->upload($uploadedFile);
 
         if ($form->isSubmitted() && $form->isValid()) {
            $filmRepository->add($film);
            return $this->redirectToRoute('app_film_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('film/new.html.twig', [
            'film' => $film,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_film_show", methods={"GET"})
     */
    public function show(Film $film): Response
    {
        return $this->render('film/show.html.twig', [
            'film' => $film,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_film_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Film $film, FilmRepository $filmRepository): Response
    {
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filmRepository->add($film);
            return $this->redirectToRoute('app_film_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('film/edit.html.twig', [
            'film' => $film,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_film_delete", methods={"POST"})
     */
    public function delete(Request $request, Film $film, FilmRepository $filmRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$film->getId(), $request->request->get('_token'))) {
            $filmRepository->remove($film);
        }

        return $this->redirectToRoute('app_film_index', [], Response::HTTP_SEE_OTHER);
    }
}
