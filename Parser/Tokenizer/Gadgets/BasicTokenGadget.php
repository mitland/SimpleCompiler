<?php

require 'Contracts\TokenGadgetContract.php';

abstract class BasicTokenGadget implements TokenGadgetContract
{
	protected $token;
	protected $next = true;

	public function needNext()
	{
		return $this->next;
	}

	public function setToken($token)
	{
		$this->token = $token;
		return $this;
	}

	public function getToken()
	{
		return $this->token;
	}

	public function stop()
	{
		$this->next = false;
		return $this;
	}

	public function reset()
	{
		$this->next = true;
		$this->token = null;
		return $this;
	}

	public function haveClosingChar()
	{
		return false;
	}
}