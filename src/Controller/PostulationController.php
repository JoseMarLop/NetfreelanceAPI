<?php

namespace App\Controller;

use App\Entity\Postulation;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostulationController extends AbstractController
{
    #[Route('/api/project/postulations/{id}', name: 'apiGetProjectPostulation', methods: ['GET'])]
    public function getProjectPostulations(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $project = $entityManager->getRepository(Project::class)->find($data['projectid']);
        if (!$project) {
            return new JsonResponse(['error' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }
        $postulations = $entityManager->getRepository(Postulation::class)->findBy(['project' => $project]);
        return new JsonResponse($postulations, Response::HTTP_OK);
    }

    #[Route('/api/user/postulations', name: 'apiGetUserPostulations', methods: ['GET'])]
    public function getUserPostulations(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $postulations = $entityManager->getRepository(Postulation::class)->findBy(['freelancer' => $user]);
        return new JsonResponse($postulations, Response::HTTP_OK);
    }



    #[Route('/api/postulation/new', name: 'apiAddPostulation', methods: ['POST'])]
    public function addPostulation(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        $project = $entityManager->getRepository(Project::class)->find($data['projectid']);
        if (!$project) {
            return new JsonResponse(['error' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        $postulation = new Postulation();
        $postulation->setFreelancer($user);
        $postulation->setProject($project);
        $postulation->setMessage($data['message']);

        $date = new \DateTime();
        $postulation->setDate($date->format('d-m-Y'));

        $entityManager->persist($postulation);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Postulation created successfully'], Response::HTTP_CREATED);
    }
}
