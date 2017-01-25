<?php

/**+----------------------------------------------------------------------+
   | Tokenizer                                                 			  |
   +----------------------------------------------------------------------+
   | Purpose: Lexical analyze of a string  								  |
   +----------------------------------------------------------------------+
   | The Tokenizer create a token for every recognized one, 			  |
   | it has registration methods for Tokens which give you the 		  |
   | chance to analyze every language and to prepare tokens for it 		  |
   | most important method is tokenize which do the actual work.  		  |
   | 			  														  |
   +----------------------------------------------------------------------+
 */
class Tokenizer
{
	/**
	 * Current Version of the Tokenizer
	 */
	const VERSION = '1.0';
	
	/**
	 * Array of Contracts\TokenGadgetContract
	 *  which take action about tokens, when they need more 
	 *  then one char from the string
	 * @var array
	 */
	protected $gadgets			= [];
	
	/**
	 * Array of Closures
	 *  which are an asociation for given character, and they 
	 *  hold a Token of a specific type for return stament
	 *  example: ( => function()use($type){ return new Token()->setType($type) }
	 * @var array
	 */
	protected $charTokens	 	= [];
	
	/**
	 * Array of Closures
	 *  which are an asociation for a given pattern/regex, and they 
	 *  hold a Token of a specific type for return stament
	 *  example: /[a-z]/ => function()use($type){ return new Token()->setType($type) }
	 * @var array
	 */
	protected $regexTokens	 	= [];

	/**
	 * Pattern which is used to remove unwonted characters, 
	 * before the search for tokens on given pattern or character get started
	 * @var string
	 */
	protected $skipCharsPattern	= '/[\s\t ]/';

	/**
	 * Register token for a given character
	 * 
	 * @param  string $char Char for which the token will take account
	 * @param  string $type Type of the token example [String, SpecialChar, Keyword]
	 * @return Tokenizer
	 */
	public function registerTokenForChar($char, $type)
	{
		return $this->registerToken($char, $type);
	}

	/**
	 * Register token for a given pattern
	 * 
	 * @param  string              				$pattern     regex or char for which the token will take account
	 * @param  string              				$type        Type of the token example [String, SpecialChar, Keyword]
	 * @param  Contracts\TokenGadgetContract 	$tokenGadget Gadget wich to be used for searching the next char
	 * @return Tokenizer                        
	 */
	public function registerTokenForPattern($pattern, $type, TokenGadgetContract $tokenGadget)
	{
		return $this->registerToken($pattern, $type, $tokenGadget, true);
	}

	/**
	 * Register token
	 * 
	 * @param  string                   		$pattern     regex or char for which the token will take account
	 * @param  string              				$type        Type of the token example [String, SpecialChar, Keyword]
	 * @param  Contracts\TokenGadgetContract 	$tokenGadget Gadget wich to be used for searching the next char
	 * @param  boolean                  		$regex       is the pattern regex
	 * @return Tokenizer  
	 */
	public function registerToken($pattern, $type, TokenGadgetContract $tokenGadget = null, $regex = false)
	{
		$tokenClosure = $this->createTokenClosure($type);

		if($regex)
		{
			$this->regexTokens[$pattern] = $tokenClosure;	
		}
		else
		{
			$this->charTokens[$pattern] = $tokenClosure;		
		}
		
		if(!is_null($tokenGadget))
		{
			$this->gadgets[$type] = $tokenGadget;	
		}
		
		return $this;	
	}

	/**
	 * Creates token closure which is to create token every time on call
	 * 
	 * @param  string $type Type of the token example [String, SpecialChar, Keyword]
	 * @return Closure
	 */
	private function createTokenClosure($type)
	{
		return function()use($type){
			return (new Token())->setType($type);
		};		
	}

	/**
	 * Searching for a token gadget
	 * 
	 * @param  string $type    Type of the token example [String, SpecialChar, Keyword]
	 * @param  mixed  $default Default value to return if not found any
	 * @return mixed | Contracts\TokenGadgetContract
	 */
	public function getTokenGadget($type, $default = null)
	{
		return isset($this->gadgets[$type]) ? $this->gadgets[$type] : $default;
	}

	/**
	 * Searching for a token by char
	 * 
	 * @param  string $char 	
	 * @return Token | Exception
	 */
	public function findTokenForChar($char)
	{
		if(isset($this->charTokens[$char]))
		{
			return $this->charTokens[$char]();
		}

		//@TODO Make events
		//$this->dispatcher->fire('token.found', $token);

		foreach ($this->regexTokens as $pattern => $tokenCreator) 
		{
			if(preg_match($pattern, $char))
			{
				return $tokenCreator();
			}
		}

		throw new Exception("Error Processing Request, FREAK OUT, NO SUCH TOKEN FOR pattern: " . $char, 1);
	}

	/**
	 * Tokenize given string
	 * 
	 * @param  string $string String which to be tokenized
	 * @return Array of Tokens
	 */
	public function tokenize($string)
	{
		$tokens = [];
		$stringHandler = $this->getStringHandler($string);

		while($stringHandler->haveNextChar())
		{
			$char = $stringHandler->getNextChar();

			if($this->isCharUnwonted()))
			{
				continue;
			}
			
			$token = $this->findTokenForChar($char)->setValue($char);
			
			$gadget = $this->getTokenGadget($token->getType());
			
			if($gadget)
			{
				$gadget->reset()->setToken($token);
								
				while($gadget->needNext())
				{
					$gadget->walkNext($stringHandler->getNextChar());
				}
				
				if(!$gadget->haveClosingChar())
				{
					$stringHandler->decrementPointer();
				}

				$token = $gadget->getToken();
			}
			
			$tokens[] = $token;
		}

		return $tokens;
	}


	protected function isCharUnwonted($char)
	{
		return preg_match($this->skipCharsPattern, $char);
	}

	/**
	 * Get StringHandler
	 * 
	 * @param  string $string 
	 * @return StringHandler
	 */
	private function getStringHandler($string)
	{
		return new StringHandler($string);
	}

	/**
	 * Get current version of the Tokenizer
	 * @return string
	 */
	public function getVersion()
	{
		return slef::VERSION;
	}
}