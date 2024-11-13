<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TestController extends AbstractController
{
    #[Route('/api/test', name: 'test', methods: ['GET'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function test(): JsonResponse
    {
        return new JsonResponse(['message' => 'API is working!']);
    }
} 