<?php

interface TokenGadgetContract
{
	public function walkNext($char);
	public function needNext();
	public function setToken($token);
	public function getToken();
	public function stop();
	public function reset();
	public function haveClosingChar();
}