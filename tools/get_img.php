<?php
set_time_limit(0);

$rtime 		= microtime();
$rtime 		= explode(" ",$rtime);
$rtime 		= $rtime[1] + $rtime[0];
$start_rb 	= $rtime;
if( !defined('ROOT_PATH2') ){
	define('ROOT_PATH2', str_replace(array('/core'),array(''),strtr(dirname( __FILE__ ) ."/",array('\\'=>'/'))));
}
$path = str_replace('/tools', '', ROOT_PATH2);
require_once $path.'core/Debug.php'; //System Debug...
require_once $path.'config/config.php';//System Config...
require_once $path.'core/Init.php';  //System Init...

define('STORY_FOLDER', 'story/');
define('XXX_FOLDER', 'sex/');

$sieutoc_url = isset($_POST['upsieutoc_url']) ? $_POST['upsieutoc_url'] : '';
if($sieutoc_url != ''){
	CRAWLER::upsieutoc($sieutoc_url);
}

?>

<html>
<head>
	<title>Get Images</title>
</head>
<body>
	<div>
		<form action="?type=upsieutoc" method="POST" name="upsieutoc">
			<fieldset style="float:left">
				<legend>UpSieuToc.com</legend>
				<table>
					<tr>
						<td>URL</td>
						<td><input type="text" value="<?=$sieutoc_url?>" name="upsieutoc_url" id="upsieutoc_url" onfocus="this.select()" placeholder="http://xxx.com/yyy/zzz" size="60" /></td>
						<td><input type="submit" name="upsieutoc_submit" value="DO IT" onclick="return document.getElementById('upsieutoc_url').value != ''" /></td></tr>
				</table>
			</fieldset>
		</form>
		<div style="clear:both"></div>
	</div>
</body>
</html>

<?php

class CRAWLER{
	static function download($img = '', $path = ''){
		return file_put_contents($path, file_get_contents($img));
	}
	
	static function upsieutoc($url = ''){
		//define
		$recperpage = 24;

		$content = file_get_contents($url);

		//getname
		$name = '';
		$pattern = '/(<input class="search one-icon-padding" type="text" placeholder="[^"]+)/i';
		preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
		if(!empty($matches)){
			$name = str_replace('<input class="search one-icon-padding" type="text" placeholder="', '', $matches[0][0][0]);
		}
		$name = StringLib::stripUnicode($name);

		//get total page
		$total = 0;
		$pages = 1;
		$pattern = '/image-count">[^<]+/i';
		preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
		if(!empty($matches)){
			$total = intval(str_replace('image-count">', '', $matches[0][0][0]));
		}
		$pages = ceil($total/$recperpage);

		//start get images
		$url = pathinfo ($url);
		$url = $url['dirname'].'/?list=images&sort=date_desc&page=';
		
		//create dir by chapter
		$dir = ROOT_PATH.IMAGE_PATH.XXX_FOLDER.$name;
		FileHandler::CheckDir($dir);

		$counter = 0;
		for($i=1;$i<=$pages;$i++){
			$content = file_get_contents($url.$i);
			$pattern = '/src\="http:\/\/sv[0-9].upsieutoc.com[\._a-zA-Z0-9-\/\%]+/i';
			preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
			if(!empty($matches) && !empty($matches[0])){
				//save image to local
				foreach($matches[0] as $img){
					$img = str_replace(array('src="', '.md'), array('',''), $img[0]);
					//write file
					self::download($img, $dir.'/'.$counter.'.'.FileHandler::getExtension($img,'jpg'));
		
					//incre counter
					$counter++;
				}
			}
		}
		echo '<h2>DONE</h2>';
	}
	
	// -------------------  UPTRUYEN.COM -------------------------
	static function truyentranhonline(){
		//need change here
		$image_path = "http:\/\/truyentranhonline.vn\/wp-content\/uploads";
		$image_path2 = "http:\/\/[0-9].bp.blogspot.com";
	
		$story_name = 'LionKing';
		$link = "http://truyentranhonline.vn/vua-su-tu";
		$aKeys = array(
			//tap 1
			158 => 39, //1
			159 => 22, //2
			161 => 18, //3
			162 => 24, //4
			163 => 23, //5
			164 => 41, //6
			
			//tap 2
			23902 => 21, //1
			24561 => 17, //2
			25959 => 29,
			27866 => 18,
			28392 => 17,
			28840 => 20,
			29048 => 23,
			30025 => 10,
			32119 => 23,
			33627 => 18,
			33881 => 15,
			
			//tap3
			41299 => 21,
			48241 => 14,
			49591 => 18,
			50151 => 19,
			51126 => 21,
			54183 => 20,
			54316 => 18,
			55908 => 21,
			57230 => 23
		);
		
		$chapterMap = array(
			1 => 6,
			2 => 11,
			3 => 9
		);
		
		//dont touch
		$chapter = 3;
		$image_counter = 0;
		$chapCounter = 1;
		$counter = 152;
		foreach($aKeys as $id => $pages){
			if($chapter >=2){
				$image_path = $image_path2;
			}
			echo '<p>Getting Chapter '.$chapter.'...</p>';
			//create dir by chapter
			$dir = ROOT_PATH.IMAGE_PATH.STORY_FOLDER.$story_name.'/'.$chapter;
			FileHandler::CheckDir($dir);
	
			for($i=1;$i<=$pages;$i++){
				$l = $link.'/?id='.$id.'&gp='.$i;
				$file = file_get_contents($l);
				$pattern = '/' . $image_path . '[\._a-zA-Z0-9-\/\%]+/i';
				preg_match_all($pattern, $file, $matches, PREG_OFFSET_CAPTURE);
				if(!empty($matches)){
					//write file
					self::download($matches[0][0][0], $dir.'/'.$counter.'.'.FileHandler::getExtension($matches[0][0][0],'jpg'));
		
					//incre counter
					$counter++;
					$image_counter++;
		
					//debug
					echo ($counter-1).' -> ';
				}
			}
			if($chapCounter >= $chapterMap[$chapter]){
				$counter = 0;
				$chapter++;
				$chapCounter = 0;
			}
			$chapCounter++;
		}
		
		echo "<hr><h1>DONE!!! Total <font color='red'>$image_counter</font> images</h1>";
	}


