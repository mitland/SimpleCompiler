<?php

class NameTokenGadget extends BasicTokenGadget
{
	private $pattern = '/[a-zA-Z]/';
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