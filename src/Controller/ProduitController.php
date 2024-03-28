<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Entity\ContenuPanier;
use App\Form\ContenuPanierType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Panier;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
              /** @var UploadedFile $photoFile */
              $photoFile = $form->get('photo')->getData();

              // this condition is needed because the 'brochure' field is not required
              // so the PDF file must be processed only when a file is uploaded
              if ($photoFile) {
                  $newFilename = uniqid() . '.' . $photoFile->guessExtension();
  
                  // Move the file to the directory where brochures are stored
                  try {
                      $photoFile->move(
                          $this->getParameter('upload_directory'),
                          $newFilename
                      );
                  } catch (FileException $e) {
                      // ... handle exception if something happens during file upload
                      $this->addFlash('danger', "Impossible d'uploader le fichier");
                      return $this->redirectToRoute('app_produit');
                  }
  
                  // updates the 'photoFilename' property to store the PDF file name
                  // instead of its contents
                  $produit->setPhoto($newFilename);
              }

            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_produit_show', methods: ['GET', 'POST'])]
    public function show(Produit $produit, EntityManagerInterface $em, Request $request): Response
    {   
         // Création d'un objet vide pour le formulaire
         $addToCart = new ContenuPanier();
         // associer l'id du produit dans le panier

            $produit = $em->getRepository(Produit::class)->find($produit->getId());

         // Création du formulaire en utilisant l'objet vide
         $form = $this->createForm(ContenuPanierType::class, $addToCart);
         // Analyse la requête HTTP
         $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
 
             // Si le formulaire a été soumis et qu'il a passé les vérifications, on le sauverage en base de données
             $em->persist($addToCart); // Prepare en PDO
             $em->flush(); // Execute la requête
             
             $this->addFlash('success', 'Produit ajoutée au panier');
             // Redirection vers la list des catégories pour qu'il recharge la liste des catégories
             return $this->redirectToRoute('app_produit');
         }
 
         // Récupération de la table categorie
         $addToCart = $em->getRepository(ContenuPanier::class)->findAll();

         
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'form_addToCart' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/add/{id}', name: 'app_produit_add', methods: ['GET'])]
    public function add(Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $contenuPanier = new ContenuPanier();

        $contenuPanier->addProduit($produit);
        $contenuPanier->setQuantiter(1);
        $contenuPanier->setDateAjout(new \DateTime());
        //$contenuPanier->setPanier($this->getUser()->getUserPanier());

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $panierValide = $entityManager->getRepository(Panier::class)->findPanierActif($user);

        if($panierValide == null){
            $panierValide = new Panier();
            $panierValide->setUtilisateur($user);
            $panierValide->setEtat(false);
            $entityManager->persist($panierValide);
        }

        $panierValide->addContenuPanier($contenuPanier);
        $entityManager->persist($contenuPanier);
        $entityManager->flush();

        return $this->redirectToRoute('app_contenu_panier_index');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit', [], Response::HTTP_SEE_OTHER);
    }
}
