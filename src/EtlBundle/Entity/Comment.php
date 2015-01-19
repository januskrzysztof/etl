<?php

namespace EtlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Class Comment
 * @package EtlBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="products_comments")
 */
class Comment {
    const BUY_CONFIRMED = 1;
    const NOT_CONFIRMED = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $review;

    /**
     * @ORM\Column(type="smallint")
     *
     * @var int
     */
    protected $confirmed = self::NOT_CONFIRMED;

    /**
     * @ORM\Column(length=255)
     *
     * @var string
     */
    protected $author;

    /**
     * @ORM\OneToMany(targetEntity="EtlBundle\Entity\Product", mappedBy="comments")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     *
     * @var Product
     */
    protected $product;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="float")
     *
     * @var float
     */
    protected $rate = 0.0;

    /**
     * @param string $review
     */
    public function setReview($review) {
        $this->review = $review;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product) {
        $this->product = $product;
    }

    /**
     * @param int $confirmed
     */
    public function setConfirmed($confirmed) {
        $this->confirmed = (int) $confirmed;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author) {
        $this->author = $author;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt = null) {
        $this->createdAt = $createdAt;
    }

    /**
     * @param float $rate
     */
    public function setRate($rate) {
        $this->rate = (float) $rate;
    }
}