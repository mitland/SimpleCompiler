<?php

require 'Contracts\TokenContract.php';

class Token implements TokenContract
{
	protected $type;
	protected $value;

	public function getValue()
	{
		return $this->value;
	}

	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}		
	
}