	// -------------------  UPTRUYEN.COM -------------------------
	static function uptruyen(){
		//need change here
		$image_path = "http:\/\/uptruyen.com\/stream\/cloud_link";
		$story_name = 'LionKing';
		$link = array(
			"http://uptruyen.com/manga/189534/vua-su-tu/vua-su-tu-chap-1.html",
			"http://uptruyen.com/manga/189533/vua-su-tu/vua-su-tu-chap-1.1-khoi-dau.html",
			"http://uptruyen.com/manga/189532/vua-su-tu/vua-su-tu-chap-2-chuyen-o-aiden.html",
			"http://uptruyen.com/manga/189531/vua-su-tu/vua-su-tu-chap-2-hanh-trinh-den-new-york.html",
			"http://uptruyen.com/manga/189530/vua-su-tu/vua-su-tu-chap-3-bi-mat-da-anh-trang.html",
			"http://uptruyen.com/manga/189522/vua-su-tu/vua-su-tu-chap-11-thanh-tri-cua-simba.html",
			"http://uptruyen.com/manga/189521/vua-su-tu/vua-su-tu-chap-12-benh-ban.html",
			"http://uptruyen.com/manga/189520/vua-su-tu/vua-su-tu-chap-13-huyet-thanh.html",
			"http://uptruyen.com/manga/189598/vua-su-tu/vua-su-tu-chap-14-ghi-chep-ve-nui-mat-trang.html",
			"http://uptruyen.com/manga/189597/vua-su-tu/vua-su-tu-chap-15-quay-ve-que-cu.html",
			"http://uptruyen.com/manga/189596/vua-su-tu/vua-su-tu-chap-16-dai-chinh-phuc.html",
			"http://uptruyen.com/manga/189776/vua-su-tu/vua-su-tu-chap-17-dia-nguc-bang-gia.html",
			"http://uptruyen.com/manga/189835/vua-su-tu/vua-su-tu-chap-18-ket-thuc.html",
			"http://uptruyen.com/manga/189529/vua-su-tu/vua-su-tu-chap-4-dat-chan-den-chau-phi.html",
			"http://uptruyen.com/manga/189528/vua-su-tu/vua-su-tu-chap-5-tieng-goi-noi-hoang-da.html",
			"http://uptruyen.com/manga/189527/vua-su-tu/vua-su-tu-chap-6-loi-day-cua-amdoguni.html",
			"http://uptruyen.com/manga/189526/vua-su-tu/vua-su-tu-chap-7-nu-hoang-gunga.html",
			"http://uptruyen.com/manga/189525/vua-su-tu/vua-su-tu-chap-8-ben-dong-dunga.html",
			"http://uptruyen.com/manga/189524/vua-su-tu/vua-su-tu-chap-9-nuoc-mat-nu-hoang-gunga.html",
			"http://uptruyen.com/manga/189523/vua-su-tu/vua-su-tu-chap-10-tranh-dau.html"
		);
		
		//dont touch
		$chapter = 1;
		$image_counter = 0;
		foreach($link as $l){
			echo '<p>Getting <a href="'.$l.'">Chapter '.$chapter.'</a> ...</p>';
			$file = file_get_contents($l);
			$pattern = '/' . $image_path . '[\._a-zA-Z0-9-\/]+/i';
			preg_match_all($pattern, $file, $matches, PREG_OFFSET_CAPTURE);
			if(!empty($matches)){
				if(!empty($matches[0])){
					//create dir by chapter
					$dir = ROOT_PATH.IMAGE_PATH.STORY_FOLDER.$story_name.'/'.$chapter;
					FileHandler::CheckDir($dir);
		
					//save image to local
					$counter = 0;
					foreach($matches[0] as $img){
						//write file
						self::download($img[0], $dir.'/'.$counter.'.'.FileHandler::getExtension($img[0],'jpg'));
			
						//incre counter
						$counter++;
						$image_counter++;
		
						//debug
						echo ($counter-1).' -> ';
					}
				}
			}
			$chapter++;
		}
		
		echo "<hr><h1>DONE!!! Total <font color='red'>$image_counter</font> images</h1>";
	}

}