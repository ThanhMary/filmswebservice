<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Search;
use App\Form\SearchType;
use App\Repository\FilmRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiFilmController extends AbstractController
{
    /**
     * @Route("/api/movies", name="api_film_index", methods={"GET"})
     * 
     * Permet de récupérer toutes les films
     */
    public function index(FilmRepository $filmReposistory, CategoryRepository $categoryRepository,PaginatorInterface $paginator,Request $request): Response
    {
        /** On récupère tout les films ainsi que les catégories */
        $categories = $categoryRepository->findAll();
        $films = $filmReposistory->findAll();
        /** On mets du JSON sur notre headers */
        $responseType = $this->getType($request->headers->get('Accept', 'application/json'));
        /** Deux pagination pour les films ainsi que les catégories */ 
        $paginationFilms = $paginator->paginate($films, $request->query->getInt('page', 1), 10);
        $paginationCategories = $paginator->paginate($categories, $request->query->getInt('page', 1), 10);
         
        //$responseType = $this->getType($request->headers->get('Accept', 'application/json'));  

        //On renvoie une information en JSON
        return $this->json([$paginationFilms, $paginationCategories], Response:: HTTP_OK, [

            'Content-Type' => ('json' === $responseType ? 'application/json' : 'application/xml')

        ]);
       
    }

     /**
     * @Route("/api/movies/{id}", name="api_one_film", methods={"GET"})
     * Permet de récupérer un film
     */
    public function getFilmsByCategory(FilmRepository $filmReposistory, int $id, Request $request): Response
    {
         
       $filmsByCat = $filmReposistory->find($id);

       if (empty($filmsByCat)) {

            $responseType = $this->getType($request->headers->get('Accept', 'application/json'));

            return $this->json([

                "message" => "Aucune information trouvée!"

            ], Response::HTTP_NOT_FOUND, [

                'Content-Type' =>('json' === $responseType ? 'application/json': 'application/xml')

            ]);

       } else {

            $responseType = $this->getType($request->headers->get('Accept', 'application/json'));

            return $this->json($filmsByCat, Response:: HTTP_OK, [

                'Content-Type'=>('json'===$responseType? 'application/json': 'application/xml')

            ]);

       }

    }

    
     /**
     * @Route("/api/movie/{id}", name="api_film_one", methods={"GET"})
     */
    public function getOne(FilmRepository $filmReposistory, Request $request, Film $film): Response
    {
       
        $responseType = $this->getType($request->headers->get('Accept', 'application/json'));

        return $this->json($film, Response:: HTTP_OK, [

            'Content-Type'=>('json' === $responseType? 'application/json': 'application/xml')

        ]);
    }

    /**
     * @Route("/api/movie", name="api_film_create", methods={"POST"})
     */
    public function Create(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator )   
    {
        $JsonRecu = ($request->getContent());

        try {

            $film = $serializer->deserialize($JsonRecu, Film::class, 'json');
            $errors = $validator->validate($film);

            if (count($errors) > 0) {

                return $this->json($errors,Response:: HTTP_NOT_ACCEPTABLE);

            }

            $manager->persist($film);
            $manager->flush();

            $responseType = $this->getType($request->headers->get('Accept', 'application/json'));   

            return $this->json($film, Response:: HTTP_CREATED, [

                'Content-Type'=>('json'===$responseType? 'application/json': 'application/xml')

            ]);

         } catch (NotEncodableValueException $e) {

            return $this->json([

                'status'=>400,
                'message'=> $e->getMessage()

            ], 400);

        }
        
    }

    /**
     * @Route("/api/movie/{id}", name="api_film_edit", methods={"PUT"})
     */
    public function Edit(Request $request, Film $film, FilmRepository $filmRepository, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator, int $id )   
    {

        try {

            $film = $filmRepository->find($id);
            $newData = $serializer->deserialize($request->getContent(), Film::class, 'json');
            $errors = $validator->validate($newData);

            if(count($errors)>0) {

                return $this->json($errors, 201);

            }

            if ($newData->getName()) {
                
                $film->setName($newData->getName());

            }

            if ($newData->getDescription()) {
                
                $film->setDescription($newData->getDescription());
                
            }

            if ($newData->getReleased()) {
                
                $film->setReleased($newData->getReleased());

            }

            if ($newData->getNote()) {
                
                $film->setNote($newData->getNote());

            }

            if ($newData->getCategory()) {

                $film->setCategory($newData->getCategory());

            }

            $manager->flush();

            $responseType = $this->getType($request->headers->get('Accept', 'application/json'));    

            return $this->json($film, Response::HTTP_OK, [

                'Content-Type' => ('json' === $responseType ? 'application/json' : 'application/xml')

            ]);

        } catch (NotEncodableValueException $e) {

            return $this->json([

                'status' => 400,
                'message' => $e->getMessage()

            ], Response::HTTP_NOT_FOUND);

        }
        
    }

    /**
     * @Route("/api/movie/{id}", name="api_film_delete", methods={"DELETE"})
     */
    public function Delete(Request $request, Film $film, FilmRepository $filmRepository, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator )   {
      
        if(!$film){

            return $this->json([
                'status'=>400,
                'message'=> "NOT FOUND"
            ], Response::HTTP_NOT_FOUND);

        }

        try{

            $manager->remove($film);
            $manager->flush();
            return $this->json(null, Response::HTTP_OK);

        }catch (NotEncodableValueException $e){

            return $this->json([

                'status'=> null,
                'message'=> $e->getMessage()
                
            ], 400);

        }

    }

    private function getType(string $mime){

       return  'application/json' === $mime? 'json': 'xml';    

    }

}
