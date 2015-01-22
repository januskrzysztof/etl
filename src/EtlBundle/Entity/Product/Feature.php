<?php

namespace EtlBundle\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
use EtlBundle\Entity\Product;

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
    protected $advantages = 0;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $disadvantages = 0;
    /**
     * @ORM\ManyToOne(targetEntity="EtlBundle\Entity\Product", inversedBy="features")
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

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param $advantages
     */
    public function setAdvantages($advantages) {
        $this->advantages = (int) $advantages;
    }

    /**
     * @return int
     */
    public function getAdvantages() {
        return $this->advantages;
    }

    /**
     * @param $disadvantages
     */
    public function setDisadvantages($disadvantages) {
        $this->disadvantages = (int) $disadvantages;
    }

    /**
     * @return int
     */
    public function getDisadvantages() {
        return $this->disadvantages;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product) {
        $this->product = $product;
    }
}