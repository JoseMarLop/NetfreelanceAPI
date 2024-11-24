<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class AuthController extends AbstractController
{
    public function __construct(
        private UserController $userController
    ) {
    }

    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validate required fields
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
            return new JsonResponse(['message' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        // Check if user already exists
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'User already exists'], Response::HTTP_CONFLICT);
        }

        // Create new user
        $user = new User();
        $user->setEmail($data['email']);
        $user->setRoles($data['roles']);
        $user->setName($data['name']);
        $user->setPrice($data['price'] ?? null);
        $user->setCompany($data['company'] ?? null);
        $user->setCompanyName($data['companyName'] ?? null);
        $user->setDescription($data['description'] ?? null);
        $user->setAddress($data['address'] ?? null);
        $user->setJob($data['job'] ?? null);
        $user->setPhone($data['phone'] ?? null);

        // Hash the password
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Save user to database
        $entityManager->persist($user);
        $entityManager->flush();

        // Handle profile picture if present
        /** @var UploadedFile $profilePicture */
        $profilePicture = $request->files->get('profile_picture');
        if ($profilePicture) {
            $this->userController->handleProfilePictureUpload($profilePicture, $user);
        }

        return new JsonResponse(['message' => 'User registered successfully'], Response::HTTP_CREATED);
    }

    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(
        Request $request, 
        JWTTokenManagerInterface $JWTManager,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Validate that email and password are provided
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(
                ['error' => 'Email and password are required'], 
                Response::HTTP_BAD_REQUEST
            );
        }

        // Check if user exists
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if (!$user) {
            return new JsonResponse(
                ['error' => 'Email not found'], 
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Verify password
        if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(
                ['error' => 'Invalid password'], 
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Get user data and create token
        $token = $JWTManager->create($user);
        
        // Create response data
        $responseData = [
            'token' => $token,
            'roles' => $user->getRoles(),
            'email' => $user->getEmail()   
        ];

        return new JsonResponse($responseData, Response::HTTP_OK);
    }

    #[Route('/api/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        // This route will be handled by the security system
        throw new \Exception('This should never be reached!');
    }
}
