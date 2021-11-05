<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgetPasswordFormType;
use App\Form\ResetPasswordFormType;
use App\Manager\UserManager;
use App\Services\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CategoryController extends AbstractController
{
    public function menu(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categories = $entityManager->getRepository('App:Category')->findBy(['parent' => null]);

        return $this->render('category/menu.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/{id}", name="app_category_view")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function view(Request $request, int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository('App:Category')->find($id);

        if (!$category) {
            throw $this->createNotFoundException('The category does not exist');
        }

        $tableau = [1,2,3];
        $products = $category->getProducts();

        foreach ($category->getChildren() as $child){
            foreach ($child->getProducts() as $product){
                $products->add($product);
            }
        }

        return $this->render('category/view.html.twig', [
            'category' => $category,
            'products' => $products,
            'tableau' => $tableau
        ]);
    }
}
