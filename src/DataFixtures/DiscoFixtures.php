<?php

namespace App\DataFixtures;

use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Artiste;
use App\Entity\Type;
use App\Entity\Chanson;

class DiscoFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Génération des types
        $types = ["Auteur", "Compositeur", "Interprète", "Musicien", "Chanteur", "Groupe"];
        $typeEntities = [];
        
        foreach ($types as $typeName) {
            $type = (new Type())->setType($typeName)
                                ->setDescription($faker->paragraph());

            $manager->persist($type);
            $typeEntities[] = $type;
        }

        $manager->flush();
        
        // Génération des artistes
        $artistes = [];
        for ($i = 0; $i < 50; $i++) {
            $dateU = DateTimeImmutable::createFromMutable($faker->dateTime());
            $artiste = (new Artiste())->setNom($faker->lastName())
                                       ->setPrenom($faker->firstName())
                                       ->setDateNaissance($dateU)
                                       ->setLieuNaissance($faker->city())
                                       ->setPhoto("https://picsum.photos/360/360?image=".($i+1))
                                       ->setDescription($faker->paragraph());
            
            // Associer un type aléatoire à l'artiste
            $artiste->setType($faker->randomElement($typeEntities));

            $manager->persist($artiste);
            $artistes[] = $artiste;
        }

        $manager->flush();
        
        // Génération des chansons
        for ($i = 0; $i < 50; $i++) {
            $chanson = (new Chanson())->setTitre($faker->sentence(3))
                                       ->setDateSortie($faker->dateTime())
                                       ->setGenre($faker->word())
                                       ->setLangue($faker->languageCode())
                                       ->setPhotoCouverture("https://picsum.photos/360/360?image=".($i+50))
                                       ->addArtiste($faker->randomElement($artistes));

            $manager->persist($chanson);
        }

        $manager->flush();
    }
}