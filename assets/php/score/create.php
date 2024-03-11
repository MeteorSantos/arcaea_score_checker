<?php
class createImg{
	
	private $db; // 数据库
	private $songName; // 曲名
	private $imagePath; // 图片路径
	private $difficulty; // 难度
	private $diffLevel; // 难度等级
	private $mapDifficultyRating; // 定数
	private $score; // 分数
	private $note; // 物量
	private $ptt; // 计算定数
	private $b30Avg; // b30平均数
	
	// 检查是否加载GD库
	function __construct() {
		if(!extension_loaded("gd")) {
			die("检查是否加载GD库");
		}
	}
	
	// 数据库操作
	private function pdoMysql() {
		// 引入数据库配置文件
		require dirname(__FILE__,2)."/database/configuration.php";
		// 引入数据库操作类
		require dirname(__FILE__,2)."/database/databaseclass.php";
		// 实例化数据库操作类
		$this -> db = new databaseMysql($configuration);
	}
	
	// 上传分数
	public function dataCleaning() {
		// 连接数据库
		$this -> pdoMysql();
		// 查询id
		$idSql = "SELECT id FROM user_table WHERE `name` = '{$_COOKIE['arcaea_score_checker_uname']}';";
		$id = $this -> db -> readDb($idSql);
		$uid = $id['id'];
		// 查询定数表最高30条平均数
		$b30avgSql = "SELECT ROUND(AVG(`{$uid}`), 4) FROM (SELECT `{$uid}` FROM song_grade ORDER BY `{$uid}` DESC LIMIT 30) AS subquery;";
		$b30Avg = implode($this -> db -> readDb($b30avgSql));
		$this->b30Avg = $b30Avg;
		// 查询定数表最高40条
		$mdrSql = "SELECT `{$uid}`, `song` FROM song_grade ORDER BY `{$uid}` DESC LIMIT 40;";
		$mdrb40 = $this -> db -> readDb($mdrSql,false); // 返回多行
		// 整理定数表数据
		$newMdr = [];
		$ss = ""; // sql语句列名
		foreach ($mdrb40 as $mv) {
			$newMdr[$mv['song']] = $mv[$uid];
			$ss .= ($ss !== "") ? "," : "";
			$ss .= "`" . $mv['song'] . "`";
		}
		$ptt = array_values($newMdr);
		$this->ptt = $ptt;
		// 查询成绩表最高40条
		$scoreSql = "SELECT {$ss} FROM song_score WHERE `user_table_id` = '{$uid}';";
		$oScore = $this -> db -> readDb($scoreSql);
		$score = []; // 整合分数列表
		foreach (array_values($oScore) as $sv) {
			// 为分数添加分位符
			$sv = substr_replace($sv, "'", 2, 0);
			$sv = substr_replace($sv, "'", -3, 0);
			$score[] = $sv;
		}
		$this->score = $score;
		// 整合其余数据
		$songName = [];
		$difficultyAbb = [];
		$oNote = [];
		$oMapDifficultyRating = [];
		$oLevel = [];
		foreach (array_keys($newMdr) as $nmv) {
			$songName[] = substr((string)$nmv, 0, -14);
			$difficultyAbb[] = substr((string)$nmv, -14, 3);
			$oNote[] = substr((string)$nmv, -4, 4);
			$oMapDifficultyRating[] = substr((string)$nmv, -8, 4);
			$oLevel[] = substr((string)$nmv, -11, 3);
		}
		$this->songName = $songName;
		// 难度文字替换
		$difficulty = [];
		foreach ($difficultyAbb as $dv) {
			if ($dv === "BYD") {
				$difficulty[] = "Beyond";
			}else if ($dv === "FTR") {
				$difficulty[] = "Future";
			}else if ($dv === "PRS") {
				$difficulty[] = "Present";
			}else if ($dv === "PST") {
				$difficulty[] = "Past";
			}
		}
		$this->difficulty = $difficulty;
		// 等级去0,加括号
		$level = [];
		$mapDifficultyRating = [];
		$note = [];
		foreach ($oLevel as $lk => $lv) {
			$level[] = ltrim($lv, '0');
			$mapDifficultyRating[] = ltrim($oMapDifficultyRating[$lk], '0');
			$note[] = '( ' . $oNote[$lk] . ' )';
		}
		$this->mapDifficultyRating = $mapDifficultyRating;
		$this->note = $note;
		// 拼接难度等级
		$diffLevel = [];
		foreach ($difficulty as $dlk => $dlv) {
			$diffLevel[] = $dlv . ' ' . $level[$dlk];
		}
		$this->diffLevel = $diffLevel;
		// 查询匹配表数据,图片路径
		$matchSql = "SELECT {$ss} FROM package_match;";
		$package = array_values($this -> db -> readDb($matchSql));
		$imagePath = [];
		foreach ($package as $pk => $pv) {
			$imagePath[$pk] = dirname(__FILE__, 3) . "/img/song/" . $pv . "/" . $songName[$pk] . ".jpg";
		}
		$this->imagePath = $imagePath;
		
		$this -> createImage();
		
	}
	
