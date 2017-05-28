<?php

	ini_set('memory_limit', '512M');
	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	error_reporting(-1);


	$charset = "anagramas";
	$charsetFreqs = getFrequencyArray($charset);
	$charsetLen = strlen($charset);

	$dictionaryFull = file("lemario-general-del-espanol.txt", FILE_IGNORE_NEW_LINES);
	$dictionary = array();


	foreach ($dictionaryFull as $word) {
		list ($found, ) = containsAnagram($charsetFreqs, $charsetLen, $word);
		if ($found){
			$dictionary[] = $word;
		}
	}

	usort($dictionary, function($a, $b) {
    	return strlen($b) - strlen($a);
	});


	echo "    Full dict: " . (sizeof($dictionaryFull)) . " words\n";
	echo "Filtered dict: " . (sizeof($dictionary)) . " words\n";


	$anagramList = array();
	findAnagram($charset, $charsetFreqs, $charsetLen, "");



	function findAnagram($charset, $charsetFreqs, $charsetLen, $current) {

		global $dictionary, $anagramList;

		foreach($dictionary as $dictWord) {

			list ($found, $remaining) = containsAnagram($charsetFreqs, $charsetLen, $dictWord);

			if ($found) {
			
				$words = explode(" ", $current);

				if (sizeof($words)==1 || strlen($words[sizeof($words)-1]) >= strlen($dictWord)) {
			
					if ($remaining!=""){
						findAnagram($remaining, getFrequencyArray($remaining), strlen($remaining), $current . " " . $dictWord);
					} else {
						$words[] = $dictWord;
						array_shift($words);
						sort($words);
						$newAnagram =  implode(" ", $words);
						if (!isset($anagramList[$newAnagram])) {
							echo "$newAnagram\n";
							$anagramList[$newAnagram] = 1;
						}
					}
			
				}

			} 
		
		}
	}	



	function containsAnagram($charsetFreqs, $charsetLen, $dictWord) {
			
		if ($charsetLen < strlen($dictWord)) {
			return array(false, "");
		}

		for($f=0; $f<strlen($dictWord); $f++) {

			if (!isset($charsetFreqs[$dictWord[$f]])){
				return array(false, "");
			}

			$charsetFreqs[$dictWord[$f]]--;
			if ($charsetFreqs[$dictWord[$f]]==0) {
				unset($charsetFreqs[$dictWord[$f]]);
			}
		}

		$remaining = "";
		foreach ($charsetFreqs as $char => $repetitions) {
			$remaining .= str_repeat($char, $repetitions);
		}

		return array (true, $remaining);

	}



	function getFrequencyArray($w){

		for($f=0; $f<strlen($w);$f++) {
			@$fa[$w[$f]]++;
		}

		return $fa;

	}

