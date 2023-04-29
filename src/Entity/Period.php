<?php

namespace App\Entity;

use App\Repository\PeriodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PeriodRepository::class)]
class Period
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getActivities", "getPeriods"])]
    private ?int $id = null;


 
    #[ORM\ManyToOne(inversedBy: 'periods')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["getPeriods"])]
    #[Assert\NotBlank(message:"L'identifiant activity_id est obligatoire")]
    private ?Activity $activity = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["getActivities", "getPeriods"])]
    private ?\DateTime $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(["getActivities", "getPeriods"])]
    private ?\DateTime $stop = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["getActivities", "getPeriods"])]
    private ?string $title = null;

    #[ORM\ManyToMany(targetEntity: PeriodTag::class, inversedBy: 'periods')]
    #[Groups(["getActivities", "getPeriods"])]
    private Collection $Tags;

    public function __construct()
    {
        $this->Tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->getActivity()->getUser();
    }

   

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getStart(): ?\DateTime
    {
        return $this->start;
    }

    public function setStart(\DateTime $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getStop(): ?\DateTime
    {
        return $this->stop;
    }

    public function setStop(\DateTime $stop): self
    {
        $this->stop = $stop;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, PeriodTag>
     */
    public function getTags(): Collection
    {
        return $this->Tags;
    }

    public function addTag(PeriodTag $tag): self
    {
        if (!$this->Tags->contains($tag)) {
            $this->Tags->add($tag);
        }

        return $this;
    }

    public function removeTag(PeriodTag $tag): self
    {
        $this->Tags->removeElement($tag);

        return $this;
    }
}
