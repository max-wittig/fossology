<?php


namespace www\ui\api\models;


use Symfony\Component\Config\Definition\Exception\Exception;

class Search
{
  private $limit;
  private $filename;
  private $tag;
  private $filesize_min;
  private $filesize_max;
  private $license;
  private $copyright;

  /**
   * Search constructor.
   * @param $limit
   * @param $filename
   * @param $tag
   * @param $filesize_min
   * @param $filesize_max
   * @param $license
   * @param $copyright
   */
  public function __construct($limit, $filename, $tag, $filesize_min, $filesize_max, $license, $copyright)
  {
    $this->limit = $limit;
    $this->filename = $filename;
    $this->tag = $tag;
    $this->filesize_min = $filesize_min;
    $this->filesize_max = $filesize_max;
    $this->license = $license;
    $this->copyright = $copyright;
  }

  public function getNotNullProperties()
  {
    $returnArray = [];
    $reflection = new \ReflectionClass($this);
    $class_vars = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
    foreach ($class_vars as $var)
    {
      $var->setAccessible(true);
      if($var->getValue($this) != NULL)
      {
        $returnArray[] = $var->getName();
      }
    }

    return $returnArray;
  }

}
