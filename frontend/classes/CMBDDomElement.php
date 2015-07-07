<?php
class CMBDDomElement 
{	
	/**
	 * @var DOMDocument
	 */
	private $_dom;
	
	/**
	 * 
	 * @var DOMElement
	 */
	private $_element;
	
	public function __construct($name, $value) {
		$this->_dom = new DOMDocument('1.0', 'UTF-8');
		$this->_element = $this->_dom->createElement($name, $value);
	}	
	
	public function getHTML() {
		$this->_dom->appendChild($this->_element);
		
		return $this->_dom->saveHTML();
	}
	
	public function getLoadedHTML($html) {
		if (!empty($html)) {
			$this->_dom->loadHTML($html);
		}
		return $this->_dom->saveHTML();
	}
	
	public function addClass($class) {
		$this->_element->setAttribute('class', $class);
	}
}