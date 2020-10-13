<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Likes;
use App\Repository\ArticleRepository;
use App\Repository\LikesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function index(ArticleRepository $repo)
    {
        return $this->render('article/index.html.twig', [
            'posts' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/article/{id}", name="single_page")
     */
    public function pages(ArticleRepository $repo, Article $article)
    {
        $article = $repo->find($article->getId());
        dd($article);
        return $this->render('article/pages.html.twig', [
            'posts' => $repo->findAll(),
        ]);
    }
    /**
     * @Route("/articleLikes/{id}/likes", name="likes")
     */
    public function likes(LikesRepository $repo, EntityManagerInterface $manager, Article $article): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) return $this->json(['code'=> 403,'messages'=> "Tu n'es pas connecté"],403);

        if ($article->getLikesByUser($user)){
            $likes = $repo->findOneBy(array("article" => $article, "user" => $user));

            $manager->remove($likes);
            $manager->flush();

            return $this->json([
                'code'=> 200,
                'messages'=> "like retiré !",
                'likes' => $repo->count(["article"=> $article])
            ],200);
        }

        $likes = new Likes();
        $likes->setArticle($article);
        $likes->setUser($user);

        $manager->persist($likes);

        $manager->flush();

        return $this->json([
            'code'=> 200,
            'messages'=> "Nouvel enregistrement ",
            'likes' => $repo->count(["article"=> $article])
        ],200);
    }
}
