<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Entity
 * @ORM\Table(name="auction_cart")
 */
class Cart
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * @var User
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="cart")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CartItem", mappedBy="cart", cascade={"persist", "remove"})
     */
    protected $cartItems;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
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
     * @return Collection|CartItem[]
     */
    public function getCartItems(): ?Collection
    {
        return $this->cartItems;
    }

    /**
     * @param CartItem $cartItem
     * @return $this
     */
    public function addCartItem(CartItem $cartItem): self
    {
        $cartItem->setCart($this);
        $this->cartItems->add($cartItem);

        return $this;
    }

    /**
     * @param CartItem $cartItem
     * @return bool
     */
    public function removeCartItem(CartItem $cartItem): bool
    {
        $cartItem->setCart(null);
        return $this->cartItems->removeElement($cartItem);
    }

    public function getTotalCart()
    {
        $total = 0;

        foreach ($this->cartItems as $item) {
            $total += $item->getPrice();
        }

        return $total;
    }

    public function findItemByProduct(Product $product): bool
    {
        foreach ($this->cartItems as $item) {
            if ($item->getProduct()->getId() == $product->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed|string|null
     */
    public function __toString(): string
    {
        return $this->getId();
    }
}
