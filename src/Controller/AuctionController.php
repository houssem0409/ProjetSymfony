<?php

namespace App\Controller;

use App\Entity\Auction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuctionController extends AbstractController
{
    /**
     * @Route("/auction/new", name="app_auction")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $auctionForm = $request->request->all()['auction_form'];
        $product = $entityManager->getRepository('App:Product')->find($auctionForm['product']);

        if (!$product) {
            $this->addFlash('error', 'The product does not exist!');

            return $this->redirectToRoute('app_product_view', ['id' => $auctionForm['product']]);
        }

        $auction = new Auction();
        $auction->setProduct($product);
        $auction->setUser($this->getUser());
        $auction->setPrice((float)$auctionForm['price']);

        if ($auction->getPrice() <= $product->getMaxPrice()) {
            $this->addFlash('error', 'Enchérissez plus de ' . $product->getPrice() . ' DT');

            return $this->redirectToRoute('app_product_view', ['id' => $product->getId()]);
        }

        $timeLimit = $product->getTimeLimit() ?? 0;
        $expirationDate = $product->getCreatedAt()->modify('+ ' . $timeLimit . ' hour');
        $now = new \DateTime("now");

        if ($expirationDate <= $now) {
            $this->addFlash('error', 'La vente est terminée');

            return $this->redirectToRoute('app_product_view', ['id' => $product->getId()]);
        }

        $entityManager->persist($auction);
        $entityManager->flush();
        $this->addFlash('success', 'Votre enchère a été enregistré');

        return $this->redirectToRoute('app_product_view', ['id' => $product->getId()]);
    }
}
