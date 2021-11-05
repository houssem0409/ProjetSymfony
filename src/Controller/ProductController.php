<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Image;
use App\Entity\Product;
use App\Form\AuctionFormType;
use App\Form\CartItemFormType;
use App\Form\ProductFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/sell/product", name="app_product_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function sell(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $image = new Image();
                $image->setName($fichier);
                $product->addImage($image);
            }

            $product->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_account');
        }

        return $this->render('product/sell.html.twig', [
            'productForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/{id}", name="app_product_view")
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws \Exception
     */
    public function view(Request $request, int $id)
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository('App:Product')->find($id);

        if (!$product) {
            throw $this->createNotFoundException('The product does not exist');
        }

        $timeLimit = $product->getTimeLimit() ?? 0;
        $expirationDate = $product->getCreatedAt()->modify('+ ' . $timeLimit . ' hour');
        $now = new \DateTime("now");

        if ($expirationDate < $now && $product->getTransactionType() == 'E' && $auction = $product->getLastAuction()) {
            if (!$auction->getUser()->getCart()) {
                $cart = new Cart();
                $cart->setUser($auction->getUser());
                $entityManager->persist($cart);
                $entityManager->flush();
            } else {
                $cart = $auction->getUser()->getCart();
            }

            if (!$cart->findItemByProduct($product)) {
                $cartItem = new CartItem();
                $cartItem->setProduct($product);
                $cartItem->setPrice($product->getMaxPrice());
                $cart->addCartItem($cartItem);
                $entityManager->persist($cart);
                $entityManager->flush();
            }
        }

        $auctionForm = $this->createForm(AuctionFormType::class, new Auction(), [
            'action' => $this->generateUrl('app_auction'),
            'method' => 'POST',
        ]);

        $cartItemForm = $this->createForm(CartItemFormType::class, new CartItem(), [
            'action' => $this->generateUrl('app_cart_item_add'),
            'method' => 'POST',
        ]);

        return $this->render('product/view.html.twig', [
            'product' => $product,
            'expirationDate' => $expirationDate > $now ? $expirationDate : null,
            'auctionForm' => $auctionForm->createView(),
            'maxPrice' => $product->getMaxPrice(),
            'cartItemForm' => $cartItemForm->createView(),
        ]);
    }
}
