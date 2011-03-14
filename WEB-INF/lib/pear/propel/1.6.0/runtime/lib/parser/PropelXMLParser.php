<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * XML parser. Converts data between associative array and XML formats
 *
 * @author     Francois Zaninotto
 * @package    propel.runtime.parser
 */
class PropelXMLParser extends PropelParser
{

	/**
	 * Converts data from an associative array to XML.
	 *
	 * @param  array   $array Source data to convert
	 * @param  string  $rootElementName Name of the root element of the XML document
	 * @param  string  $charset Character set of the input data. Defaults to UTF-8.
	 *
	 * @return string Converted data, as an XML string
	 */
	public function fromArray($array, $rootElementName = 'data', $charset = null)
	{
		$xml = new DOMDocument('1.0', 'UTF-8');
		$xml->preserveWhiteSpace = false;
		$xml->formatOutput = true;
		$rootElement = $xml->createElement($rootElementName);
		$xml->appendChild($rootElement);
		$this->arrayToDOM($array, $rootElement, $charset);
		
		return $xml->saveXML();
	}

	/**
	 * Alias for PropelXMLParser::fromArray()
	 *
	 * @param  array   $array Source data to convert
	 * @param  string  $rootElementName Name of the root element of the XML document
	 * @param  string  $charset Character set of the input data. Defaults to UTF-8.
	 *
	 * @return string Converted data, as an XML string
	 */
	public function toXML($array, $rootElementName = 'data', $charset = null)
	{
		return $this->fromArray($array, $rootElementName, $charset);
	}

	protected function arrayToDOM($array, $rootElement, $charset = null)
	{
		foreach ($array as $key => $value) {
			$key = preg_replace('/[^a-z]/i', '', $key);
			$element = $rootElement->ownerDocument->createElement($key);
			if (is_array($value)) {
				if (!empty($value)) {
					$element = $this->arrayToDOM($value, $element, $charset);
				}
			} elseif (is_string($value)) {
				$charset = $charset ? $charset : 'utf-8';
				if (function_exists('iconv') && strcasecmp($charset, 'utf-8') !== 0 && strcasecmp($charset, 'utf8') !== 0) {
					$value = iconv($charset, 'UTF-8', $value);
				}
				$value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
				$child = $element->ownerDocument->createCDATASection($value);
				$element->appendChild($child);
			} else {
				$child = $element->ownerDocument->createTextNode($value);
				$element->appendChild($child);
			}
			$rootElement->appendChild($element);
		}

		return $rootElement;
	}
    
	/**
	 * Converts data from XML to an associative array.
	 *
	 * @param  string $data Source data to convert, as an XML string
	 * @return array Converted data
	 */
	public function toArray($data)
	{
		$doc = new DomDocument('1.0', 'UTF-8');
		$doc->loadXML($data);
		$element = $doc->documentElement;
		return $this->convertDOMElementToArray($element);
	}

	/**
	 * Alias for PropelXMLParser::toArray()
	 *
	 * @param  string $data Source data to convert, as an XML string
	 * @return array Converted data
	 */
	public function fromXML($data)
	{
		return $this->toArray($data);
	}
	
	protected function convertDOMElementToArray(DOMNode $data)
	{
		$array = array();
		$elementNames = array();
		foreach ($data->childNodes as $element) {
			if ($element->nodeType == XML_TEXT_NODE) {
				continue;
			}
			$name = $element->nodeName;
			if (isset($elementNames[$name])) {
				if (isset($array[$name])) {
					// change the first 'book' to 'book0'
					$array[$name . $elementNames[$name]] = $array[$name];
					unset($array[$name]);
				}
				$elementNames[$name] += 1;
				$index = $name . $elementNames[$name];
			} else {
				$index = $name;
				$elementNames[$name] = 0;
			}
			if ($element->hasChildNodes() && !$this->hasOnlyTextNodes($element)) {
				$array[$index] = $this->convertDOMElementToArray($element);
			} elseif ($element->hasChildNodes() && $element->firstChild->nodeType == XML_CDATA_SECTION_NODE) {
				$array[$index] = htmlspecialchars_decode($element->firstChild->textContent);
			} elseif (!$element->hasChildNodes()) {
				$array[$index] = null;
			} else {
				$array[$index] = $element->textContent;
			}
		}
		return $array;
	}

	protected function hasOnlyTextNodes(DomNode $node)
	{
		foreach ($node->childNodes as $childNode) {
			if ($childNode->nodeType != XML_CDATA_SECTION_NODE && $childNode->nodeType != XML_TEXT_NODE) {
				return false;
			}
		}
		return true;
	}
}
