<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Likes;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Liior\Faker\Prices;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $encoder;

    public function __construct( UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $users=[];
        $faker = Factory::create();
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new Prices($faker));

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setPassword($this->encoder->encodePassword($user, "password"));
            $manager->persist($user);
            $users[] = $user;
        }

        for ($i = 0; $i < 20; $i++) {
            $article = new Article();
            $article->setTitle($faker->text(50));
            $article->setDescription('<p>' . join("," ,$faker->paragraphs()) .'</p>');
            $article->setCreatedAt(new \DateTimeImmutable("now"));
            $article->setPrice($faker->price(20, 200));
            $manager->persist($article);

//            for ($i = 0; $i < mt_rand(0, 10); $i++) {
//                $like = new Likes();
//                $like
//                    ->setArticle($article)
//                    ->setUser($faker->randomElement($users))
//                ;
//                $manager->persist($like);
//            }
        }

        $manager->flush();
    }
}
