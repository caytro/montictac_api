<?php

namespace App\Controller;

use App\Entity\Period;
use App\Repository\ActivityRepository;
use App\Repository\PeriodRepository;
use App\Repository\PeriodTagRepository;
use App\Service\MonTicTacService as MonTicTac;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints\DateTime as ConstraintsDateTime;

class PeriodController extends AbstractController
{
    #[Route('/api/period', name: 'createPeriod', methods: ['POST'])]
    public function createPeriod(
        Request $request,
        SerializerInterface $serializer,
        ActivityRepository $activityRepository,
        UrlGeneratorInterface $urlGenerator,
        MonTicTac $monTicTac
    ): JsonResponse {
        /* Récupère activity dans la requete -> evolution ? : + title et description */
        $user = $this->getUser();
        $activityId = $request->toArray()['activity_id'] ?? null;
        if (!$activityId) {
            $message = "Missing activity_id";
            return new JsonResponse($message, JsonResponse::HTTP_BAD_REQUEST, []);
        }
        $activity = $activityRepository->findBy(['user' => $user, "id" => $activityId])[0] ?? null;
        if (!$activity) {
            $message = "Aucune activité id = " . $activityId . " pour user " . $user->getUserIdentifier();
            return new JsonResponse($message, JsonResponse::HTTP_BAD_REQUEST, []);
        }
        $periodParam = $request->toArray()['period'];
        if (!$periodParam) {
            //période démarrée à la volée
            $newPeriod = $monTicTac->startActivityPeriod($activity);
        } else {
            $newPeriod = $monTicTac->createActivityPeriod($activity, $periodParam);
        }

        $jsonPeriod = $serializer->serialize($newPeriod, 'json', ["groups" => "getPeriods", DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']);
        $location = $urlGenerator->generate('getPeriod', ['id' => $newPeriod->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonPeriod, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/period/list', name: 'getPeriodList', methods: ['GET'])]
    public function getPeriodList(PeriodRepository $periodRepository, ActivityRepository $activityRepository,  SerializerInterface $serializer): JsonResponse
    {
        $activities = $activityRepository->findBy(['user' => $this->getUser()]);
        $periods = [];
        foreach ($activities as $activity) {
            $periods[] = $activity->getPeriods();
        }
        $jsonPeriods = $serializer->serialize($periods, 'json', ["groups" => "getPeriods", DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']);
        return new JsonResponse($jsonPeriods, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/period/{id}', name: 'getPeriod', methods: ['GET'])]
    public function getPeriod(int $id, PeriodRepository $periodRepository, SerializerInterface $serializer): JsonResponse
    {
        $period = $periodRepository->find($id) ?? null;
        if (!$period) {
            $message = "Aucune periode id = " . $id;
            return new JsonResponse($message, JsonResponse::HTTP_NOT_FOUND, []);
        }
        if ($period->getActivity()->getUser() !== $this->getUser()) {
            $message = "Aucune periode id = " . $id . " pour user " . $this->getUser()->getUserIdentifier();
            return new JsonResponse($message, JsonResponse::HTTP_NOT_FOUND, []);
        }
        $jsonPeriod = $serializer->serialize($period, 'json', ["groups" => "getPeriods", DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']);
        return new JsonResponse($jsonPeriod, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/period/{id}', name: 'deletePeriod', methods: ['DELETE'])]
    public function deletePeriod(int $id, PeriodRepository $periodRepository): JsonResponse
    {

        $period = $periodRepository->find($id) ?? null;
        if (!$period) {
            $message = "Aucune periode id = " . $id;
            return new JsonResponse($message, JsonResponse::HTTP_NOT_FOUND, []);
        }
        if ($period->getActivity()->getUser() !== $this->getUser()) {
            $message = "Aucune periode id = " . $id . " pour user " . $this->getUser()->getUserIdentifier();
            return new JsonResponse($message, JsonResponse::HTTP_NOT_FOUND, []);
        }
        $periodRepository->remove($period, true);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/period/stop/{id}', name: 'stopPeriod', methods: ['PUT'])]
    public function stopPeriod(int $id, PeriodRepository $periodRepository, EntityManagerInterface $em, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $period = $periodRepository->find($id) ?? null;
        if (!$period) {
            $message = "Aucune periode id = " . $id;
            return new JsonResponse($message, JsonResponse::HTTP_NOT_FOUND, []);
        }
        if ($period->getActivity()->getUser() !== $this->getUser()) {
            $message = "Aucune periode id = " . $id . " pour user " . $this->getUser()->getUserIdentifier();
            return new JsonResponse($message, JsonResponse::HTTP_NOT_FOUND, []);
        }
        $stop = new DateTime();
        $period->setStop($stop);
        $em->persist($period);
        $em->flush();
        $jsonPeriod = $serializer->serialize($period, 'json', ["groups" => "getPeriods", DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']);
        $location = $urlGenerator->generate('getPeriod', ['id' => $period->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonPeriod, JsonResponse::HTTP_OK, ["Location" => $location], true);
    }

    #[Route('/api/period/{id}', name: 'updatePeriod', methods: ['PUT'])]
    public function updatePeriod(int $id, PeriodRepository $periodRepository, Request $request, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $period = $periodRepository->find($id) ?? null;
        if (!$period) {
            $message = "Aucune periode id = " . $id;
            return new JsonResponse($message, JsonResponse::HTTP_NOT_FOUND, []);
        }
        if ($period->getActivity()->getUser() !== $this->getUser()) {
            $message = "Aucune periode id = " . $id . " pour user " . $this->getUser()->getUserIdentifier();
            return new JsonResponse($message, JsonResponse::HTTP_NOT_FOUND, []);
        }
        $updatedPeriod = $request->toArray()['period'] ?? null;
        if (!$updatedPeriod) {
            $message = "Bad Request: le corps de la requete doit contenir un objet Period";
            return new JsonResponse($message, JsonResponse::HTTP_BAD_REQUEST, []);
        }
        $title = $updatedPeriod['title'] ?? null;
        if ($title) {
            $period->setTitle($title);
        }
        $start = $updatedPeriod['start'] ?? null;
        if ($start) {
            $period->setStart(new DateTime($start));
        }
        $stop = $updatedPeriod['stop'] ?? null;
        if ($stop) {
            $period->setStop(new DateTime($stop));
        }

        $periodRepository->save($period, true);
        $jsonPeriod = $serializer->serialize($period, 'json', ['groups' => 'getPeriods', DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']);
        return new JsonResponse($jsonPeriod, JsonResponse::HTTP_OK);
    }
}
