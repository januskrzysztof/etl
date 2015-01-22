<?php

namespace EtlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EtlBundle\Entity\Product;
use EtlBundle\Logic\Mappers\Ceneo\CeneoMapperParser;
use Exception;

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
     * @Route("/product/generate/form", name="product_form_generate")
     */
    public function formAction(Request $request) {
        $name = $request->get('product-name');
        return $this->redirect($this->generateUrl('product_generate', ['name' => $name]));
    }

    /**
     * @Route("/generate/{name}", name="product_generate")
     * @Template()
     *
     * @param $name
     * @return array
     * @throws Exception
     */
    public function generateAction($name) {
        $repository = $this->getDoctrine()->getRepository(Product::class);

        if ($repository->productExists($name)) {
            $product = $repository->findOneBy(['name' => $name]);
        } else {
            $mapper  = new CeneoMapperParser($name);
            $product = $mapper->parse();

            if ($product !== null) {
                $repository->save($product);
            }
        }

        return array(
            'name'    => $name,
            'product' => $product
        );
    }

    /**
     * @Route("/product/delete/{$name}", name="product_delete")
     *
     * @param string $name
     * @return Response
     */
    public function deleteAction($name) {
        return new Response("Delete product: ".$name);
    }
}