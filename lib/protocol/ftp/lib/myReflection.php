<?php
/**
 * myReflection
 * This class is a customized version of ReflectionMethod class in Reflection API that faciliates capturing of
 * descriptions. 
 *
 * @author Amin Saeedi, <amin.w3dev@gmail.com>
 * @copyright Copyright (c) 2009, Amin Saeedi
 */
class myReflection extends ReflectionMethod
{
	public function getDescription(){
		$doc = $this->getDocComment();
		$doc = substr($doc, 6, strpos($doc, "@")-6);
		$doc = "<".trim(str_replace("*","",$doc)).">";
		return $doc;
	}
	
	public function getParamDesc(){
		$doc = $this->getDocComment();
		$doc = substr($doc, strpos($doc, "@"));
		$doc = str_replace("/","",$doc);
		preg_match_all('/\@param(.*)\n?/',$doc, $paramDoc);
		return $paramDoc;
	}
}
