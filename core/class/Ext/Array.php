<?php
/**
 * 数组处理扩展
 * @author 许仙 <QQ:1216560669 >
 * @time 15:45 2010-10-28
 * @version 1.0
 */
class Ext_Array {	
	
	public static function map ($array, $fun) {
		foreach ($array as $key =>$item) {
			$array[$key] = is_array ($item) ? self::map ($item, $fun) : call_user_func($fun, $item);
		}
		return $array;
	}

	
	public static function format ($array, $keyField = null, $valueField = null) {
		$newArray = array ();
		foreach ($array as $key => $value) {
			$index = !is_null ($keyField) ? $value[$keyField] : $key;
			if (is_null ($valueField)) {
				$newArray[$index] = $value;
			}
			elseif (is_array ($valueField)) {
				reset ($valueField);
				foreach ($valueField as $valueKey => $valueItem) {
					$newArray[$index][$valueItem] = $value[$valueItem];
				}
			}
			else {
				$newArray[$index] = $value[$valueField];
			}
		}
		return $newArray;
	}

	
	public static function rand($array) {
		$index = array_rand ($array);
		return $array[$index];
	}
	
	
	public static function randMulti($array, $count = 1) {
		$count = min($count, count($array));
		$index = array_rand ($array, $count);
		if (is_array($index)) {
			foreach ($index as $i) {
				$ret[] = $array[$i];
			}
		}
		else {
			$ret = array($array[$index]);
		}
		return $ret;
	}
	
	
	public static function sort ($array, $keyFields = 0, $sortTypes = 'asc') {
		// $sortType —— 'asc': 升序 'desc': 降序

		$valueArray = array ();
		$newArray = array ();

		$keyField = is_array ($keyFields) ? current ($keyFields) : $keyFields;
		$sortType = is_array ($sortTypes) ? current ($sortTypes) : $sortTypes;

		foreach ($array as $key => $item) {
			$valueArray[$key] = $item[$keyField];
		}

		$sortFunc = strtolower ($sortType) == 'desc' ? 'arsort' : 'asort';
		$sortFunc ($valueArray);

		$lastItem = null;
		$i = 0;
		foreach ($valueArray as  $key => $item) {
			if (!is_null ($lastItem) && $array[$key][$keyField] != $lastItem[$keyField]) $i ++;
			$newArray[$i][$key] = $array[$key];
			$lastItem = $array[$key];
		}

		if (array_shift ($keyFields)) {
			array_shift ($sortTypes);
			foreach ($newArray as  $key => $item) {
				if (count ($item) > 1) {
					$newArray[$key] = self::sort ($item, $keyFields, $sortTypes);
				}
			}
			reset ($newArray);
		}
		
		$retArray = array ();
		foreach ($newArray as $key => $item) { 
			foreach ($item as  $sKey => $sItem) {
				$retArray[$sKey] = $array[$sKey];
			}
		}
		return $retArray;
	}


	
	public static function reindex ($array) {
		$newArray = array ();
		foreach ($array as $key => $item) {
			$newArray[] = $item;
		}
		return $newArray;
	}

	
	public static function join ($char, $array) { 
		foreach ($array as $key => $item) {
			if (strval ($item) == '') {
				unset ($array[$key]);
			}
		}
		$str = join ($char, $array);
		return $str;
	}

	
	public static function filter ($array, $arrayKeys) {
		foreach ($array as $key => $item) {
			if (!in_array ($key, $arrayKeys)) {
				unset ($array[$key]);
			}
		}
		return $array;
	}

	
	public static function inArray ($array, $string, $splitStr = ',') {
		if (!is_array ($array)) {
			$array = explode ($splitStr, $array);
		}
		return (in_array ($string, $array));
	}


	
	public static function cols ($array, $keyField) {
		$array_cols = array ();
		foreach ($array as $key => $item) {
			$array_cols[] = $item[$keyField];
		}
		return $array_cols;
	}

	
	public static function serialToArray ($strSerial, $strSplitMain = '|', $strSplitSub = ':') {
		$arrResult = array ();
		if ($strSerial) {
			$arrRand = explode ($strSplitMain, $strSerial);
			foreach ($arrRand as $key => $item) {
				$arrItem = explode ($strSplitSub, $item);
				$arrItem[0] = str_replace (array ("\n", "\r"), '', $arrItem[0]);
				$arrResult[$arrItem[0]] = $arrItem[1];
			}
		}
		return $arrResult;
	}

	
	public static function arrayToSerial ($array, $strSplitMain = '|', $strSplitSub = ':') {
		foreach ($array as  $key=>$item) {
			$array[$key] = $key . $strSplitSub . $item;
		} 
		$strSerial = join ($strSplitMain, $array);
		return $strSerial;
	}
	
	
	public static function objectToArray($obj) {
		if (is_object($obj)) {
			$obj = (array) $obj;
		}	
		if (is_array($obj)) {
			$obj = Ext_Array::map($obj, 'Ext_Array::objectToArray');	
		}
		return $obj;
	}
	
	
	
	public static function multiArraySort($arr, $keys, $type = "asc") {
		if (!is_array($arr)) {
			return false;
	    }
	    $keysvalue = array();
	    foreach ($arr as $key => $val) {
			$keysvalue[$key] = $val[$keys];
	    }
	    if ($type == "asc") {
			asort($keysvalue);
	    }
	    else {
		    arsort($keysvalue);
	    }
	    reset($keysvalue);
	    foreach ($keysvalue as $key => $vals) {
			$keysort[$key] = $key;
	    }
	    $new_array = array();
	    foreach ($keysort as $key => $val) {
			$new_array[$key] = $arr[$val];
	    }
	    return $new_array;
    }


	
	public static function trimEmpty ($array) {
		foreach ($array as  $key=>$item) {
			if (is_array ($item)) {
				$array[$key] = self::trimEmpty($item);
			}
			elseif (!$item) {
				unset ($array[$key]);
			}
		}
		reset ($array);
		return $array;
	}


	
	public static function randByProbability ($array, $mul = 1000) {
		$max = array_sum ($array) * $mul;
		$rand = round (mt_rand (0, $max));
		$next = 0;
		$last = 0;
		foreach ($array as $key => $val) {
			$val = $val * $mul;
			$next += $val; 
			if ($rand >= $last && $rand <= $next) {
				$res = $key;
				break;	
			}
			$last = $next;
		}
		return $res;
	}
}
