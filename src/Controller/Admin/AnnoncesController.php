<?php

namespace App\Controller\Admin;

use App\Repository\AnnoncesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Annonces;

/**
 * @Route("/admin/annonces", name="admin_annonces_")
 * @package App\Controller\Admin
 */

class AnnoncesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(AnnoncesRepository $annoncesRepo): Response
    {
        return $this->render('admin/annonces/index.html.twig', [
            'controller_name' => 'Gerer les annonces',
            'annonces' => $annoncesRepo->findAll()
        ]);
    }

    /**
     * @Route("/activer/{id}", name="activer")
     */
    public function activer(Annonces $annonce,EntityManagerInterface $em ):Response
    {
        $annonce->setActive(($annonce->getActive())?false:true);
        $em->persist($annonce);
        $em->flush();

        return new Response('OK'); 
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimer(Annonces $annonce,EntityManagerInterface $em ):Response
    {
        $em->remove($annonce);
        $em->flush();

        //$this->addFlash("message","annonce supprimé");

        return $this->redirectToRoute("admin_annonces_home");
    }
}
