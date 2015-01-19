<?php

namespace EtlBundle\Logic\Mappers;
use EtlBundle\Entity\Product;

/**
 * Interface MapperParserInterface
 * @package EtlBundle\Logic\Mappers
 */
interface MapperParserInterface {
    /**
     * @return Product
     */
    public function parse();
}