<?php
/*
EDIFACT Messages Encoder
(c)2014 Stefano Sabatini

INPUT
	$c=new Encoder(X,[Y]);
		X is a multidimensional array where first dimension are edi segment, second are elements:
			- single value
			- array (representing composite elements)
		Y is a boolean, if you need a segment per line set to false to disable wrapping
	or
	$c=new Encoder();
	followed by $c->encode($array,$wrap)

OUTPUT
	String  $c->get()
*/

namespace EDI;

class Encoder
{
	private $output='';

	public function __construct($array=null,$wrap=true)
	{
		if($array===null) return;
		$this->output=$this->encode($array,$wrap);
	}

	function encode($arr,$wrap=true)
	{
		$edistring='';
		$count=count($arr);
		$k=0;
		foreach ($arr as $row)
		{
			$str='';
			$k++;
			$t=count($row);
			for ($i=0;$i<$t;$i++)
			{
				$elm='';
				if(!is_array($row[$i]))
					$elm=$this->escapeValue($row[$i]);
				else
				{
					foreach ($row[$i] as &$temp)
						$temp=$this->escapeValue($temp);
					$elm=implode(":",$row[$i]);
				}
				$str.=$elm;
				if($i==$t-1) break;
				$str.="+";
			}
			$str.="'";
			$edistring.=$str;
			if(!$wrap&&$k<$count)
				$edistring.="\n";
		}
		return $edistring;
	}

	function escapeValue($str)
	{
		$str = preg_replace('/(\'|\\+|:|\\?)/', '?$1', $str, -1);
		return $str;
	}

	function get()
	{
		return $this->output;
	}
}
?>
