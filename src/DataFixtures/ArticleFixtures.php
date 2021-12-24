<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Article;
use App\Entity\Comment;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
//use Faker\Factory;



class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //$faker = Factory::create();
        for ($i = 1; $i < 5; $i++) {

            $category = new Category();
            $category->setTitle('Titre  ' . $i);
            $category->setDescription('Description' . $i);
            $manager->persist($category);


            for ($j = 1; $j < 3; $j++) {
                $article = new Article();
                $article->setTitle('Titre  ' . $j);
                $article->setContent('czeiufehihfifzhfuizfhzifhzifuezfheuifzhfzifz');
                $article->setCreatedAt(new \DateTime());
                $article->setImage("https://picsum.photos/seed/picsum/300/150");
                $article->setCategory($category);
                $manager->persist($article);


                for ($k = 1; $k <= mt_rand(1, 6); $k++) {
                    $comment = new Comment();

                    $comment->setAuthor('utilisateur ' . $k);
                    $comment->setContent('czeiufehihfifzhfuizfhzifhzifuezfheuifzhfzifz');
                    $comment->setCreatedAt(new \DateTimeImmutable());
                    $comment->setArticle($article);
                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
