<?php

namespace EtlBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnexpectedResultException;
use EtlBundle\Entity\Product;
use Exception;

/**
 * Class ProductRepository
 * @package EtlBundle\Repository
 */
class ProductRepository extends EntityRepository {
    /**
     * @param string $name
     * @return bool
     */
    public function productExists($name) {
        return $this->findOneBy(['name' => $name]) !== null;
    }

    /**
     * @param string $name
     * @return Product|null
     */
    public function getProduct($name) {
        $query = $this->createQueryBuilder('product')
            ->select('product, comments, image, features, reviews')
            ->leftJoin('product.comments', 'comments')
            ->leftJoin('product.image', 'image')
            ->leftJoin('product.features', 'features')
            ->leftJoin('comments.reviews', 'reviews')
            ->where('product.name = ?1')
            ->setParameter(1, $name);

        try {
            return $query->getQuery()->getSingleResult();
        } catch (UnexpectedResultException $ex) {
            return null;
        }
    }

    /**
     * @param Product $product
     * @throws Exception
     */
    public function delete(Product $product) {
        $em = $this->getEntityManager();
        $em->beginTransaction();

        try {
            $em->remove($product);
            $em->flush();
            $em->commit();
        } catch (Exception $ex) {
            $em->rollback();
            throw $ex;
        }
    }

    /**
     * @param Product $product
     * @throws Exception
     */
    public function save(Product $product) {
        $em = $this->getEntityManager();
        $em->beginTransaction();

        try {
            $em->persist($product);
            $em->flush();
            $em->commit();
        } catch (Exception $ex) {
            $em->rollback();
            throw $ex;
        }
    }
}