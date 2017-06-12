<?php


namespace www\ui\api\helper;


class StringHelper
{
  /**
   * Removes lines from string
   * @param $lineNumbersToRemove
   * @param $wholeString
   * @return string
   */
  function removeLines($lineNumbersToRemove, $wholeString)
  {
    $splitString = explode("\n", $wholeString);
    for($i=0; $i < sizeof($splitString); $i++)
    {
      if(in_array($i, $lineNumbersToRemove))
      {
        unset($splitString[$i]);
      }
    }
    return implode("\n",$splitString);
  }

  /**
   * @param $wholeString - the string that needs to be cut
   * @param $numbersToRemove - numbers to remove from string
   * @param $outerLowerString - the string on the bottom of the file
   * @return string - content
   */
  function getContentBetweenString($wholeString, $numbersToRemove, $outerLowerString)
  {
    //remove numbers in array from string
    $cutString = $this->removeLines($numbersToRemove, $wholeString);
    $contentString = substr($cutString, 0, strpos($cutString, $outerLowerString));
    return $contentString;
  }
}
