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

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $newProducts = $entityManager->getRepository('App:Product')->findBy([], ['id' => 'DESC'], 8, null);

        return $this->render('home/index.html.twig',[
            'products' => $newProducts
        ]);
    }

    /**
     * @Route("/about", name="app_about")
     */
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    /**
     * @Route("/contact", name="app_contact")
     */
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }
}
