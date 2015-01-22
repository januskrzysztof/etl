<?php

namespace EtlBundle\Entity\Product\Comment;

use Doctrine\ORM\Mapping as ORM;
use EtlBundle\Entity\Product\Comment;
use EtlBundle\Entity\ToArrayInterface;
use InvalidArgumentException;

/**
 * Class Review
 * @package EtlBundle\Entity\Product\Comment
 *
 * @ORM\Entity()
 * @ORM\Table(name="products_comments_reviews")
 */
class Review implements ToArrayInterface {
    const TYPE_ADVANTAGE = 1;
    const TYPE_DISADVANTAGE = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="smallint")
     *
     * @var int
     */
    protected $type;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $review;

    /**
     * @ORM\ManyToOne(targetEntity="EtlBundle\Entity\Product\Comment", inversedBy="reviews")
     * @ORM\JoinColumn(name="comment_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var Comment
     */
    protected $comment;

    /**
     * @param int $type
     */
    public function setType($type) {
        if ($type === self::TYPE_ADVANTAGE || self::TYPE_DISADVANTAGE) {
            $this->type = (int)$type;
        } else {
            throw new InvalidArgumentException("Type: '$type' is not valid.");
        }
    }

    /**
     * @return int
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $review
     */
    public function setReview($review) {
        $this->review = $review;
    }

    /**
     * @return string
     */
    public function getReview() {
        return $this->review;
    }

    /**
     * @param Comment $comment
     */
    public function setComment(Comment $comment) {
        $this->comment = $comment;
    }

    /**
     * @return array
     */
    public function toArray() {
        return [
            'type' => $this->type === self::TYPE_ADVANTAGE ? 'advantage' : 'disadvantage',
            'review' => $this->review
        ];
    }
}