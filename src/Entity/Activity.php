<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getActivities", "getPeriods"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getActivities", "getPeriods"])]
    #[Assert\NotBlank(message:"Le titre de l'activité est obligatoire")]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["getActivities", "getPeriods"])]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: Period::class, cascade: ['persist', 'remove'])]
    #[Groups(["getActivities"])]
    private Collection $periods;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["getActivities", "getPeriods"])]
    #[Assert\NotBlank(message:"L'identifiant user_id est obligatoire")]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: ActivityTag::class, inversedBy: 'activities')]
    #[Groups(["getActivities", "getPeriods"])]
    private Collection $tags;

    
    
    public function __construct()
    {
        $this->periods = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Period>
     */
    public function getPeriods(): Collection
    {
        return $this->periods;
    }

    public function addPeriod(Period $period): self
    {
        if (!$this->periods->contains($period)) {
            $this->periods->add($period);
            $period->setActivity($this);
        }

        return $this;
    }

    public function removePeriod(Period $period): self
    {
        if ($this->periods->removeElement($period)) {
            // set the owning side to null (unless already changed)
            if ($period->getActivity() === $this) {
                $period->setActivity(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, ActivityTag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(ActivityTag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(ActivityTag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

 
}
