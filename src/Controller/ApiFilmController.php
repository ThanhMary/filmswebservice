<?php

namespace App\Controller;


use App\Entity\Film;
use App\Entity\Search;
use App\Repository\FilmRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\DataProvider\PaginatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiFilmController extends AbstractController
{
    /**
     * @Route("/api/movies", name="api_film_index", methods={"GET"})
     */
    public function index(FilmRepository $filmReposistory, CategoryRepository $categoryRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $cats = $categoryRepository->findAll();

        $responseType = $this->getType($request->headers->get('Accept', 'application/json'));  
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        if($search == null){
            $films= $paginator->getTotalItems($filmReposistory->findAll(),
            $request->query->getInt('page', 1),10 );// 10 maximum par page
         
            $responseType = $this->getType($request->headers->get('Accept', 'application/json'));  
            return $this->json([$films, $cats], Response:: HTTP_OK, [
                'Content-Type'=>('json'===$responseType? 'application/json': 'application/xml')
            ]);
        }else{
          
            $films= $paginator->getTotalItems($this->filmReposistory->filter($search),
            $request->query->getInt('page', 1),10 );// 10 maximum par page

            return $this->json([$films, $cats], Response:: HTTP_OK, [
                'Content-Type'=>('json'===$responseType? 'application/json': 'application/xml')
            ]);

        }
       
    }

     /**
     * @Route("/api/movies/{id}", name="api_film_byCat", methods={"GET"})
     */
    public function getFilmsByCategory(FilmRepository $filmReposistory, int $id, Request $request): Response
    {
         
       $filmsByCat = $filmReposistory->findByCategory($id);
        $responseType = $this->getType($request->headers->get('Accept', 'application/json'));  
        return $this->json($filmsByCat, Response:: HTTP_OK, [
            'Content-Type'=>('json'===$responseType? 'application/json': 'application/xml')
        ]);
    }

    
     /**
     * @Route("/api/movie/{id}", name="api_film_one", methods={"GET"})
     */
    public function getOne(FilmRepository $filmReposistory, Request $request, Film $film): Response
    {
       
        $responseType = $this->getType($request->headers->get('Accept', 'application/json'));    
        return $this->json($film, Response:: HTTP_OK, [
            'Content-Type'=>('json'===$responseType? 'application/json': 'application/xml')
        ]);
    }

    /**
     * @Route("/api/movie/new", name="api_film_create", methods={"POST"})
     */
    public function Create(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator )   
    {
        $JsonRecu = $request->getContent();
        try{
            $film = $serializer->deserialize($JsonRecu, Post:: class, 'json');
            $film->setReleased(new \DateTime());
            $errors = $validator->validate($film);
            if(count($errors)>0){
                return $this->json($errors,Response:: HTTP_NOT_ACCEPTABLE);
            }
            $manager->persist($film);
            $manager->flush();

            $responseType = $this->getType($request->headers->get('Accept', 'application/json'));   
                    return $this->json($film, Response:: HTTP_CREATED, [
                        'Content-Type'=>('json'===$responseType? 'application/json': 'application/xml')
                    ]);
         }catch (NotEncodableValueException $e){
            return $this->json([
                'status'=>400,
                'message'=> $e->getMessage()
            ], 400);
        }
        
    }
    /**
     * @Route("/api/movieEdit/{id}", name="api_film_edit", methods={"POST", "GET"})
     */
    public function Edit(Request $request,Film $film, FilmRepository $filmRepository, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator )   {
   
        try{
            $movie = $serializer->deserialize($film, Post:: class, 'json');
            $movie->setReleased(new \DateTime());
            $errors = $validator->validate($movie);
            if(count($errors)>0){
                return $this->json($errors, 201);
            }
            $manager->persist($movie);
            $manager->flush();
            $responseType = $this->getType($request->headers->get('Accept', 'application/json'));    
            return $this->json($movie, Response::HTTP_OK, [
                'Content-Type'=>('json'===$responseType? 'application/json': 'application/xml')
            ]);

        }catch (NotEncodableValueException $e){
            return $this->json([
                'status'=>400,
                'message'=> $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }    
    }


      /**
     * @Route("/api/movieDelete/{id}", name="api_film_delete", methods={"DELETE"})
     */
    public function Delete(Request $request, Film $film, FilmRepository $filmRepository, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator )   {
      
        if(!$film){
                return $this->json([
                    'status'=>400,
                    'message'=> "NOT FOUND"
                ], Response::HTTP_NOT_FOUND);
        }
        try{
            $film = $serializer->deserialize($film, Post:: class, 'json');
            $film->setReleased(new \DateTime());
            $errors = $validator->validate($film);
            if(count($errors)>0){
                return $this->json($errors, 201);
            }

            $manager->remove($film);
            $manager->flush();
            return $this->json(null, Response::HTTP_NO_CONTENT);

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
