<?php

namespace App\Controller;

use App\Entity\UserAbilities;
use App\Repository\UserAbilitiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class SkillController extends AbstractController
{
    public function __construct(private UserAbilitiesRepository $skillsRepository){

    }
    #[Route('/api/users/skills/edit', name: 'apiEditSkills', methods: ['POST'])]
    public function addSkill(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
         /** @var User $user */
         $user = $this->getUser();
         if (!$user) {
             return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
         }

         $data = json_decode($request->getContent(),true);

         $skillsData = $data;

         $existingSkills = $user->getUserAbilities()->toArray();

         foreach($existingSkills as $skill){
            $user->removeUserAbility($skill);
            $entityManager->remove($skill);
         }

         foreach ($skillsData as $skillData) {
            $skill = new UserAbilities();
            $skill->setText($skillData['text']);
            $skill->setUser($user);
            $user->addUserAbility($skill);
            $entityManager->persist($skill);
        }
         $entityManager->persist($user);
         $entityManager->flush();

        
        return new JsonResponse (["success"=>"Skills updated"], Response::HTTP_OK);
    }
}
