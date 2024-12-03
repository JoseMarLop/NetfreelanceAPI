<?php

namespace App\Controller;

use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends AbstractController
{
    public function __construct(private ProjectRepository $projectsRepository) {}

    #[Route('/api/projects', name: 'apiGetProjects', methods: ['GET'])]
    public function getAllProjects(): JsonResponse
    {

        $projects = $this->projectsRepository->findBy(['state' => true]);

        if (empty($projects)) {
            return new JsonResponse(['message' => 'No se encontraron proyectos.'], Response::HTTP_NOT_FOUND);
        }

        $data = [];
        foreach ($projects as $project) {
            $data[] = [
                'id' => $project->getId(),
                'title' => $project->getTitle(),
                'description' => $project->getDescription(),
                'projectdate' => $project->getProjectdate(),
                'budget' => $project->getBudget(),
                'client' => $project->getClient()->getName(),
                'category' => $project->getCategory(),
                'duration' => $project->getDuration(),
                'state' => $project->isState()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/projects/{id}', name: 'apiGetProject', methods: ['GET'])]
    public function getProject(int $id): JsonResponse
    {
        $project = $this->projectsRepository->find($id);

        if (!$project) {
            return new JsonResponse(['error' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $project->getId(),
            'title' => $project->getTitle(),
            'description' => $project->getDescription(),
            'projectdate' => $project->getProjectdate(),
            'budget' => $project->getBudget(),
            'category' => $project->getCategory(),
            'duration' => $project->getDuration(),
            'state' => $project->isState(),
            'client' => [
                'id' => $project->getClient()->getId(),
                'name' => $project->getClient()->getName(),
                'email' => $project->getClient()->getEmail(),
                'phone' => $project->getClient()->getPhone(),
                'address' => $project->getClient()->getAddress()
            ]
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }



    #[Route('/api/projects/new', name: 'apiAddProject', methods: ['POST'])]
    public function addProject(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);

        if ($data['title'] == null || $data['description'] == null || $data['budget'] == null || $data['category'] == null) {
            return new JsonResponse(['error' => 'All fields are required'], Response::HTTP_BAD_REQUEST);
        }

        $project = new Project();

        $project->setClient($user);
        $project->setTitle($data['title']);
        $project->setDescription($data['description']);
        $project->setBudget($data['budget']);
        $project->setCategory($data['category']);
        $project->setDuration($data['duration']);

        $date = new \DateTime();
        $project->setProjectdate($date->format('d-m-Y'));

        $project->setState(true);
        $entityManager->persist($project);
        $entityManager->flush();

        return new JsonResponse(['message' => "Project Created"]);
    }

    #[Route('/api/projects/{id}', name: 'apiDeleteProject', methods: ['DELETE'])]
    public function deleteProject(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $project = $this->projectsRepository->find($id);
        if (!$project) {
            return new JsonResponse(['error' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        // Remove project from user
        $user->removeProject($project);

        // Remove project from database
        $entityManager->remove($project);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Project deleted successfully'], Response::HTTP_OK);
    }

    #[Route('/api/projects/{id}', name: 'apiEditProject', methods: ['PUT'])]
    public function editProject(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $project = $this->projectsRepository->find($id);

        if (!$project) {
            return new JsonResponse(['error' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        if (isset($data['title'])) {
            $project->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $project->setDescription($data['description']);
        }
        if (isset($data['budget'])) {
            $project->setBudget($data['budget']);
        }
        if (isset($data['category'])) {
            $project->setCategory($data['category']);
        }
        if (isset($data['title'])) {
            $project->setState($data['state']);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Project updated successfully'], Response::HTTP_OK);
    }
}
