<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Category;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserRepository $userRepo, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->userRepo = $userRepo;
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $faker->seed(1337);

        for ($u = 1; $u <= 10; $u++) {
            $user = new User();
            $user->setEmail(sprintf("email+%d@snowtrick.fr", $u));
            $user->setPseudo($faker->firstName($gender = 'male' | 'female'));
            $user->setAvatar($faker->imageUrl($width = 50, $height = 50));


            if ($u === 1)
                $user->setRoles(['ROLE_ADMIN']);
            else
                $user->setRoles(['ROLE_USER']);


            $user->setPassword(
                $this->passwordEncoder->hashPassword(
                    $user,
                    '000000'
                )
            );
            $user->setIsVerified($faker->numberBetween(0, 1));
            $manager->persist($user);

            $this->addReference('user_' . $u, $user);
            $manager->flush();
        }
        $categories = ['Expert', 'Pro', 'Intermédiaire', 'Amateur', 'Débutant'];
        $tricks = [
            'Big air', 'Hip', 'Step-up', 'Half-pipe', 'Quarter-pipe', 'Barre de slide',
            'Waterslide', 'Box', 'Wall', 'Corner', 'Kicker', 'Road gap'
        ];

        foreach ($categories as $key => $item) {
            $category = new Category();
            $category->setName($item);
            $manager->persist($category);

            $this->addReference('cat_' . $key, $category);
        }

        foreach ($tricks as $key => $item) {
            $catego = $this->getReference('cat_' . $faker->numberBetween(1, 3));

            $trick = new Trick();
            $trick->setName($item);
            $trick->setContent($faker->sentences(12, true));
            $trick->setCategory($catego);
            $manager->persist($trick);

            for ($j = 0; $j <= rand(1, 3); $j++) {
                $users = $this->getReference('user_' . $faker->numberBetween(1, 5));

                $comment = new Comment();
                $comment->setContent($faker->sentences(random_int(1, 2), true));
                $comment->setTrick($trick);
                $comment->setUser($users);
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
}