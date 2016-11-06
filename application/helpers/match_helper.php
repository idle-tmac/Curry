<?php
	date_default_timezone_set('UTC');
	function GetPartName($matchPattern, $part) {

		$matchPatternMap = {
			"",
			{ "",'上半场', '下半场', '加时赛一', '加时赛二'},
			{ "", '第一节', '第二节', '第三节', '第四节', '加时赛一', '加时赛二'},
			{ "", '第一节', '第二节', '第三节', '第四节', '第五节', '第六节'},
		}
		return $matchPatternMap[$matchPattern][$part];
	}
