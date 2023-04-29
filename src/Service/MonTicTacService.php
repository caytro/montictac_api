<?php

namespace App\Service;

use App\Entity\Activity;
use App\Entity\Period;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use \DateTime;

class MonTicTacService
{
    private $em;



    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createActivityPeriod(Activity $activity,  $newPeriod): Period
    {
        $period = new Period();
        $period->setActivity($activity);
        $title = $newPeriod['title'] ?? null;
        if ($title){
            $period->setTitle($title);
        }
        $start = $newPeriod['start'] ?? null;
        if ($start){
            $period->setStart(new DateTime($start));
        }
        $stop = $newPeriod['stop'] ?? null;
        if ($stop){
            $period->setStop(new DateTime($stop));
        }


        $this->em->persist($period);
        $this->em->flush();
        return $period;
    }

    public function startActivityPeriod(Activity $activity): Period
    {
        $period = new Period();
        $period->setActivity($activity);
        $this->stopAllUserActivities($activity->getUser());
        $period->setStart(new DateTime());
        
        $this->em->persist($period);
        $this->em->flush();
        return $period;
    }
    public function stopActivity(Activity $activity): Activity
    {

        return $activity;
    }

    public function stopAllUserActivities(User $user): int
    {
        $userActivities = $user->getActivities();
        foreach ($userActivities as $userActivity) {
            $this->stopAllActivityPeriods($userActivity);
        }
        return 0;
    }

    public function stopAllActivityPeriods(Activity $activity): int
    {
        $periods = $activity->getPeriods();
        $stop = new DateTime();
        foreach ($periods as $period) {
            if (!$period->getStop()) {
                $period->setStop($stop);
                $this->em->persist($period);
            }
        }
        $this->em->flush();
        return 0;
    }
}
