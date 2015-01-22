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
     * @Route("/product/delete/{id}", name="product_delete",
     *      requirements={"id"="\d+"}
     * )
     * @Template()
     *
     * @param string $id
     * @return Response
     */
    public function deleteAction($id) {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $product    = $repository->find((int) $id);
        $success    = false;
        $messages   = null;

        if ($product !== null) {
            $name = $product->getName();
            try {
                $repository->delete($product);
                $success = true;
            } catch (Exception $ex) {
                $messages = $ex->getMessage();
            }
        } else {
            $name = '';
            $messages = $this->get('translator')->trans('ex.cannotRemoveProduct');
        }

        return [
            'name'     => $name,
            'success'  => $success,
            'messages' => $messages
        ];
    }
}