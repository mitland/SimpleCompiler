<?php

interface TokenContract
{
	public function getValue();
	public function setValue($value);
	public function getType();
	public function setType($type);
}
