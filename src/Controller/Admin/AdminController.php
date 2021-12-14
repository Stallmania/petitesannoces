<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 * @package App\Controller\Admin
 */

class AdminController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }
    /**
     * @Route("/categories/ajout", name="categories_ajout")
     */
    public function ajoutCategorie(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categories;
        $form = $this->createForm(CategoriesType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('admin_home');
        }

        return $this->render('admin/categories/ajout.html.twig', [
            'form'=> $form->createView()
        ]);
    }
}