	public function createImage() {
		// 定位数组
		$arr = range(1, 40);
		// 用户名
		$uname = $_COOKIE['arcaea_score_checker_uname'];
		// 背景
		$bgImagePath = dirname(__FILE__,3).'/img/simg.jpg';
		$image = imagecreatefromjpeg($bgImagePath);
		
		// 颜色
		$black = imagecolorallocate($image, 34, 34, 34);
		$white = imagecolorallocate($image, 240, 240, 240);
		$cBeyond = imagecolorallocate($image, 160, 64, 64);
		$cFuture = imagecolorallocate($image, 160, 64, 160);
		$cPresent = imagecolorallocate($image, 64, 160, 64);
		$cPast = imagecolorallocate($image, 64, 160, 160);
		
		// 字体
		$fontG = dirname(__FILE__,3).'/fonts/GeosansLight.ttf';
		$fontE = dirname(__FILE__,3).'/fonts/Exo-SemiBold.ttf';
		
		// 字体大小
		$fontSize8 = 8;
		$fontSize10 = 10;
		$fontSize13 = 13;
		$fontSize14 = 14;
		$fontSize24 = 24;
		$fontSize50 = 50;
		
		// 坐标
		$unameX = 1000 - (imagettfbbox($fontSize50, 0, $fontG, $uname)[2] - imagettfbbox($fontSize50, 0, $fontG, $uname)[0])/2;
		$unameY = 100; // 用户名
		$b30avgX = 1010;
		$b30avgY =182; // b30平均数
		$dateX = 1000;
		$dateY = 1920; // 日期
		$songNameX = [140,390,640,890,1140,140,390,640,890,1140,140,390,640,890,1140,140,390,640,890,1140,140,390,640,890,1140,140,390,640,890,1140,140,390,640,890,1140,140,390,640,890,1140];
		$songNameY = [330,330,330,330,330,505,505,505,505,505,680,680,680,680,680,855,855,855,855,855,1030,1030,1030,1030,1030,1205,1205,1205,1205,1205,1555,1555,1555,1555,1555,1730,1730,1730,1730,1730]; // 曲名
		$imagePathX = [40,290,540,790,1040,40,290,540,790,1040,40,290,540,790,1040,40,290,540,790,1040,40,290,540,790,1040,40,290,540,790,1040,40,290,540,790,1040,40,290,540,790,1040];
		$imagePathY = [345,345,345,345,345,520,520,520,520,520,695,695,695,695,695,870,870,870,870,870,1045,1045,1045,1045,1045,1220,1220,1220,1220,1220,1570,1570,1570,1570,1570,1745,1745,1745,1745,1745]; // 图片
		$colorBlockX = [145,395,645,895,1145,145,395,645,895,1145,145,395,645,895,1145,145,395,645,895,1145,145,395,645,895,1145,145,395,645,895,1145,145,395,645,895,1145,145,395,645,895,1145];
		$colorBlockY = [350,350,350,350,350,525,525,525,525,525,700,700,700,700,700,875,875,875,875,875,1050,1050,1050,1050,1050,1225,1225,1225,1225,1225,1575,1575,1575,1575,1575,1750,1750,1750,1750,1750]; // 颜色块
		$diffLevelX = [151,401,651,901,1151,151,401,651,901,1151,151,401,651,901,1151,151,401,651,901,1151,151,401,651,901,1151,151,401,651,901,1151,151,401,651,901,1151,151,401,651,901,1151];
		$diffLevelY = [363,363,363,363,363,538,538,538,538,538,713,713,713,713,713,888,888,888,888,888,1063,1063,1063,1063,1063,1238,1238,1238,1238,1238,1588,1588,1588,1588,1588,1763,1763,1763,1763,1763]; // 难度等级
		$mapDifficultyRatingX = [218,468,718,968,1218,218,468,718,968,1218,218,468,718,968,1218,218,468,718,968,1218,218,468,718,968,1218,218,468,718,968,1218,218,468,718,968,1218,218,468,718,968,1218];
		$mapDifficultyRatingY = [363,363,363,363,363,538,538,538,538,538,713,713,713,713,713,888,888,888,888,888,1063,1063,1063,1063,1063,1238,1238,1238,1238,1238,1588,1588,1588,1588,1588,1763,1763,1763,1763,1763]; // 定数
		$scoreX = [145,395,645,895,1145,145,395,645,895,1145,145,395,645,895,1145,145,395,645,895,1145,145,395,645,895,1145,145,395,645,895,1145,145,395,645,895,1145,145,395,645,895,1145];
		$scoreY = [390,390,390,390,390,565,565,565,565,565,740,740,740,740,740,915,915,915,915,915,1090,1090,1090,1090,1090,1265,1265,1265,1265,1265,1615,1615,1615,1615,1615,1790,1790,1790,1790,1790]; // 分数
		$noteX = [195,445,695,945,1195,195,445,695,945,1195,195,445,695,945,1195,195,445,695,945,1195,195,445,695,945,1195,195,445,695,945,1195,195,445,695,945,1195,195,445,695,945,1195];
		$noteY = [406,406,406,406,406,581,581,581,581,581,756,756,756,756,756,931,931,931,931,931,1106,1106,1106,1106,1106,1281,1281,1281,1281,1281,1631,1631,1631,1631,1631,1806,1806,1806,1806,1806]; // 物量
		$pttX = [195,445,695,945,1195,195,445,695,945,1195,195,445,695,945,1195,195,445,695,945,1195,195,445,695,945,1195,195,445,695,945,1195,195,445,695,945,1195,195,445,695,945,1195];
		$pttY = [432,432,432,432,432,607,607,607,607,607,782,782,782,782,782,957,957,957,957,957,1132,1132,1132,1132,1132,1307,1307,1307,1307,1307,1657,1657,1657,1657,1657,1832,1832,1832,1832,1832]; // 计算定数
		
		// 绘制
		// 用户名
		imagettftext($image, $fontSize50, 0, $unameX, $unameY, $white, $fontG, $uname);
		// b30平均数
		imagettftext($image, $fontSize24, 0, $b30avgX, $b30avgY, $white, $fontE, $this -> b30Avg);
		// 日期
		imagettftext($image, $fontSize24, 0, $dateX, $dateY, $white, $fontE, date('Y.m.d'));
		// 其他
		foreach ($arr as $Key => $Value) {
			// 居中动态赋值
			if (strlen($this -> songName[$Key]) > 30) {
				$lastSpace = strrpos(substr($this -> songName[$Key], 0, 28), ' ');
				if ($lastSpace !== false) {
					$this -> songName[$Key] = substr($this -> songName[$Key], 0, $lastSpace) . "\n" . substr($this -> songName[$Key], $lastSpace + 1);
				}
				$songNameX[$Key] = $songNameX[$Key] - 98;
				$songNameY[$Key] = $songNameY[$Key] - 13;
			} else {
				$songNameX[$Key] = $songNameX[$Key] - (imagettfbbox($fontSize13, 0, $fontE, $this -> songName[$Key])[2] - imagettfbbox($fontSize13, 0, $fontE, $this -> songName[$Key])[0])/2;
			}
			$noteX[$Key] = $noteX[$Key] - (imagettfbbox($fontSize8, 0, $fontE, $this -> note[$Key])[2] - imagettfbbox($fontSize8, 0, $fontE, $this -> note[$Key])[0])/2;
			$pttX[$Key] = $pttX[$Key] - (imagettfbbox($fontSize13, 0, $fontE, $this -> ptt[$Key])[2] - imagettfbbox($fontSize13, 0, $fontE, $this -> ptt[$Key])[0])/2;
			// 绘制
			// 曲名
			imagettftext($image, $fontSize13, 0, $songNameX[$Key], $songNameY[$Key], $black, $fontE, $this -> songName[$Key]);
			// 图片
			$songImageResource = imagecreatefromjpeg($this -> imagePath[$Key]);
			imagecopyresampled($image, $songImageResource, $imagePathX[$Key], $imagePathY[$Key], 0, 0, 100, 100, imagesx($songImageResource), imagesy($songImageResource));
			imagedestroy($songImageResource);
			// 颜色块
			if($this -> difficulty[$Key] == 'Beyond') {$rectColor = $cBeyond;}
			else if($this -> difficulty[$Key] == 'Future'){$rectColor = $cFuture;}
			else if($this -> difficulty[$Key] == 'Present'){$rectColor = $cPresent;}
			else if($this -> difficulty[$Key] == 'Past'){$rectColor = $cPast;}
			imagefilledarc($image, $colorBlockX[$Key] + 1, $colorBlockY[$Key] + 1, 1 * 2, 1 * 2, 180, 270, $rectColor, IMG_ARC_PIE);
			imagefilledarc($image, $colorBlockX[$Key] + 70 - 1, $colorBlockY[$Key] + 1, 1 * 2, 1 * 2, 270, 0, $rectColor, IMG_ARC_PIE);
			imagefilledarc($image, $colorBlockX[$Key] + 70 - 1, $colorBlockY[$Key] + 15 - 1, 1 * 2, 1 * 2, 0, 90, $rectColor, IMG_ARC_PIE);
			imagefilledarc($image, $colorBlockX[$Key] + 1, $colorBlockY[$Key] + 15 - 1, 1 * 2, 1 * 2, 90, 180, $rectColor, IMG_ARC_PIE);
			imagefilledrectangle($image, $colorBlockX[$Key] + 1, $colorBlockY[$Key], $colorBlockX[$Key] + 70 - 1, $colorBlockY[$Key] + 15, $rectColor);
			imagefilledrectangle($image, $colorBlockX[$Key], $colorBlockY[$Key] + 1, $colorBlockX[$Key] + 70, $colorBlockY[$Key] + 15 - 1, $rectColor);
			// 难度等级
			imagettftext($image, $fontSize10, 0, $diffLevelX[$Key], $diffLevelY[$Key], $white, $fontE, $this -> diffLevel[$Key]);
			// 定数
			imagettftext($image, $fontSize10, 0, $mapDifficultyRatingX[$Key], $mapDifficultyRatingY[$Key], $black, $fontE, $this -> mapDifficultyRating[$Key]);
			// 分数
			imagettftext($image, $fontSize14, 0, $scoreX[$Key], $scoreY[$Key], $black, $fontE, $this -> score[$Key]);
			// 物量
			imagettftext($image, $fontSize8, 0, $noteX[$Key], $noteY[$Key], $black, $fontE, $this -> note[$Key]);
			// 计算定数
			imagettftext($image, $fontSize13, 0, $pttX[$Key], $pttY[$Key], $black, $fontE, $this -> ptt[$Key]);
		}
		
		// 输出并销毁图片,提供下载标识
		$fileName = $uname . date('Y.m.d') . ".png";
		header('Content-Type: image/png');
		header("Content-Disposition: attachment; filename=\"$fileName\"");
		imagepng($image);
		imagedestroy($image);
	}
	
}

$obj_ci = new createImg();
$obj_ci -> dataCleaning();


?>