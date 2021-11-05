<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartItemController extends AbstractController
{
    /**
     * @Route("/cartItem/add", name="app_cart_item_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $cartItemForm = $request->request->all()['cart_item_form'];
        $product = $entityManager->getRepository('App:Product')->find($cartItemForm['product']);

        if (!$product) {
            $this->addFlash('error', 'The product does not exist!');

            return $this->redirectToRoute('app_product_view', ['id' => $cartItemForm['product']]);
        }

        // verifier si l'utilisateur a deja une carte 
        if (!$user->getCart()) {
            $cart = new Cart();
            $cart->setUser($user);
            $entityManager->persist($cart);
            $entityManager->flush();
        } else {
            // recuperer la carte de l'utilisateur 
            $cart = $user->getCart();
        }

        // verifier si le produit existe deja dans la carte 
        if ($cart->findItemByProduct($product)) {
            $this->addFlash('error', 'Vous avez dejà commandé ce produit!');

            return $this->redirectToRoute('app_product_view', ['id' => $product->getId()]);
        }

        $cartItem = new CartItem();
        $cartItem->setProduct($product);
        $cartItem->setPrice($product->getMaxPrice());
        $cart->addCartItem($cartItem);

        $entityManager->persist($cart);
        $entityManager->flush();
        $this->addFlash('success', 'Votre produit a été ajouté avec succès');

        return $this->redirectToRoute('app_product_view', ['id' => $product->getId()]);
    }
}
