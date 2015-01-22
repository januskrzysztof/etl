<?php

namespace EtlBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EtlBundle\Entity\Product\Comment;
use EtlBundle\Entity\Product\Feature;

/**
 * Class Product
 * @package EtlBundle\Entity
 *
 * @ORM\Entity(repositoryClass="EtlBundle\Repository\ProductRepository")
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
     * @ORM\Column(length=255, nullable=false)
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="EtlBundle\Entity\File", cascade={"all"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     *
     * @var File
     */
    protected $image;

    /**
     * @ORM\OneToMany(targetEntity="EtlBundle\Entity\Product\Feature", mappedBy="product", cascade={"all"})
     *
     * @var Feature[]
     */
    protected $features;

    /**
     * @ORM\OneToMany(targetEntity="EtlBundle\Entity\Product\Comment", mappedBy="product", cascade={"all"})
     *
     * @var Comment[]
     */
    protected $comments;

    public function __construct() {
        $this->features = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param Feature $feature
     */
    public function addFeature(Feature $feature) {
        $feature->setProduct($this);
        $this->features[] = $feature;
    }

    /**
     * @return ArrayCollection|Feature[]
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

    /**
     * @param File $image
     */
    public function setImage(File $image) {
        $this->image = $image;
    }

    /**
     * @return File
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @return ArrayCollection|Comment[]
     */
    public function getBuyConfirmedComments() {
        return $this->getCommentsByConfirmed(Comment::BUY_CONFIRMED);
    }

    /**
     * @return ArrayCollection|Comment[]
     */
    public function getNotConfirmedComments() {
        return $this->getCommentsByConfirmed(Comment::NOT_CONFIRMED);
    }

    /**
     * @param int $confirmed
     * @return ArrayCollection|Comment[]
     */
    private function getCommentsByConfirmed($confirmed) {
        $comments = new ArrayCollection();
        foreach ($this->comments as $comment) {
            if ($comment->getConfirmed() == $confirmed) {
                $comments[] = $comment;
            }
        }
        return $comments;
    }
}