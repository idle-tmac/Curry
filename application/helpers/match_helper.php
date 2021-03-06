<?php
	date_default_timezone_set('PRC');
	function GetPartName($matchPattern, $part) {

		$matchPatternMap = array(
			"",
			[ "",'上半场', '下半场', '加时赛一', '加时赛二'],
			[ "", '第一节', '第二节', '第三节', '第四节', '加时赛一', '加时赛二'],
			[ "", '第一节', '第二节', '第三节', '第四节', '第五节', '第六节'],
		);
		return $matchPatternMap[$matchPattern][$part];
	}
  	
	function GetSomeStatistic($aStatistic, $item) {
		$cnt = 0;
		foreach($aStatistic as $val) {
			$cnt = $cnt + $val[$item];
		}
		return $cnt;
	}
