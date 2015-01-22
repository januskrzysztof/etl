<?php

namespace EtlBundle\Logic\Mappers\Ceneo;

use EtlBundle\Entity\File;
use EtlBundle\Entity\Product\Comment;
use EtlBundle\Entity\Product\Comment\Review;
use EtlBundle\Entity\Product\Feature;
use EtlBundle\Entity\Product;
use EtlBundle\Logic\Mappers\MapperException;
use EtlBundle\Logic\Mappers\MapperParserInterface;
use InvalidArgumentException;
use DateTime;
use simple_html_dom;
use simple_html_dom_node;

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
        $this->item = $item;
    }

    /**
     * @return Product
     */
    public function parse() {
        $item = preg_replace('/\\s/', '+', $this->item);
        $url  = self::$url . "/;szukaj-{$item}";
        $html = file_get_html($url);
        $list = $this->first('.category-list-body', $html);

        $product = new Product();
        $product->setName($this->item);
        try {
            /** @var simple_html_dom $item */
            $item = $this->first('.cat-prod-row-foto', $this->first('.cat-prod-row', $list))->firstChild();
            $url  = self::$url.$item->href;
            $html = file_get_html($url);

            $product->setImage(new File($item->firstChild()->attr['src']));

            $featuresItems = $this->first('.product-features', $html);
            foreach ($featuresItems->find('.js_product-feature-row') as $featureItem) {
                $feature = new Feature();
                $feature->setName($this->first('.feature-name', $featureItem)->innertext);
                $feature->setAdvantages($this->first('.js_vote-label-pro', $featureItem)->innertext);
                $feature->setDisadvantages($this->first('.js_vote-label-con', $featureItem)->innertext);
                $product->addFeature($feature);
            }

            $this->addCommentToProduct($product, Comment::BUY_CONFIRMED, $this->first('div[class=reviews]', $html)->find('ol[class=product-reviews]', 0));
            $this->addCommentToProduct($product, Comment::NOT_CONFIRMED, $this->first('div[class=reviews]', $html)->find('ol[class=product-reviews]', 1));
            $this->getNextComments($product, $html);
        } catch (MapperException $ex) {
            var_dump($ex);
        }

        return $product;
    }

    /**
     * @param Product $product
     * @param simple_html_dom $html
     */
    private function getNextComments(Product $product, $html, $counter = 0) {
        $nextItem = $html->find('li[class=arrow-next]', 0);
        if ($nextItem !== null) {
            $href = $nextItem->firstChild()->attr['href'];
            $url  = self::$url.$href;
            $html = file_get_html($url);

            $this->addCommentToProduct($product, Comment::NOT_CONFIRMED, $html->find('ol[class=product-reviews]', 0));
            $counter++;
            if ($counter > 1) {
                return;
            }

            $this->getNextComments($product, $html, $counter);
        }
    }

    /**
     * @param Product $product
     * @param $confirmed
     * @param simple_html_dom $html
     */
    private function addCommentToProduct(Product $product, $confirmed, $html) {
        if ($html !== null) {
            foreach ($html->find('li[class=product-review]') as $commentItem) {
                $author = 'anonim';
                $rate   = 0.0;
                $param  = $this->first('p[class=product-reviewer]', $commentItem)->nodes;
                $time   = isset($param[3]) ? new DateTime($param[3]->datetime) : null;

                $rateItem = $this->first('dl[class=product-review-score]', $commentItem);
                $rateItem = $this->first('dd', $rateItem)->nodes;
                if (isset($rateItem[2])) {
                    $rate = str_replace(',', '.', trim($rateItem[2]->innertext));
                }
                if (isset($param[0])) {
                    $author = trim($param[0]->innertext);
                    $author = preg_match('/(użytkownik anonimowy)/', $author) ? 'anonim' : substr($author, 20);
                }


                $usefulItem = $this->first('span[class=product-review-usefulness-stats]', $commentItem);
                $useful = $usefulItem->firstChild()->innertext;

                $comment = new Comment();
                $comment->setComment($this->first('.product-review-body', $commentItem)->innertext);
                $comment->setConfirmed($confirmed);
                $comment->setAuthor($author);
                $comment->setCreatedAt($time);
                $comment->setRate((float) $rate);
                $comment->setUseful((int) $useful);
                $product->addComment($comment);

                $reviewItem = $this->first('dl[class=product-review-pros-cons]', $commentItem);

                if (count($reviewItem->children) === 4) {
                    $this->addCommentReviews($comment, Review::TYPE_ADVANTAGE, $reviewItem->find('dd', 0));
                    $this->addCommentReviews($comment, Review::TYPE_DISADVANTAGE, $reviewItem->find('dd', 1));
                } else if (count($reviewItem->children) === 2) {
                    $item = $this->first('dt', $reviewItem);
                    if (isset($item->attr['class'])) {
                        $class = $item->attr['class'];
                        if ($class == 'pros') {
                            $this->addCommentReviews($comment, Review::TYPE_ADVANTAGE, $reviewItem->find('dd', 0));
                        } else if ($class == 'cons') {
                            $this->addCommentReviews($comment, Review::TYPE_DISADVANTAGE, $reviewItem->find('dd', 0));
                        }
                    }
                }
            }
        }
    }

    /**
     * @param Comment $comment
     * @param simple_html_dom_node $html
     */
    private function addCommentReviews(Comment $comment, $type, simple_html_dom_node $html = null) {
        if ($html !== null) {
            foreach ((array)$html->find('li') as $value) {
                $review = new Review();
                $review->setType($type);
                $review->setReview($value->innertext);
                $comment->addReview($review);
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