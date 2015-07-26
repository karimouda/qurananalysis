<?php

/**
 * Implements logic expression parser.
 *  @version	$Id: OWLExpression.php,v 1.2 2004/03/23 20:56:54 klangner Exp $
 */
class OWLExpression
{
	//---------------------------------------------------------------------------
	/**
	 * Constructor
	 * @param $exp expression to evaluate
	 */ 
	function OWLExpression($exp){
		
		$input_array = $this->string2array($exp);
		$this->rpn_array = $this->arrayToRpn($input_array);
		$this->prepareVariables();
	}


	//---------------------------------------------------------------------------
	/**
	 * get variables found in expression
	 */ 
	function getVariables(){
		return $this->variables;		
	}


	//---------------------------------------------------------------------------
	/**
	 * Evaluate exression
	 *
	 *  initialise stack to empty
	 *  while not end of postfix expression  {
	 *     get next postfix item
	 *     if(item is value) push it onto the stack
	 *     else if(item is binary operator)
	 *     {  
	 *  	 		 pop the stack to x;
	 *         pop the stack to y;
	 *         perform y operator x
	 *         push the results onto the stack   
	 *  	 }
	 *     else if (item is unary operator)
	 *     {  
	 *  			 pop the stack to x;
	 *         perform operator(x)
	 *         push the results onto the stack   
	 *  	 }
	 *  }
	 *  	 
	 *  The single value on the stack is the desired result.
	 *  	 
	 * @return Input expression as array
	 */
	function evaluate($input) {

		// Convert values to lower case
		$this->prepareValues($input);		
		$stack = array();

		for($i = 0; $i < count($this->rpn_array); $i++){
			
			$item = $this->rpn_array[$i];
			if($this->isValue($item)){
				array_push($stack, $item);
			}else if($this->isBinaryOperator($item)){

   			$x = array_pop($stack);
   			$y = array_pop($stack);
				$result = $this->performBinary($item, $x, $y);
				array_push($stack, $result); 
			}
	   	else if(!$this->isBinaryOperator($item)){

   			$x = array_pop($stack);
				$result = $this->performUnary($item, $x);
				array_push($stack, $result); 
			}
			else{
				echo "Problem with token: " . $item . "<br/>"; 
			}
		}

		//The single value on the stack is the desired result.
		return (bool)array_pop($stack);
	}
	
	
	//---------------------------------------------------------------------------
	/**
	 * Perform binary $operation on $x and $y
	 *
	 * @return value
	 */
	function performBinary($operator, $x, $y) {
		
		$patterns = array("/^['\"]/", "/['\"]$/");
		
		if(array_key_exists($x, $this->values)){
			$x = $this->values[$x];
		}
		else{
			// Remove enclosed ''' or '"'
			$x = preg_replace($patterns, "", $x);
		} 
		
		if(array_key_exists($y, $this->values)){
			$y = $this->values[$y];
		}
		else{
			// Remove enclosed ''' or '"'
			$y = preg_replace($patterns, "", $y);
		} 
		
		$fun = $this->operators[$operator][3];
		return $this->$fun($x, $y);
	}
	
	
	//---------------------------------------------------------------------------
	/**
	 * Perform unary $operation on $x
	 * currently on not is supported
	 *
	 * @return value
	 */
	function performUnary($operator, $x) {
		
		$ret = false;
		if($temp == "not"){
			if($x)
				$ret = 0;
			else
				$ret = 1;
		}
					
		return $ret;
	}
	
	
	//---------------------------------------------------------------------------
	/**
	 * Convert input expression into array
	 *
	 * @return Input expression as array
	 */
	function string2array($exp) {

		$output = array();
		$input = strtolower($exp);
		$value = null;
		$concat_mode = null;
		
		for($i = 0; $i < strlen($input); $i++){
			
			switch($input[$i]){
				
				case ' ':
					if($concat_mode != null){
						$value .= $input[$i];
					}
					else if($value != null){
						array_push($output, $value);
						$value = null;
					}
				break;
				 
				case '=':
				case '(':
				case ')':
					if($value != null){
						array_push($output, $value);
						$value = null;
					}
					array_push($output, $input[$i]);
				break;
				
				case '\'':
				case '\"':
					$value .= $input[$i];
					if($concat_mode == $input[$i])
						$concat_mode = null;
					else if($concat_mode == null)
						$concat_mode = $input[$i];
				break;
				 
				default:
					$value .= $input[$i];
				break;
			}
		}  
		
		if($value != null){
			array_push($output, $value);
			$value = null;
		}

		return $output;
	}


