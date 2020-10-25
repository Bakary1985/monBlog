<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Doctrine\Common\Cache\RedisCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    /**
     * @Route("/panier", name="panier_index")
     */
    public function panier(SessionInterface $session, ArticleRepository $repository){

        $panier = $session->get("panier",[]);

        $panierWithData = [];

        foreach ($panier as $id => $quantity){
            $panierWithData[]= [
                "product"=> $repository->find($id),
                "quantity"=> $quantity
            ];
        }

        $total = 0;
        foreach ($panierWithData as $item){
            $totalItem= $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        return $this->render('card/index.html.twig', [
            "items" => $panierWithData,
            "total" => $total,
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="card_add")
     */
    public function index($id, SessionInterface $session)
    {
        $panier = $session->get("panier",[]);

        if (!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        $session->set("panier",$panier);
        return $this->redirectToRoute("panier_index");
    }

    /**
     * @Route("/panier/remove/{id}", name="card_remove")
     */
    public function remove($id, SessionInterface $session){
        $panier = $session->get("panier", []);

        if (!empty($panier[$id]) && $panier[$id] !== 1){
            $panier[$id]--;
        }elseif ($panier[$id] === 1){
            unset($panier[$id]);
        }

        $session->set("panier", $panier);

        return $this->redirectToRoute("panier_index");
    }
}
