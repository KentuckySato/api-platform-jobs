<?php

namespace App\Entity;

use App\Entity\Recruiter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CompanyRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['recruiter:collection:get']],
)]
#[GetCollection(normalizationContext: ['groups' => ['company:collection:get', 'recruiter:collection:get']])]
#[Get(normalizationContext: ['groups' => ['company:item:get', 'recruiter:collection:get']])]
#[Post(
    normalizationContext: ['groups' => ['company:item:get']],
    denormalizationContext: ['groups' => ['company:post']]
)]
#[Put(
    normalizationContext: ['groups' => ['company:item:get']],
    denormalizationContext: ['groups' => ['company:put']]
)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['company:item:get', 'company:collection:get', 'company:post'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['company:item:get', 'company:collection:get', 'company:post'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['company:item:get', 'company:collection:get', 'company:post'])]
    private ?string $reference = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['company:item:get', 'company:collection:get', 'company:post'])]
    private ?string $description = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['company:item:get', 'company:collection:get'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['company:item:get', 'company:collection:get'])]
    private ?string $website = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['company:item:get', 'company:collection:get'])]
    private ?string $siren = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['company:item:get', 'company:collection:get'])]
    private ?string $siret = null;

    #[ORM\Column]
    #[Groups(['company:item:get', 'company:collection:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['company:collection:get', 'company:put'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Recruiter::class)]
    #[Groups(['company:collection:get', 'company:item:get'])]
    private Collection $recruiters;

    public function __construct()
    {
        $this->recruiters = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(?string $siren): self
    {
        $this->siren = $siren;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

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

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }


    /**
     * @return Collection<int, Recruiter>
     */
    public function getRecruiters(): Collection
    {
        return $this->recruiters;
    }

    public function addRecruiter(Recruiter $recruiter): self
    {
        if (!$this->recruiters->contains($recruiter)) {
            $this->recruiters->add($recruiter);
            $recruiter->setCompany($this);
        }

        return $this;
    }

    public function removeRecruiter(Recruiter $recruiter): self
    {
        if ($this->recruiters->removeElement($recruiter)) {
            // set the owning side to null (unless already changed)
            if ($recruiter->getCompany() === $this) {
                $recruiter->setCompany(null);
            }
        }

        return $this;
    }
}
