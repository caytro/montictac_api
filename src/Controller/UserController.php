<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    

    #[Route('/api/user', name: 'createAccount', methods: ['POST'])]
    public function createAccount(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        SerializerInterface $serializer
    ): JsonResponse {

        $userMail = $request->toArray()['userMail'] ?? null;
        $plainTextPassword = $request->toArray()['password'] ?? null;
        if (!$userMail || !$plainTextPassword) {
            $message = "Create account missing userMail or password";
            return new JsonResponse(['message' => $message], JsonResponse::HTTP_BAD_REQUEST, []);
        }
        $user = new User();
        $user->setEmail($userMail);
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plainTextPassword
        );
        $user->setPassword($hashedPassword);
        $userRepository->save($user, true);
        $jsonUser = $serializer->serialize($user, 'json');
        return new JsonResponse($jsonUser, Response::HTTP_CREATED);
    }
}
