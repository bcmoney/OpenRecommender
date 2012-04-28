<?php

class TorrentReader {

	static public function parse( $data, &$reader = null ) {
		$reader = new self($data);
		$reader->_parse();
		return $reader->output;
	}

	public $debug = false;
	protected $types = array();
	protected $dataLength = -1;
	public $input = '';
	public $output;
	public $iterations = -1;

	public function __construct( $data ) {
		$this->input = $data;
		$this->dataLength = strlen($this->input);
	}

	public function _parse() {

		$key = $content = null;

		$php = '';
		$keyval = 'root';

		$start = 0;
		$i = 0;

		while ( $start < $this->dataLength && $i < 99999 ) {

			if ( !is_numeric($start) ) {
				break;
			}

			$i++;

			// end
			if ( 'e' == substr($this->input, $start, 1) ) {
				$ct = array_pop($this->types);
				$php .= str_repeat("\t", count($this->types));
				$php .= '),'."\n";
				$key = true;
				if ( $this->debug ) {
					echo '<p><b>END CURRENT '.$ct.'</b></p>';
				}
				$start += 1;
				continue;
			}
			// dict
			else if ( 'd' == substr($this->input, $start, 1) ) {
				$php .= str_repeat("\t", count($this->types));
				$php .= ( $this->dict() ? "'".addslashes($keyval)."'".' => array(' : 'array(' )."\n";
				$key = true;
				if ( $this->debug ) {
					echo '<p><b>NEW DICTIONARY</b></p>';
				}
				$start += 1;
				$this->types[] = 'dict';
				continue;
			}
			// list
			else if ( 'l' == substr($this->input, $start, 1) ) {
				$php .= str_repeat("\t", count($this->types));
				$php .= ( $this->dict() ? "'".addslashes($keyval)."'".' => array(' : 'array(' )."\n";
				if ( $this->debug ) {
					echo '<p><b>NEW LIST</b></p>';
				}
				$start += 1;
				$this->types[] = 'list';
				continue;
			}

			// value = Integer
			if ( in_array(substr($this->input, $start, 1), array('i', 'f')) ) {
				$end = strpos($this->input, 'e', $start+1);
				$content = substr($this->input, $start+1, $end-$start-1);
				if ( !is_numeric($content) ) {
					break;
				}
				$content = (float)$content;
				$start = $end+1;
			}
			// value = String
			else {
				$lpos = strpos($this->input, ':', $start);
				$length = substr($this->input, $start, $lpos-$start);
				if ( !is_numeric($length) ) {
					break;
				}
				$start = $lpos+1;
				$content = substr($this->input, $start, $length);
				$start += $length;
			}

			if ( 'dict' != $this->ct() || !$key ) {
				$php .= str_repeat("\t", count($this->types));
				$php .= ( $this->dict() ? "'".$keyval."'".' => '."'".addslashes($content)."'" : "'".addslashes($content)."'" ).",\n";
			}

			if ( $this->debug ) {
				echo '<pre>';
				var_dump($content);
				echo '</pre>';
			}

			if ( 'dict' == $this->ct() ) {
				$keyval = $content;
				$key = !$key;
			}
		}

		$phpArray = false;
		@eval('$phpArray = array('.$php.');');
		if ( $phpArray && isset($phpArray[0]) ) {
			$this->output = $phpArray[0];

			$this->iterations = $i;

			return true;
		}

		return false;

	}

	protected function ct() {
		return !$this->types ? null : $this->types[count($this->types)-1];
	}

	protected function dict() {
		return 'dict' == $this->ct();
	}

}


