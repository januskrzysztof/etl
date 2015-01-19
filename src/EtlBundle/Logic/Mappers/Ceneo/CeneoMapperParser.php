<?php

namespace EtlBundle\Logic\Mappers\Ceneo;

use EtlBundle\Entity\Comment;
use EtlBundle\Entity\Feature;
use EtlBundle\Entity\Product;
use EtlBundle\Logic\Mappers\MapperException;
use EtlBundle\Logic\Mappers\MapperParserInterface;
use InvalidArgumentException;
use DateTime;
use simple_html_dom;

/**
 * Class CeneoMapper
 * @package EtlBundle\Logic\Mappers
 */
class CeneoMapperParser implements MapperParserInterface {
    /**
     * @var string
     */
    public static $url = 'http://www.ceneo.pl';

    /**
     * @var string
     */
    private $item;

    /**
     * @param string $item
     */
    public function __construct($item) {
        $file = realpath(dirname(dirname(dirname(__FILE__))).'/SimpleHtmlDom.php');
        if (!is_string($item)) {
            throw new InvalidArgumentException('Variable "$item" must be string.');
        }
        if ($file) {
            include_once  $file;
        } else {
            throw new InvalidArgumentException("Cannot load SimpleHtmlDom class.");
        }
        $this->item = preg_replace('/\\s/', '+', $item);;
    }

    /**
     * @return Product
     */
    public function parse() {
        $url  = self::$url . "/;szukaj-{$this->item}";
        $html = file_get_html($url);
        $list = $this->first('.category-list-body', $html);

        $product = new Product();
        try {
            $item = $this->first('.cat-prod-row-foto', $this->first('.cat-prod-row', $list))->firstChild();
            $url  = self::$url.$item->href;
            $html = file_get_html($url);

            $featuresItems = $this->first('.product-features', $html);
            foreach ($featuresItems->find('.js_product-feature-row') as $featureItem) {
                $feature = new Feature();
                $feature->setName($this->first('.feature-name', $featureItem)->innertext);
                $feature->setPro($this->first('.js_vote-label-pro', $featureItem)->innertext);
                $feature->setCon($this->first('.js_vote-label-con', $featureItem)->innertext);
                $product->addFeature($feature);
            }

            $this->addCommentToProduct($product, Comment::BUY_CONFIRMED, $this->first('div[class=reviews]', $html)->find('ol[class=product-reviews]', 0));
            $this->addCommentToProduct($product, Comment::NOT_CONFIRMED, $this->first('div[class=reviews]', $html)->find('ol[class=product-reviews]', 1));
        } catch (MapperException $ex) {
            var_dump($ex);
        }

        return $product;
    }

    /**
     * @param Product $product
     * @param $confirmed
     * @param simple_html_dom $html
     */
    private function addCommentToProduct(Product $product, $confirmed, $html) {
        if ($html !== null) {
            foreach ($html->find('li[class=product-review]') as $reviewItem) {
                $author = 'anonim';
                $rate   = 0.0;
                $param  = $this->first('p[class=product-reviewer]', $reviewItem)->nodes;
                $time   = isset($param[3]) ? new DateTime($param[3]->datetime) : null;

                $rateItem = $this->first('dl[class=product-review-score]', $reviewItem);
                $rateItem = $this->first('dd', $rateItem)->nodes;
                if (isset($rateItem[2])) {
                    $rate = str_replace(',', '.', trim($rateItem[2]->innertext));
                }

                if (isset($param[0])) {
                    $author = trim($param[0]->innertext);
                    $author = preg_match('/(użytkownik anonimowy)/', $author) ? 'anonim' : substr($author, 20);
                }

                $comment = new Comment();
                $comment->setReview($this->first('.product-review-body', $reviewItem)->innertext);
                $comment->setConfirmed($confirmed);
                $comment->setAuthor($author);
                $comment->setCreatedAt($time);
                $comment->setRate((float) $rate);
                $product->addComment($comment);
            }
        }
    }

    /**
     * @param string $selector
     * @param simple_html_dom $html
     * @return simple_html_dom
     * @throws MapperException
     */
    private function first($selector, $html) {
        $item = $html->find($selector, 0);
        if (!$item) {
            throw new MapperException("Item not found.");
        }

        return $item;
    }
}