<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserLinksRepository;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class LinkController extends AbstractController
{
    public function __construct(private UserLinksRepository $linksRepository){

    }

    #[Route('/api/users/links/remove/{id}', name: 'apiDeleteLink', methods: ['DELETE'])]
    public function removeLink(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $link = $this->linksRepository->findOneBy(['id' => $id]);

        if (!$link || $link->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Link not found or does not belong to the user'], Response::HTTP_NOT_FOUND);
        }else{
            $user->removeUserLink($link);
            $entityManager->remove($link);
            $entityManager->flush();
            return new JsonResponse(['success' => 'Link deleted'], Response::HTTP_OK);
        }

    }

    #[Route('/api/users/links/edit/{id}', name: 'apiEditLink', methods: ['PUT'])]
    public function editLink (int $id, EntityManagerInterface $entityManager,Request $request): JsonResponse{
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if($data['text'] == null || $data['icon'] == null || $data['link'] == null){
            return new JsonResponse(['error' => 'All fields are required'], Response::HTTP_BAD_REQUEST);
        }
        $link = $this->linksRepository->findOneBy(['id' => $id]);
        $link->setText($data['text']);
        $link->setIcon($data['icon']);
        $link->setLink($data['link']);

        $entityManager->persist($link);
        $entityManager->flush();

        return new JsonResponse (['success' => 'Link edited'], Response:: HTTP_OK);
    }
}
