<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\JobRepository;
use App\State\JobStateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ApiResource]
#[GetCollection(normalizationContext: ['groups' => ['job:collection:get',  'recruiter:item:get']])]
#[Get(normalizationContext: ['groups' => ['job:item:get']])]
#[Post(
    processor: JobStateProcessor::class,
    normalizationContext: ['groups' => ['job:item:get']],
    denormalizationContext: ['groups' => ['job:post']]
)]
#[Put(
    processor: JobStateProcessor::class,
    normalizationContext: ['groups' => ['job:item:get']],
    denormalizationContext: ['groups' => ['job:put']]
)]
#[Delete]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['job:collection:get', 'job:item:get'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post'])]
    private ?string $reference = null;

    #[ORM\Column]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?int $status = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?int $contract = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?float $contractDuration = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?int $educationLevel = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?int $experienceLevel = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?int $startAsap = null;

    #[ORM\Column]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?int $salaryLow = null;

    #[ORM\Column]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?int $salaryHigh = null;

    #[ORM\Column]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?bool $salaryPrivacy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?string $context = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?string $profile = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?string $comment = null;

    #[ORM\Column]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post', 'job:put'])]
    private ?bool $fullRemote = null;

    #[ORM\Column]
    #[Groups(['job:collection:get', 'job:item:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['job:collection:get', 'job:item:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'jobs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post'])]
    private ?Company $company = null;

    #[ORM\ManyToOne(inversedBy: 'jobs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['job:collection:get', 'job:item:get', 'job:post'])]
    private ?Recruiter $recruiter = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getContract(): ?int
    {
        return $this->contract;
    }

    public function setContract(?int $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    public function getContractDuration(): ?float
    {
        return $this->contractDuration;
    }

    public function setContractDuration(?float $contractDuration): self
    {
        $this->contractDuration = $contractDuration;

        return $this;
    }

    public function getEducationLevel(): ?int
    {
        return $this->educationLevel;
    }

    public function setEducationLevel(?int $educationLevel): self
    {
        $this->educationLevel = $educationLevel;

        return $this;
    }

    public function getExperienceLevel(): ?int
    {
        return $this->experienceLevel;
    }

    public function setExperienceLevel(?int $experienceLevel): self
    {
        $this->experienceLevel = $experienceLevel;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getStartAsap(): ?int
    {
        return $this->startAsap;
    }

    public function setStartAsap(?int $startAsap): self
    {
        $this->startAsap = $startAsap;

        return $this;
    }

    public function getSalaryLow(): ?int
    {
        return $this->salaryLow;
    }

    public function setSalaryLow(int $salaryLow): self
    {
        $this->salaryLow = $salaryLow;

        return $this;
    }

    public function getSalaryHigh(): ?int
    {
        return $this->salaryHigh;
    }

    public function setSalaryHigh(int $salaryHigh): self
    {
        $this->salaryHigh = $salaryHigh;

        return $this;
    }

    public function isSalaryPrivacy(): ?bool
    {
        return $this->salaryPrivacy;
    }

    public function setSalaryPrivacy(bool $salaryPrivacy): self
    {
        $this->salaryPrivacy = $salaryPrivacy;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(?string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getProfile(): ?string
    {
        return $this->profile;
    }

    public function setProfile(string $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function isFullRemote(): ?bool
    {
        return $this->fullRemote;
    }

    public function setFullRemote(bool $fullRemote): self
    {
        $this->fullRemote = $fullRemote;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getRecruiter(): ?Recruiter
    {
        return $this->recruiter;
    }

    public function setRecruiter(?Recruiter $recruiter): self
    {
        $this->recruiter = $recruiter;

        return $this;
    }
}
