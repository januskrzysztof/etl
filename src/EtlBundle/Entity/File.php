<?php

namespace EtlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class File
 * @package EtlBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="files")
 */
class File implements ToArrayInterface {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=false)
     *
     * @var string
     */
    private $basePath;

    /**
     * @ORM\Column(type="text", nullable=false)
     *
     * @var string
     */
    private $filename;

    /**
     * @ORM\Column(length=5)
     *
     * @var string
     */
    private $ext;

    public function __construct($url) {
        $urlParts = explode('/', $url);
        $file     = $urlParts[count($urlParts)-1];
        $fileParts= explode('.', $file);
        $ext      = $fileParts[count($fileParts)-1];
        unset($fileParts[count($fileParts)-1]);
        $filename = implode('.', $fileParts);
        unset($urlParts[count($urlParts)-1]);
        $basePath = rtrim(implode('/', $urlParts), '/').'/';

        $this->basePath = $basePath;
        $this->filename = $filename;
        $this->ext      = $ext;
    }

    /**
     * @param string $basePath
     */
    public function setBasePath($basePath) {
        $this->basePath = $basePath;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename) {
        $this->filename = $filename;
    }

    /**
     * @param string $ext
     */
    public function setExt($ext) {
        $this->ext = $ext;
    }

    /**
     * @param string $prefix
     * @param string $suffix
     * @return string
     */
    public function getUri($prefix = '', $suffix = '') {
        return $this->basePath.$prefix.$this->filename.$suffix.'.'.$this->ext;
    }

    /**
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'base_path' => $this->basePath,
            'ext' => $this->ext,
            'uri' => $this->getUri()
        ];
    }
}