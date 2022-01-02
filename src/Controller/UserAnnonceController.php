<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Images;
use App\Form\AnnonceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAnnonceController extends AbstractController
{
    /**
     * @Route("/user/annonces", name="user_annonce")
     */
    public function index(): Response
    {
        return $this->render('user_annonce/index.html.twig', [
            'controller_name' => 'UserAnnonceController',
        ]);
    }
    
    /**
     * @Route("/user/annonces/{id}", name="annonces_show", methods={"GET"})
     */
    public function show(Annonces $annonce): Response
    {

        return $this->render('user_annonce/annonces/show.html.twig', [
            'annonce' => $annonce
        ]);
    }

    
    /**
     * @Route("/user/annonces/edition/{id}/modification", name="annonces_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Annonces $annonce, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach($images as $image){
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                
                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                
                // On crée l'image dans la base de données
                $img = new Images();
                $img->setName($fichier);
                $annonce->addImage($img);
            }

            $entityManager->flush();

            return $this->redirectToRoute('user_annonce', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_annonce/annonces/modification.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/user/annonces/edition/ajout", name="users_annonces_ajout")
     */
    public function ajoutAnnonces(Request $request, EntityManagerInterface $em): Response
    {
        $annonce = new Annonces();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $images = $form->get('images')->getData();
            foreach($images as $image){
                $fichier = md5(uniqid()).'.'.$image->guessExtension();

                $image->move($this->getParameter('images_directory'),$fichier);

                $img = new Images();
                $img->setName($fichier);
                $annonce->addImage($img);
            }

            $annonce->setUsers($this->getUser());
            $annonce->setActive(false);

            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('user_annonce/annonces/ajout.html.twig', [
            'formulaire' => $form->createView(),
            'annonce' => $annonce
        ]);
    }

    /**
     * @Route("/user/annonces/edition/supprime/image/{id}", name="annonces_delete_image", methods={"DELETE"})
     */
    public function deleteImage(Images $image, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            // On récupère le nom de l'image
            $nom = $image->getName();
            // On supprime le fichier
            unlink($this->getParameter('images_directory') . '/' . $nom);

            // On supprime l'entrée de la base
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

    /**
     * @Route("/user/annonces/edition/delete/{id}", name="annonces_delete", methods={"POST"})
     */
    public function delete(Request $request, Annonces $annonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('annonces_index', [], Response::HTTP_SEE_OTHER);
    }
}
