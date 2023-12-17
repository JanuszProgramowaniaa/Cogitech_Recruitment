<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\IsGranted;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class PostsController extends AbstractController
{

    /**
     * Wyświetla listę postów na określonej stronie.
     *
     * @param PostRepository $postRepository Repozytorium postów.
     * @param int $page Numer strony.
     * @param int $elementPerPage Ilość elementów na stronie.
     *
     * @return Response Odpowiedź HTTP zawierająca listę postów.
     */
    #[Route('/posts/{page}', name: 'app_posts')]
    #[IsGranted('ROLE_USER')]
    public function index(PostRepository $postRepository, int $page = 1, int $elementPerPage = 5): Response
    {
        
        $posts = $postRepository->findPosts($page*$elementPerPage -$elementPerPage , $elementPerPage);

        /* Zabezpieczenie przed wbiciem na numer strony dla której nie ma wynikow */
        if($page > intval(ceil(count($posts) / $elementPerPage)) && $page != 1  ){
            return $this->redirectToRoute('app_posts', ['page' => 1] );
        }
       
        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
            'totalElements' => count($posts),
            'elementPerPage' => $elementPerPage,
            'page' => $page
        ]);
    }


    /**
     * Usuwa post o określonym ID.
     *
     * @param EntityManagerInterface $entityManager Manager encji.
     * @param PostRepository $postRepository Repozytorium postów.
     * @param Request $request Obiekt Request.
     * @param int $id ID posta do usunięcia.
     * @param int $page Numer strony, do której ma wrócić po usunięciu posta.
     *
     * @return Response Odpowiedź HTTP po usunięciu posta.
     */
    #[Route('/post/delete/{id}/{page}', name: 'app_post_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(EntityManagerInterface $entityManager, Request $request, PostRepository $postRepository, int $id, int $page): Response
    {
        $post = $postRepository->findOneBy(["id" => $id]);
    
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $entityManager->remove($post);
        $entityManager->flush();

        
        // Przekierowanie zależne od strony, z której przyszło żądanie
        $referer = $request->headers->get('referer');
        if ($referer !== null) {
            return $this->redirect($referer);
        } else {
            return $this->redirectToRoute('app_posts', ['page' => $page]);
        }
    }
}
