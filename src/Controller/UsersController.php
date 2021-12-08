<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Annonces;
use App\Form\AnnonceType;
use App\Form\EditProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index(): Response
    {
        return $this->render('users/index.html.twig');
    }
    /**
     * @Route("/users/annonces/ajout", name="users_annonces_ajout")
     */
    public function ajoutAnnonces(Request $request, EntityManagerInterface $em): Response
    {
        $annonce = new Annonces;
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $annonce->setUsers($this->getUser());
            $annonce->setActive(false);

            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('users/annonces/ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/users/profilmodif", name="users_profil_modif")
     */
    public function editProfil(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfilType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('message', 'Votre profile est mis à jour');
            return $this->redirectToRoute('users');
        }

        return $this->render('users/editProfil.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/users/passmodif", name="users_passe_modif")
     */
    public function editPass(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $manager)
    {
        if ($request->isMethod('POST')) {
            $user = $this->getUser();
            if ($request->request->get('passe') == $request->request->get('passe2'))
                {
                //dd($user->getPassword());
                $encodedPassword = $userPasswordHasher->hashPassword($user,$request->request->get('passe'));

                $user->setPassword($encodedPassword);

                //$manager->persist($encodedPassword);
                $manager->flush();

                $this->addFlash('message', 'Mot de passe mis a jour avec succsé');
                return $this->render('users/index.html.twig');
                }
            else{
                $this->addFlash('error', 'Les deux mots des passe ne sont pas identique !');
            }
        }
        return $this->render('users/editPasse.html.twig');
    }
}
