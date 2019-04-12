<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Jeux;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Comment;

class Jeuxfixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker=\Faker\Factory::create('fr_FR');
        for ($k=1;$k<= 3; $k++){
            $category= new Category();
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());
            $manager->persist($category);


        }
        //crée 4 à 6 jeux

        for ($i=1;$i<=10;$i++){
         $jeux=new Jeux();

         $content='<p>'.join($faker->paragraphs(5),'</p><p>').'</p>';

                $jeux->setTitle($faker->sentence())
             ->setDescription($content)
             ->setImage('http://placehold.it//350x150')
             ->setCreatedAt($faker->dateTimeBetween('-6months'))
             ->setCategory($category);

                $manager->persist($jeux);

                //commentaires pour le jeux

            for ($k=1;$k<=mt_rand(4,10);$k++){

                $comment= new Comment();

                $content='<p>'.join($faker->paragraphs(2),'</p><p>').'</p>';

                $days=(new \DateTime())->diff($jeux->getCreatedAt())->days;
                $comment->setAuteur($faker->name)
                        ->setContent($content)
                    ->setCreatedAt($faker->dateTimeBetween('-'.$days.'days'))
                    ->setJeux($jeux);
                $manager->persist($comment);


            }
        }
        $manager->flush();
    }
}
