<?php

namespace EtlBundle\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EtlBundle\Entity\Product\Comment\Review;
use EtlBundle\Entity\Product;
use EtlBundle\Entity\ToArrayInterface;
use EtlBundle\Logic\ObjectToArray;
use InvalidArgumentException;
use DateTime;

/**
 * Class Comment
 * @package EtlBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="products_comments")
 */
class Comment implements ToArrayInterface {
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
    protected $comment;

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
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    protected $createdAt = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @var float
     */
    protected $rate = 0.0;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     *
     * @var int
     */
    protected $useful = 0;

    /**
     * @ORM\ManyToOne(targetEntity="EtlBundle\Entity\Product", inversedBy="comments")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     *
     * @var Product
     */
    protected $product;

    /**
     * @ORM\OneToMany(targetEntity="EtlBundle\Entity\Product\Comment\Review", mappedBy="comment", cascade={"all"})
     *
     * @var Review[]
     */
    protected $reviews;

    public function __construct() {
        $this->reviews = new ArrayCollection();
    }

    /**
     * @param string $comment
     */
    public function setComment($comment) {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getComment() {
        return $this->comment;
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
     * @return int
     */
    public function getConfirmed() {
        return $this->confirmed;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author) {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt = null) {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * @param float $rate
     */
    public function setRate($rate) {
        $this->rate = (float) $rate;
    }

    /**
     * @param Review $review
     */
    public function addReview(Review $review) {
        $review->setComment($this);
        $this->reviews[] = $review;
    }

    /**
     * @return ArrayCollection|Review[]
     */
    public function getReviews() {
        return $this->reviews;
    }

    /**
     * @return ArrayCollection|Review[]
     */
    public function getAdvantageReviews() {
        return $this->getReviewsByType(Review::TYPE_ADVANTAGE);
    }

    /**
     * @return ArrayCollection|Review[]
     */
    public function getDisadvantageReviews() {
        return $this->getReviewsByType(Review::TYPE_DISADVANTAGE);
    }

    /**
     * @param int $type
     * @return ArrayCollection|Review[]
     */
    private function getReviewsByType($type) {
        $reviews = new ArrayCollection();
        foreach ($this->reviews as $review) {
            if ($review->getType() === $type) {
                $reviews[] = $review;
            }
        }

        return $reviews;
    }

    /**
     * @param int $useful
     */
    public function setUseful($useful) {
        $useful = (int) $useful;
        if ($useful >= 0 || 100 <= $useful) {
            $this->useful = $useful;
        } else {
            throw new InvalidArgumentException("Useful must be between: 0 and 100. In percentage.");
        }
    }

    /**
     * @return int
     */
    public function getUseful() {
        return $this->useful;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'author' => $this->author,
            'confirmed' => $this->confirmed == self::BUY_CONFIRMED ? 'buy confirmed' : 'not confirmed',
            'date' => $this->createdAt->format('d.m.Y'),
            'useful' => $this->useful,
            'rate' => $this->rate,
        ];
    }
}