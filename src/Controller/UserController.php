<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\User;

class UserController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function handleProfilePictureUpload(?UploadedFile $file, User $user): ?string
    {
        if (!$file) {
            return null;
        }

        $newFilename = uniqid() . '.' . $file->guessExtension();

        try {
            $file->move(
                $this->getParameter('profile_pictures_directory'),
                $newFilename
            );

            $user->setProfilepcic($newFilename);
            $this->entityManager->flush();

            return $newFilename;
        } catch (FileException $e) {
            return null;
        }
    }

    #[Route('/api/users/profile', name: 'apiGetUser', methods: ['GET'])]
    public function apiGetUser(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $responseData = [
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
            'price' => $user->getPrice(),
            'company' => $user->isCompany(),
            'companyName' => $user->getCompanyName(),
            'description' => $user->getDescription(),
            'address' => $user->getAddress(),
            'job' => $user->getJob(),
            // Add any other fields you want to return
        ];
        return new JsonResponse($responseData, Response::HTTP_OK);
    }
    public function uploadProfilePicture(Request $request, User $user): Response
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('profile_picture');
        
        $filename = $this->handleProfilePictureUpload($uploadedFile, $user);
        
        if (!$filename) {
            return new Response('Error uploading file', Response::HTTP_BAD_REQUEST);
        }

        return new Response('File uploaded successfully', Response::HTTP_OK);
    }
}
