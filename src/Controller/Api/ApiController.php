<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class ApiController extends AbstractController
{
    #[Route('/api/articles/{id}', methods: ['GET'])]
    public function getArticle(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $article = $entityManager->getRepository(Article::class)->find($id);

            if (!$article) {
                return $this->json([
                    'error' => 'Article not found'
                ], 404);
            }

            return $this->json([
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'text' => $article->getText(),
                'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $article->getUpdatedAt() ? $article->getUpdatedAt()->format('Y-m-d H:i:s') : null,
                'users' => $article->getUsers()
            ], 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Failed to fetch article',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/api/users/{userId}/articles', methods: ['GET'])]
    public function getUserArticles(int $userId, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $user = $entityManager->getRepository(User::class)->find($userId);

            if (!$user) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            $articles = $user->getArticles();

            $response = [];
            foreach ($articles as $article) {
                $response[] = [
                    'id' => $article->getId(),
                    'title' => $article->getTitle(),
                    'text' => $article->getText(),
                    'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updated_at' => $article->getUpdatedAt() ? $article->getUpdatedAt()->format('Y-m-d H:i:s') : null
                ];
            }

            return $this->json($response, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Failed to fetch user articles',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/api/top-authors', methods: ['GET'])]
    public function getTopAuthors(EntityManagerInterface $entityManager): JsonResponse
    {
        try {

            $result = $entityManager->getRepository(User::class)->topAuthors() ?? [];

            $response = [];
            foreach ($result as $row) {
                $response[] = [
                    'name' => $row['name'],
                    'articles_count' => $row['articles_count']
                ];
            }

            return $this->json($response, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Failed to fetch top authors',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
