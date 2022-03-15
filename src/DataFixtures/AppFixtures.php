<?php
namespace App\DataFixtures;

use App\Entity\Category;
use Faker;
use App\Entity\Film;
use DateTime;
use DateTimeInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 20; $i++) {
            $cat = new Category();
            $cat->setName($faker->sentence($nbWords = 1, $variableNbWords = true));
            $manager->persist($cat);
        }
    
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

        /* $category = new Category();
        $category->setName('Fantastique');
        $manager->persist($category);

        $film = new Film();
        $film
            ->setName('Warcraft')
            ->setNote(3)
            ->setDescription("Le pacifique royaume d'Azeroth est au bord de la guerre alors que sa civilisation doit faire face à une redoutable race d’envahisseurs: des guerriers Orcs fuyant leur monde moribond pour en coloniser un autre. Alors qu’un portail s’ouvre pour connecter les deux mondes, une armée fait face à la destruction et l'autre à l'extinction. De côtés opposés, deux héros vont s’affronter et décider du sort de leur famille, de leur peuple et de leur patrie.")
            ->setReleased(new DateTime("2016-05-25 12:30:00"))
            ->setCategory($category);
        ;
        $manager->persist($film);
        $manager->flush(); */
    }
    
}

