<?php
class ParseXML {
	public $xmlfile;
	
	public function __construct($xmlfile) {
		$this->xmlfile=$xmlfile;
	}
	public function getArray($elements) {
		$arr=array();
		if($elements->hasAttributes()) { //将属性值放到第一[0]个
			foreach ($elements->attributes as $attr) {
				//$arr[$attr->nodeName] = iconv("UTF-8","GB2312",$attr->nodeValue);
				$arr[$attr->nodeName] = trim($attr->nodeValue);
			}
		}
		if($elements->hasChildNodes() && $elements->childNodes->length!=1) { //$elements->childNodes->length!=1 过滤 #text
			foreach($elements->childNodes as $node) {
							
				if($node->nodeType != XML_TEXT_NODE) {
					if($node->nodeName=="#cdata-section") {
						//return iconv("UTF-8","GB2312",$elements->nodeValue);
						return trim($elements->nodeValue);
					}	
					$arr[$node->nodeName][]=$this->getArray($node);
				}
			
			}
		}
		else {
		
			//return iconv("UTF-8","GB2312",$elements->nodeValue);
			return trim($elements->nodeValue);
		}
		return $arr;
	}
	/**
	*解析XML文件
	*将XML文件内容解析成数组
	*/
	
	public function parseXML() {
		$dom = new DOMDocument();
		$dom->load($this->xmlfile);
		$elements = $dom->documentElement;
		$arr=$this->getArray($elements);
		return $arr;
	}
	

}


/*
include "ParseXML.php";
$a=new ParseXML("filter.xml");
$arr=$a->parseXML();
print_r($arr);
*/

?>
