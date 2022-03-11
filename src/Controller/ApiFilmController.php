<?php

namespace App\Controller;


use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiFilmController extends AbstractController
{
    /**
     * @Route("/api/movies", name="api_film_index", methods={"GET"})
     */
    public function index(FilmRepository $filmReposistory, Request $request): Response
    {
        $films = $filmReposistory->findAll();
      
        $responseType = $this->getType($request->headers->get('Accept', 'application/json'));  
        return $this->json($films, Response:: HTTP_OK, [
            'Content-Type'=>('json'===$responseType? 'application/json': 'application/xml')
        ]);
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
     * @Route("/api/movies/{id}", name="api_film_one", methods={"GET"})
     */
    public function getOne(FilmRepository $filmReposistory, int $id, Request $request): Response
    {
        $film = $filmReposistory->findOneBy($id);
        $responseType = $this->getType($request->headers->get('Accept', 'application/json'));    
        return $this->json($film, Response:: HTTP_OK, [
            'Content-Type'=>('json'===$responseType? 'application/json': 'application/xml')
        ]);
    }

    /**
     * @Route("/api/movies", name="api_film_create", methods={"POST"})
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
     * @Route("/api/movies/{id}", name="api_film_edit", methods={"POST", "GET"})
     */
    public function Edit(Request $request, int $id, FilmRepository $filmRepository, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator )   {

        $movie = $filmRepository->findOneBy($id);
   
        try{
            $film = $serializer->deserialize($movie, Post:: class, 'json');
            $film->setReleased(new \DateTime());
            $errors = $validator->validate($film);
            if(count($errors)>0){
                return $this->json($errors, 201);
            }
            $manager->persist($film);
            $manager->flush();
            $responseType = $this->getType($request->headers->get('Accept', 'application/json'));    
            return $this->json($film, Response::HTTP_OK, [
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
     * @Route("/api/movies/{id}", name="api_film_delete", methods={"DELETE"})
     */
    public function Delete(Request $request, int $id, FilmRepository $filmRepository, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator )   {

        $movie = $filmRepository->findOneBy($id);
        if(!$movie){
                return $this->json([
                    'status'=>400,
                    'message'=> "NOT FOUND"
                ], Response::HTTP_NOT_FOUND);
          
        }
        try{
            $film = $serializer->deserialize($movie, Post:: class, 'json');
            $film->setReleased(new \DateTime());
            $errors = $validator->validate($film);
            if(count($errors)>0){
                return $this->json($errors, 201);
            }

            $manager->remove($film);
            $manager->flush();
            $responseType = $this->getType($request->headers->get('Accept', 'application/json'));    
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
