<?php

namespace App\DataFixtures;

use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $user = new User();
        $email = 'test@test.pl';
        $user->setEmail($email);
        $user->setName($faker->name);
        $password = $this->hasher->hashPassword($user, $email);
        $user->setPassword($password);
        $manager->persist($user);

        for ($j = 1; $j <= 5; $j++) {
            $article = new Article();
            $article->setTitle($faker->sentence(10));
            $article->setText($faker->text(3000));
            $number = $faker->numberBetween(-100, -2);
            $article->setCreatedAt(new \DateTimeImmutable($number . ' days'));
            $article->addUser($user);
            $manager->persist($article);
        }

        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $email = $faker->email;
            $user->setEmail($email);
            $user->setName($faker->name);
            $password = $this->hasher->hashPassword($user, $email);
            $user->setPassword($password);
            $manager->persist($user);

            for ($j = 1; $j <= 5; $j++) {
                $article = new Article();
                $article->setTitle($faker->sentence(10));
                $article->setText($faker->text(3000));
                $number = $faker->numberBetween(-100, -2);
                $article->setCreatedAt(new \DateTimeImmutable($number . ' days'));
                $article->addUser($user);
                $manager->persist($article);
            }
        }

        $manager->flush();
    }
}
