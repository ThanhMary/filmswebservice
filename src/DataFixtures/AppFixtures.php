<?php
namespace App\DataFixtures;

use App\Entity\Category;
use Faker;
use App\Entity\Film;
use DateTimeInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //  $faker = Faker\Factory::create('fr_FR');
        $faker = Faker\Factory::create('fr_FR');
        $cat = new Category();
        for ($i = 0; $i < 20; $i++) {
            $cat = new Category();
            $cat->setName($faker->sentence($nbWords = 1, $variableNbWords = true));
            $manager->persist($cat);
        }


         $film = new Film();

       
         for ($i = 0; $i < 20; $i++) {
            $film = new Film();
            $film->setName($faker->sentence($nbWords = 6, $variableNbWords = true));
            $film->setNote(mt_rand(1, 5));
            $film->setDescription($faker->sentence($nbWords =200, $variableNbWords = true));
            $film->setReleased(new \DateTime());
            $film->setCategory($cat);
            $manager->persist($film);
        }
 

        $manager->flush();
    }
    
}

