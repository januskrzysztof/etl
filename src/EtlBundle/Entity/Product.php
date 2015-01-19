<?php

namespace EtlBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Product
 * @package EtlBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="products")
 */
class Product {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="EtlBundle\Entity\Feature", inversedBy="product", cascade={"all"})
     *
     * @var Feature[]
     */
    protected $features;

    /**
     * @ORM\ManyToOne(targetEntity="EtlBundle\Entity\Comment", cascade={"all"})
     *
     * @var Comment[]
     */
    protected $comments;

    public function __construct() {
        $this->features = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @param Feature $feature
     */
    public function addFeature(Feature $feature) {
        $feature->setProduct($this);
        $this->features[] = $feature;
    }

    /**
     * @return Feature[]
     */
    public function getFeatures() {
        return $this->features;
    }

    /**
     * @param Comment $comment
     */
    public function addComment(Comment $comment) {
        $comment->setProduct($this);
        $this->comments[] = $comment;
    }

    /**
     * @return Comment[]
     */
    public function getComments() {
        return $this->comments;
    }
}