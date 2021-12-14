<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/categories", name="admin_categories_")
 * @package App\Controller\Admin
 */

class CategoriesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CategoriesRepository $CatsRepo): Response
    {
        return $this->render('admin/categories/index.html.twig',[
            'controller_name'=>'Gerer les catégories',
            'categories'=>$CatsRepo->findAll()
        ]);
    }
    /**
     * @Route("/ajout", name="ajout")
     */
    public function ajoutCategorie(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categories;
        $form = $this->createForm(CategoriesType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('admin_categories_home');
        }

        return $this->render('admin/categories/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifierCategorie(Request $request, EntityManagerInterface $em, Categories $categorie): Response
    {
        $form = $this->createForm(CategoriesType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('admin_categories_home');
        }

        return $this->render('admin/categories/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
