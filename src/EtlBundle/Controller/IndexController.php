<?php

namespace EtlBundle\Controller;

use EtlBundle\Logic\Mappers\Ceneo\CeneoMapperParser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Annotation
 */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class IndexController
 * @package EtlBundle\Controller
 */
class IndexController extends Controller {
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Route("/generate/{name}", name="generate")
     * @Template()
     */
    public function generateAction($name) {
        $mapper  = new CeneoMapperParser($name);
        $product = $mapper->parse();

        var_dump($product->getFeatures());
        var_dump($product->getComments());

        return array(
            'name'    => $name,
            'product' => $product
        );
    }
}