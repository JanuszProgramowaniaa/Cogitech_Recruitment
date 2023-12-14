<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\IsGranted;

class PostsController extends AbstractController
{
    
    #[Route('/posts', name: 'app_posts')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
         // Przykładowa struktura postów
         $posts = [
            [
                'title' => 'Pierwszy post',
                'content' => 'Treść pierwszego posta...',
                'author' => 'Autor 1',
                'createdAt' => new \DateTime(),
            ],
            [
                'title' => 'Drugi post',
                'content' => 'Treść drugiego posta...',
                'author' => 'Autor 2',
                'createdAt' => new \DateTime(),
            ],
            // Dodaj więcej postów według potrzeb
        ];

        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
