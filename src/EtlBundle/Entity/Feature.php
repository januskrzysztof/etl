<?php

namespace EtlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Feature
 * @package EtlBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="products_features")
 */
class Feature {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(length=255)
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $pro = 0;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $con = 0;
    /**
     * @ORM\OneToMany(targetEntity="EtlBundle\Entity\Product", mappedBy="features")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     *
     * @var Product
     */
    protected $product;

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * @param int $pro
     */
    public function setPro($pro) {
        $this->pro = (int) $pro;
    }

    /**
     * @param int $con
     */
    public function setCon($con) {
        $this->con = (int) $con;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product) {
        $this->product = $product;
    }
}