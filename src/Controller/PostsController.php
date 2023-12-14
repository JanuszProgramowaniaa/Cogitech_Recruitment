<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\IsGranted;
use App\Repository\PostRepository;

class PostsController extends AbstractController
{
    
    #[Route('/posts/{page}', name: 'app_posts')]
    #[IsGranted('ROLE_USER')]
    public function index(PostRepository $postRepository, int $page = 1, int $itemPerPage = 5): Response
    {
        
        $posts = $postRepository->findPosts($page*$itemPerPage-$itemPerPage, $itemPerPage);
          
       
        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
