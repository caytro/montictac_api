<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use App\Service\MonTicTacService as MonTicTac;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class ActivityController extends AbstractController
{

    #[Route('/api/activity/list', name: 'getActivityList', methods: ['GET'])]
    public function getAllActivities(
        SerializerInterface $serializer,
        ActivityRepository $activityRepository
    ): JsonResponse {

        $activityList = $activityRepository->findBy(['user' => $this->getUser()]);
        $jsonActivityList = $serializer->serialize($activityList, 'json', ['groups' => 'getActivities', DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']);
        return new JsonResponse($jsonActivityList, Response::HTTP_OK, [], true);
    }



    #[Route('/api/activity/{id}', name: 'getActivity', methods: ['GET'])]
    public function getActivity(int $id, SerializerInterface $serializer, ActivityRepository $activityRepository): JsonResponse
    {
        $activity = $activityRepository->findBy(['id' => $id, 'user' => $this->getUser()])[0] ?? null;
        if ($activity) {
            $jsonActivity = $serializer->serialize($activity, 'json', ['groups' => 'getActivities', DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']);
            return new JsonResponse($jsonActivity, Response::HTTP_OK, [], true);
        } else {
            $userIdentifier = $this->getUser()->getUserIdentifier();
            $message = "Aucune activité id = " . $id . " pour user " . $userIdentifier;
            return new JsonResponse(['message' => $message], JsonResponse::HTTP_BAD_REQUEST, []);
        }
    }


    #[Route('/api/activity', name: 'createActivity', methods: ['POST'])]
    public function createActivity(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse {
        $activity = $serializer->deserialize($request->getContent(), Activity::class, 'json');
        $activity->setUser($this->getUser());


        // On vérifie les erreurs
        $errors = $validator->validate($activity);
        if ($errors->count() > 0) {
            //dd($errors);
            return new JsonResponse($serializer->serialize($errors[0]->getMessage(), 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($activity);
        $em->flush();
        $jsonActivity = $serializer->serialize($activity, 'json', ['groups' => 'getActivities']);

        $location = $urlGenerator->generate('getActivity', ['id' => $activity->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonActivity, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/activity/{id}', name: 'updateActivity', methods: ['PUT'])]
    public function updateActivity(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        ActivityRepository $activityRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        $currentActivity = $activityRepository->findBy(['id' => $id, 'user' => $this->getUser()])[0] ?? null;
        if (!$currentActivity) {
            $userIdentifier = $this->getUser()->getUserIdentifier();
            $message = "Aucune activité id = " . $id . " pour user " . $userIdentifier;
            return new JsonResponse(['message' => $message], JsonResponse::HTTP_BAD_REQUEST, []);
        }
        $updatedActivity = $serializer->deserialize($request->getContent(), Activity::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentActivity]);

        $errors = $validator->validate($updatedActivity);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors[0]->getMessage(), 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($updatedActivity);
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT, []);
    }

    #[Route('/api/activity/start/{id}', name: 'startActivity', methods: ['GET'])]
    public function startActivity(int $id, ActivityRepository $activityRepository, MonTicTac $monTicTac, SerializerInterface $serializer): JsonResponse
    {
        $activity = $activityRepository->findBy(['id' => $id, 'user' => $this->getUser()])[0] ?? null;
        if (!$activity) {
            $message = "Aucune activité id = " . $id . " pour user " . $this->getUser()->getUserIdentifier();
            return new JsonResponse($message, JsonResponse::HTTP_BAD_REQUEST);
        }
        $monTicTac->startActivityPeriod($activity, true);
        $jsonActivity = $serializer->serialize($activityRepository->find($activity->getId()), 'json', ['groups' => 'getActivities', DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']);
        return new JsonResponse($jsonActivity, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/activity/stop/{id}', name: 'stopActivity', methods: ['GET'])]
    public function stopActivity(int $id, ActivityRepository $activityRepository, MonTicTac $monTicTac, SerializerInterface $serializer): JsonResponse
    {
        $activity = $activityRepository->findBy(['id' => $id, 'user' => $this->getUser()])[0] ?? null;
        if (!$activity) {
            $message = "Aucune activité id = " . $id . " pour user " . $this->getUser()->getUserIdentifier();
            return new JsonResponse($message, JsonResponse::HTTP_BAD_REQUEST);
        }
        $monTicTac->stopAllActivityPeriods($activity);
        $jsonActivity = $serializer->serialize($activityRepository->find($activity->getId()), 'json', ['groups' => 'getActivities', DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']);
        return new JsonResponse($jsonActivity, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/activity/{id}', name: 'deleteActivity', methods: ['DELETE'])]
    public function deleteActivity(int $id, ActivityRepository $activityRepository): JsonResponse
    {
        $activity = $activityRepository->findBy(['id' => $id, 'user' => $this->getUser()])[0] ?? null;
        if (!$activity) {
            $message = 'Aucune activité id = ' . $id . ' pour user ' . $this->getUser()->getUserIdentifier();
            return new JsonResponse($message, JsonResponse::HTTP_BAD_REQUEST, []);
        }
        $activityRepository->remove($activity, true);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT, []);
    }
}
