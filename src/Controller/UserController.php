<?php

namespace App\Controller;

use App\Entity\UserLinks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\User;
use Symfony\Component\Validator\Constraints\Json;

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
        $links = [];
        foreach ($user->getUserLinks() as $link) {
            $links[] = [
                'text'=>$link->getText(),
                'icon'=>$link->getIcon(),
                'link'=>$link->getLink(),
                'id'=>$link->getId()
            ];
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
            'phone' =>$user->getPhone(),
            'links' => $links
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

    #[Route('/api/users/edit', name: 'apiUpdateUser', methods: ['POST'])]
    public function apiUpdateUser(Request $request, EntityManagerInterface $entityManager): JsonResponse{
        $data = json_decode($request->getContent(), true);
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if(isset($data['email'])){
            $user->setEmail($data['email']);
        }
        if(isset($data['name'])){
            $user->setName($data['name']);
        }
        if(isset($data['price'])){
            $user->setPrice($data['price']);
        }
        if(isset($data['address'])){
            $user->setAddress($data['address']);
        }
        if(isset($data['job'])){
            $user->setJob($data['job']);
        }
        if(isset($data['description'])){
            $user->setDescription($data['description']);
        }
        if(isset($data['phone'])){
            $user->setPhone($data['phone']);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['succes' => "usuario actualizado"]);
    }

    #[Route('/api/users/editpass', name: 'apiUpdatePassUser', methods: ['POST'])]
    public function apiUpdatePassUser(Request $request,UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $entityManager
    ): JsonResponse{
        $data = json_decode($request->getContent(),true);
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if (!isset($data['currentPassword']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Current password and new password are required'], Response::HTTP_BAD_REQUEST);
        }

        if (!$passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
            return new JsonResponse(
                ['error' => 'The current password is invalid'], 
                Response::HTTP_UNAUTHORIZED
            );
        }

        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(
            ['success' => 'Password change'], 
            Response::HTTP_OK
        );
    }

    #[Route('/api/users/links', name: 'apiUserLinks', methods: ['GET'])]
    public function getUserLinks(): JsonResponse{
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $links = [];
        foreach ($user->getUserLinks() as $link) {
            $links[] = [
                'text'=>$link->getText(),
                'icon'=>$link->getIcon(),
                'link'=>$link->getLink(),
                'id'=>$link->getId()
            ];
        }
       
        return new JsonResponse($links, Response::HTTP_OK);
    }

    #[Route('/api/users/skills', name: 'apiUserSkilss', methods: ['GET'])]
    public function getUserSkills(): JsonResponse{
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $skills = [];
        foreach ($user->getUserAbilities() as $skill) {
            $skills[] = [
                'text'=>$skill->getText(),
                'id'=>$skill->getId()
            ];
        }
       
        return new JsonResponse($skills, Response::HTTP_OK);
    }

    #[Route('/api/users/links/new', name: 'apiAddUserLinks', methods: ['POST'])]
    public function addUserLinks(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse{
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if($data['text'] == null || $data['icon'] == null || $data['link'] == null){
            return new JsonResponse(['error' => 'All fields are required'], Response::HTTP_BAD_REQUEST);
        }

        $link = new UserLinks();

        $link->setUser($user);
        $link->setText($data['text']);
        $link->setIcon($data['icon']);
        $link->setLink($data['link']);
        $user->addUserLink($link);
        $entityManagerInterface->persist($link);
        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush();
        return new JsonResponse(
            ['success' => 'Link added'], 
            Response::HTTP_OK
        );   
    }

    #[Route('/api/users/projects', name: 'apiGetUserProjects', methods: ['GET'])]
    public function getUserProjects(): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $projects = $user->getProjects();
        $data = [];
        foreach ($projects as $project) {
            $data[] = [
                'id' => $project->getId(),
                'title' => $project->getTitle(), 
                'description' => $project->getDescription(), 
                'budget' => $project->getBudget(), 
                'projectdate' => $project->getProjectdate(), 
                'client' => $project->getClient()->getName(),
                'state' =>$project->isState(),
                'category' =>$project->getCategory(),
                'duration' => $project->getDuration()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }







}
