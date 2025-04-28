<?php

namespace App\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiRegisterController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface         $serializer,
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    #[Route(
        "/api/user/register",
        name: "api_register",
        defaults: [
            "_api_resource_class" => User::class,
            "_api_item_operation_name" => "api_register"
        ],
        methods: ["POST"]
    )]
    public function register(Request $request): JsonResponse
    {
        $data = $request->getContent();
        $user = $this->serializer->deserialize($data, User::class, 'json');
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        $this->userRepository->add($user);
        return new JsonResponse(['message' => 'User registered successfully'], JsonResponse::HTTP_CREATED);
    }
}
