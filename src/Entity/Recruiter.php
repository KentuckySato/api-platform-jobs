<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\RecruiterRepository;
use App\State\RecruiterStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RecruiterRepository::class)]
#[ApiResource]
#[GetCollection(normalizationContext: ['groups' => ['recruiter:collection:get', 'company:collection:get']])]
#[Get(normalizationContext: ['groups' => ['recruiter:item:get', 'recruiter:collection:get']])]
#[Post(
    processor: RecruiterStateProcessor::class,
    normalizationContext: ['groups' => ['recruiter:item:get']],
    denormalizationContext: ['groups' => ['recruiter:post']]
)]
#[Put(
    processor: RecruiterStateProcessor::class,
    normalizationContext: ['groups' => ['recruiter:item:get']],
    denormalizationContext: ['groups' => ['recruiter:put']]
)]
#[Delete]
class Recruiter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['recruiter:collection:get', 'recruiter:item:get'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recruiter:collection:get', 'recruiter:item:get', 'recruiter:post', 'recruiter:put'])]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recruiter:collection:get', 'recruiter:item:get', 'recruiter:post', 'recruiter:put'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recruiter:collection:get', 'recruiter:item:get', 'recruiter:post', 'recruiter:put'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recruiter:collection:get', 'recruiter:item:get', 'recruiter:post', 'recruiter:put'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recruiter:collection:get', 'recruiter:item:get', 'recruiter:post', 'recruiter:put'])]
    private ?string $phone = null;

    #[ORM\ManyToOne(inversedBy: 'recruiters')]
    #[Groups(['recruiter:collection:get', 'recruiter:post', 'recruiter:item:get'])]
    private ?Company $company = null;

    #[ORM\Column]
    #[Groups(['recruiter:collection:get', 'recruiter:item:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['recruiter:collection:get', 'recruiter:item:get', 'recruiter:put'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'recruiter', targetEntity: Job::class, orphanRemoval: true)]
    private Collection $jobs;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->jobs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs->add($job);
            $job->setRecruiter($this);
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getRecruiter() === $this) {
                $job->setRecruiter(null);
            }
        }

        return $this;
    }
}