	//---------------------------------------------------------------------------
	/**
	 * Change input array into RPN array
	 *
   *	initialise stack and postfix output to empty
   *	while(not end of infix expression)   {
   *	   get next infix item
   *	   if(item is value) 
   *			   append item to postfix output
   *	   else if(item == ‘(‘) push item onto stack
   *	   else if(item == ‘)’)   {
   *	       pop stack to x
   *	       while(x != ‘(‘)  
   *	          append x to postfix output and pop stack to x
   *	   }
   *	   else    {
   *	       while(precedence(stack top) >= precedence(item))
   *	          pop stack to x and append x to postfix output
   *	       push item onto stack
   *	   }
   *	}
   *	
   *	while(stack not empty)    
   *	   pop stack to x and append x to postfix output		
   *		 
	 *
	 * @return array Array with RPN expression
	 * @access private
	 */
	function arrayToRpn($input) {

		$rpn = array();
		$stack = array();
		
    for($i = 0; $i < count($input); $i++) {

			$item = $input[$i];

			if($this->isValue($item)) {
      	array_push($rpn, $item);
      } else if ($item == '('){
      	array_push($stack, $item); 
			}
			else if($item == ')'){
   			$x = array_pop($stack);
   			while($x != '('){  
   				array_push($rpn, $x);
   				$x = array_pop($stack);
   			}
			}
			else{
				while(count($stack) > 0 && 
				$this->precedence($stack[count($stack)-1]) >= $this->precedence($item)){
   				$x = array_pop($stack);
   				array_push($rpn, $x);
				}
				
				array_push($stack, $item);
			}
    }

		while(count($stack) > 0){    
			$x = array_pop($stack);
			array_push($rpn, $x);
		}

    return $rpn;
	}
	
	
	//---------------------------------------------------------------------------
	/**
	 * Check if given token is value
	 */
	function isValue($token) {

		return !array_key_exists($token, $this->operators);
	}
	

	//---------------------------------------------------------------------------
	/**
	 * Check precedence
	 */
	function precedence($token) {
		
		return $this->operators[$token][2];
	}
	
	//---------------------------------------------------------------------------
	/**
	 * Check operator
	 */
	function isBinaryOperator($token) {
		
		return ($this->operators[$token][1] == 2);
	}
	

	//---------------------------------------------------------------------------
	/**
	 * Prepare variables
	 * Find variables in rpn array
	 */
	function prepareVariables() {
		
		$this->variables = array();
		
		foreach($this->rpn_array as $item){
			if($this->isValue($item) && !is_numeric($item) && 
				 strpos($item, "'") === false && strpos($item, "\"") === false) 
			{
				array_push($this->variables, $item);
			}
		}
	}
	

	//---------------------------------------------------------------------------
	/**
	 * Convert passed values to lower case
	 */
	function prepareValues($input) {
		
		$this->values = array();
		foreach($input as $key => $value){
			if(is_array($value)){
				for($i = 0; $i < count($value); $i++){
						$value[$i] = strtolower($value[$i]);
				}
				$this->values[strtolower($key)] = $value;
			}
			else{
				$this->values[strtolower($key)] = strtolower($value);
			}
		}
	}
	

	//---------------------------------------------------------------------------
	/**
	 * Evaluate equal
	 */
	function eval_equal($x, $y) {
	
		$ret = 0;
		
		if(is_array($x)){
			if(in_array($y, $x))
				$ret = 1;
		}
		else if(is_array($y)){
			if(in_array($x, $y))
				$ret = 1;
		}
		else{
			if($x == $y)
				$ret = 1;
		}
		
		return $ret;
	}
	
	
	//---------------------------------------------------------------------------
	/**
	 * Evaluate and
	 */
	function eval_and($x, $y) {
		
		if($x && $y)
			return 1;
		else
			return 0;
	}
	
	
	//---------------------------------------------------------------------------
	/**
	 * Evaluate or
	 */
	function eval_or($x, $y) {
		
		if($x || $y)
			return 1;
		else
			return 0;
	}
	
	
	//---------------------------------------------------------------------------
	// Private members
	var	$rpn_array;
	var $values;
	var $variables;
	
	/** Operators  name, number of parameters, precedence, function name*/
	var $operators = array (
		'('    => array ('left bracket', 0),
		')'    => array ('right bracket', 1),
    'and'    => array ('and', 2, 2, "eval_and"),
    'or'    => array ('or', 2, 2, "eval_or"),
    '='    => array ('equals', 2, 3, "eval_equal"),
    'not'    => array ('not', 1, 4),
	);
}
?>