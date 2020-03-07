<?php

function bdecode($file) {
  $output = shell_exec("./bencode.out " . $file);
  //echo $output . "\n\n";
  $torrentData = json_decode($output);
  //echo "\n\n";
  return $torrentData;
}

$file = "test.torrent";
$out = bdecode($file);
var_dump($out);


class Bencode {
  private const DICT_OPEN = 'd';
  private const LIST_OPEN = 'l';
  private const INT_OPEN = 'i';
  private const CLOSE = 'e';

  private const NO_SEPARATION = "";
  private const COMMA_SEPARATION = ",";
  private const COLON_SEPARATION = ":";

  public static function fileDecode($file) {
    if (!($str = file_get_contents($file))
      return false;

    return decode($str);
  }

  public static function decode($str) {
    if (!is_string($str))
      return false;

    $array_str = str_split($str);
    $stack = new SplDoublyLinkedList;
    $stack.push(array('stack' => CLOSE, 'key' => NULL));
    $res = NULL;

    while ($index >= count($array_str))
      if (!decodeElem(&$index, $array_str, &$res, &$stack))
        return false;

    return json_decode($res);
  }

  private static function separation(&$res, &$stack) {
    $res .= $separation;

    if ($stack.top()['state'] == NO_SEPARATION && $stack.top()['stack'] == DICT_OPEN)
      $stack.top()['state'] = COLON_SEPARATION;
    if ($stack.top()['state'] == CLON_SEPARATION && $stack.top()['stack'] == DICT_OPEN)
      $stack.top()['state'] = COMMA_SEPARATION;
    if ($stack.top()['state'] == COMMA_SEPARATION && $stack.top()['stack'] == DICT_OPEN)
      $stack.top()['state'] = COLON_SEPARATION;
    if ($stack.top()['state'] == NO_SEPARATION && $stack.top()['stack'] == LIST_OPEN)
      $stack.top()['state'] = COMMA_SEPARATION;
  }

  private static function decodeInt(&$index, $array_str)
  {
    if ($array_str[$index] != 'i')
      return false;

    $acc = "";
    $index++;
    while ($array_str[$index] != 'e')
    {
      if (!is_numeric($array_str[$index]))
        return false;
      if ($index >= count($array_str))
        return false;

      $acc .= $array_str[$index];
      $index++;
    }

    $index++;
    return intval($acc);
  }

  private static function decodeStr(&$index, $array_str)
  {
    if (!is_numeric($array_str[$index]))
      return false;

    $acc = "";
    while ($array_str[$index] != ':')
    {
      if (!is_numeric($array_str[$index]))
        return false;
      if ($index >= count($array_str))
        return false;

      $acc .= $array_str[$index];
      $index++;
    }
    $len = intval($acc);

    if ($index + $len >= count($array_str))
      return false;

    $acc = "";
    while ($len > 0) {
      $index++;
      if ($index >= count($array_str))
        return false;

      $acc .= $array_str[$index];
      $len--;
    }

    $index++;
    return $acc;
  }

  private static function decodeList(&$index, $array_str)
  {
    if ($array_str[$index] != 'l')
      return false;

    $res = array();
    $index++;
    while ($array_str[$index] != "e")
    {
      if ($index >= count($array_str))
        return false;

      if (($val = decodeInt(&$index, $array_str)) != false)
        $res[count($res)] = $val;
      else if (($val = decodeStr(&$index, $array_str)) != false)
        $res[count($res)] = $val;
      else if (($val = decodeList(&$index, $array_str)) != false)
        $res[count($res)] = $val;
      else if (($val = decodeDict(&$index, $array_str)) != false)
        $res[count($res)] = $val;
      else
        return false;
    }

    $index++;
    return $res;
  }

  private static function decodeDict(&$index, $array_str)
  {
    if ($array_str[$index] != 'd')
      return false;

    $res = array();
    $key = NULL;
    $index++;
    while ($array_str[$index] != "e")
    {
      if ($index >= count($array_str))
        return false;

      if (($val = decodeInt(&$index, $array_str)) != false)
        $key = $val;
      else if (($val = decodeStr(&$index, $array_str)) != false)
        $key = $val;
      else
        return false;

      if (($val = decodeInt(&$index, $array_str)) != false)
        $res[$key] = $val;
      else if (($val = decodeStr(&$index, $array_str)) != false)
        $res[$key] = $val;
      else if (($val = decodeList(&$index, $array_str)) != false)
        $res[$key] = $val;
      else if (($val = decodeDict(&$index, $array_str)) != false)
        $res[$key] = $val;
      else
        return false;
    }

    $index++;
    return $res;
  }

  private static function decodeElem(&$index, $array_str, &$res, &$stack) {
    if ($array_str[$index] != CLOSE)
      separation(&$res, &$stack);

    switch ($array_str[$index]) {
      case DICT_OPEN:
        $res = array();
        $stack.push(array('stack' => DICT_OPEN, 'key' => NULL));
        break;

      case LIST_OPEN:
        $res = array();
        $stack.push(array('stack' => LIST_OPEN, 'key' => NULL));
        break;

      case INT_OPEN:
        $index++;
        while ($array_str[$index] != 'e') {
          if (!is_numeric($array_str[$index]))
            return false;
          if ($index >= count($array_str))
            return false;
          $res .= $array_str[$index];
          $index++;
        }
        break;

      case CLOSE:
        $stade = $stack.pop();
        if ($stade['state'] == CLON_SEPARATION)
          return false;
        if ($stade['stack'] == DICT_OPEN)
          $res .= '}';
        else if ($stade['stack'] == LIST_OPEN)
          $res .= ']';
        else
          return false;
        break;

      default:
        $len = 0;
        while ($array_str[$index] != ':') {
          if (!is_numeric($array_str[$index]))
            return false;
          if ($index >= count($array_str))
            return false;
          $len *= 10;
          $len += intval($array_str[$index]);
          $index++;
        }
        if ($index + $len >= count($array_str))
          return false;
        while ($len > 0) {
          $index++;
          $res .= $array_str[$index];
          $len--;
        }
        break;
    }

    $index++;
    return true;
  }
}

?>
