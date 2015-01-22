<?php

namespace EtlBundle\Logic;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EtlBundle\Entity\ToArrayInterface;

/**
 * Class ObjectToArray
 * @package EtlBundle\Logic
 */
class ObjectToArray {
    /**
     * @param ToArrayInterface $object
     * @return mixed
     */
    public static function toArray(ToArrayInterface $object) {
        return $object->toArray();
    }

    /**
     * @param Collection $collection
     * @return array
     */
    public static function arrayCollectionToArray(Collection $collection) {
        $array = [];
        foreach ($collection as $item) {
            if ($item instanceof ToArrayInterface) {
                $array[] = $item->toArray();
            }
        }

        return $array;
    }
}