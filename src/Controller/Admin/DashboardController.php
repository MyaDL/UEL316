<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    private $commentRepository;
    private $userRepository;
    private $postRepository;
    public function __construct(CommentRepository $commentRepository,PostRepository $postRepository,UserRepository $userRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
    }
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        $users = $this->userRepository->findBy([], ['id' => 'DESC'], 3);
        $comments = $this->commentRepository->findBy([], ['publishedDate' => 'DESC'], 3);
        $posts = $this->postRepository->findBy([], ['publishedDate' => 'DESC'], 3);

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'comments' => $comments,
            'posts' => $posts,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('UEL316');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-window-maximize');
        yield MenuItem::linkToRoute('Site', 'fa fa-home', 'app_home');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Blog');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Post', 'fas fa-message', Post::class);
        yield MenuItem::linkToCrud('Comment', 'fas fa-list', Comment::class);
        yield MenuItem::section('');
        yield MenuItem::linkToLogout('Logout', 'fas fa-sign-out');
    }
}
