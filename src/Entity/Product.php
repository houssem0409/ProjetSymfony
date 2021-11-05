<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use App\Repository\ProductRepository;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\Table(name="auction_product")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $shortDescription;

    /**
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $transactionType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $timeLimit;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="products")
     */
    protected $category;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="products")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Auction", mappedBy="product")
     */
    protected $auctions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="product",cascade={"persist"})
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CartItem", mappedBy="product")
     */
    protected $cartItems;

    public function __construct()
    {
        $this->auctions = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->cartItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param mixed $shortDescription
     */
    public function setShortDescription($shortDescription): void
    {
        $this->shortDescription = $shortDescription;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Collection|Auction[]
     */
    public function getAuctions(): ?Collection
    {
        return $this->auctions;
    }

    /**
     * @param Auction $auction
     * @return $this
     */
    public function addAuction(Auction $auction): self
    {
        $this->auctions->add($auction);

        return $this;
    }

    /**
     * @param Auction $auction
     * @return bool
     */
    public function removeAuction(Auction $auction): bool
    {
        return $this->auctions->removeElement($auction);
    }

    /**
     * @return Collection|Image[]
     */
    public function getImage(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @param mixed $transactionType
     */
    public function setTransactionType($transactionType): void
    {
        $this->transactionType = $transactionType;
    }

    /**
     * @return mixed
     */
    public function getTimeLimit()
    {
        return $this->timeLimit;
    }

    /**
     * @param mixed $timeLimit
     */
    public function setTimeLimit($timeLimit): void
    {
        $this->timeLimit = $timeLimit;
    }

    /**
     * @return Collection
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * @param Collection $images
     */
    public function setImages(Collection $images): void
    {
        $this->images = $images;
    }

    public function getFirstImage(): ?Image
    {
        return $this->images->first();
    }

    public function getMaxPrice(): float
    {
        if (!$this->auctions->count()) {
            return $this->getPrice();
        }

        $criteria = Criteria::create()
            ->orderBy(array("price" => Criteria::DESC));

        return $this->auctions->matching($criteria)->first()->getPrice();
    }

    public function getLastAuction()
    {
        return $this->auctions->last();
    }

    public function status(): string
    {
        $item =$this->getItem();
        if ($this->transactionType == 'A') {
            if($item){
                return 'vendu à '. $item->getCart()->getUser()->getFullName();
            }else{
                return 'Vente en cours';
            }
        }

        $timeLimit = $this->getTimeLimit() ?? 0;
        $expirationDate = $this->getCreatedAt()->modify('+ ' . $timeLimit . ' hour');
        $now = new \DateTime("now");

        if( $expirationDate > $now){
            return 'Vente en cours';
        }else{
            if($item){
                return 'vendu à '. $item->getCart()->getUser()->getFullName();
            }else{
                return 'Invendu';
            }
        }
    }

    /**
     * @return Collection|CartItem[]
     */
    public function getCartItems(): ?Collection
    {
        return $this->cartItems;
    }

    public function getItem()
    {
        return $this->cartItems->first();
    }

    /**
     * @return mixed|string|null
     */
    public function __toString(): string
    {
        return $this->getName() ?? '';
    }
}
