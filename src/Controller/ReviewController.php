<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ReviewRepository;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;

class ReviewController extends AbstractController
{

    #[Route('/api/users/reviews', name: 'apiGetReviews', methods: ['GET'])]
    public function apiGetReviews(ReviewRepository $reviewRepository): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $reviews = $reviewRepository->findBy(['user' => $user]);

        return new JsonResponse(['reviews' => $reviews], Response::HTTP_OK);
    }
}
