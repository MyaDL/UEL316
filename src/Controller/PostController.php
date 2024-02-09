<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/post')]
class PostController extends AbstractController
{
    private $entityManager;
    private $paginator;

    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
    }

    private function generateSlug(string $title, SluggerInterface $slugger): string
    {
        return $slugger->slug($title)->lower();
    }

    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, Request $request): Response
    {
        $donnees = $postRepository->findAll();

        $posts = $this->paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Générer le slug à partir du titre
            $post->setSlug($this->generateSlug($post->getTitle(), $slugger));
    
            $entityManager->persist($post);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/{slug}', name: 'app_post_show', methods: ['GET'])]
    public function show(PostRepository $postRepository, string $slug): Response
    {
        $post = $postRepository->findOneBy(['title' => $slug]);
    
        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }
    
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PostRepository $postRepository, string $slug, EntityManagerInterface $entityManager): Response
    {
        $post = $postRepository->findOneBy(['title' => $slug]);
    
        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }
    
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(), // Attention, créer la vue du formulaire
        ]);
    }

    #[Route('/{slug}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, PostRepository $postRepository, string $slug, EntityManagerInterface $entityManager): Response
    {
        $post = $postRepository->findOneBy(['title' => $slug]);

        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }

    
    
}
