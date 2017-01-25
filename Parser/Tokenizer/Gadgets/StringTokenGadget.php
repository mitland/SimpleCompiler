<?php

class StringTokenGadget extends BasicTokenGadget
{
	protected $stopChar = '"';

	public function walkNext($char)
	{
		if($char == $this->stopChar)
		{
			return $this->stop();
		}
		
		$this->token->setValue($this->token->getValue() . $char);
		return $this;
	}

	public function stop()
	{
		parent::stop();
		$this->onStop();
		return $this;
	}

	protected function onStop()
	{
		$this->token->setValue(substr($this->token->getValue(),1));
		return $this;
	}

	public function haveClosingChar()
	{
		return true;
	}
}