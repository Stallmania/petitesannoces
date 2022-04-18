<?php

namespace App\Controller;

use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/annonces", name="annonces_")
 * @package App\Controller
 */
class AnnoncesController extends AbstractController
{
    /**
     * @Route("/details/{slug}", name="details")
     */
    public function details($slug, AnnoncesRepository $annoncesRepo): Response
    {
        $annonce = $annoncesRepo->findOneBy(['slug'=>$slug]);

        if (!$annonce) {
            throw new NotFoundHttpException('Annonce introuvable');
        }

        return $this->render('annonces/details.html.twig', compact('annonce'));
    }
}
