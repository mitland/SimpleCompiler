<?php

class NumberTokenGadget extends BasicTokenGadget
{
	private $pattern = '/[0-9]/';
	public function walkNext($char)
	{
		if(!preg_match($this->pattern, $char))
		{
			return $this->stop();
		}
		
		$this->token->setValue($this->token->getValue() . $char);
		
		return $this;
	}

}