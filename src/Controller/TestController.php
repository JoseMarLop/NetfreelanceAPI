<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/api/test', name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return new JsonResponse(['message' => 'API is working!']);
    }
} 