<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\IsGranted;
use App\Repository\PostRepository;

class PostsController extends AbstractController
{
    
    #[Route('/posts', name: 'app_posts')]
    #[IsGranted('ROLE_USER')]
    public function index(PostRepository $postRepository): Response
    {
        
        $posts = $postRepository->findAll();
          
        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
