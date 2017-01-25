<?php

class StringHandler
{
	protected $string;
	protected $pointer = 0;

	public function __construct($string)
	{
		$this->string = (string)$string;
	}

	public function getCharFromIndex($index)
	{
		return mb_substr($this->string, $index, 1, 'utf-8');
	}

	public function getNextChar()
	{
		$char = $this->getCharFromIndex($this->getPointer());
		$this->incrementPointer();
		return $char;
	}

	public function incrementPointer()
	{
		$this->pointer += 1;
		return $this;
	}

	public function decrementPointer()
	{
		$this->pointer -= 1;
		return $this;
	}

	public function getPointer()
	{
		return $this->pointer;
	}

	public function setPointerVlaue(int $value)
	{
		$this->pointer = $value;
		return $this;
	}

	public function haveNextChar()
	{
		return $this->pointer < mb_strlen($this->string);
	}
}