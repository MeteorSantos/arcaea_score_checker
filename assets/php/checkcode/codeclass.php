<?php
class checkCode{
	
	public $code_image; // 图像资源
	public $code_width = 100; // 图像宽度
	public $code_height = 30; // 图像高度
	public $code_fontsize = 20; // 字体大小
	public $code_string = "0123456789abcdefghjkmnprstwxyABCDEFGHJKMNPRSTWXY";// 验证码随机种子
	public $code; // 生成的验证码
	
	// 检查是否加载GD库
	function __construct() {
		if(!extension_loaded("gd")) {
			die("检查是否加载GD库");
		}
	}
	
	// 生成图像资源
	private function createImage() {
		// 生成画布
		$image = imagecreatetruecolor($this->code_width,$this->code_height);
		// 设置颜色
		$backgrondcolor= imagecolorallocate($image,	220, 220, 220);
		// 填充颜色
		imagefill($image, 0, 0, $backgrondcolor);
		// 保存图像资源
		$this->code_image = $image;
		// 继续后续方法
		$this->createCode();
		$this->writeCode();
		$this->createInterference();
		// $this->showCode();
	}
	
	// 生成验证码
	private function createCode() {
		// 重置验证码
		$code = '';
		// 取4位验证码
		for($i=0;$i<4;$i++){
			$code .= $this->code_string[mt_rand(0, strlen($this->code_string)-1)];
		}
		// 保存验证码
		$this->code = $code;
	}
	
	// 写入验证码
	private function writeCode() {
		// 引入字体完整路径
		$fontfile=dirname(__FILE__,3)."/fonts/arial.ttf";
		$min= $this->code_fontsize;
		$max= $this->code_height;
		// 设置
		for($i=0;$i<4;$i++){
			// 随机取色
			$codecolor = imagecolorallocate($this->code_image, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));

			// 自定义文本
			$x = ($this->code_width/4)*$i+10;
			$y = mt_rand($min, $max);
			imagettftext($this->code_image, $this->code_fontsize, mt_rand(-15,15), $x, $y, $codecolor, $fontfile, $this->code[$i]);
		}

	}
	
	// 绘制干扰
	private function createInterference(){
		// 干扰点
		for($j=0;$j<100;$j++){
			// 随机颜色
			$pointcolor = imagecolorallocate($this->code_image, mt_rand(150, 255), mt_rand(150, 255), mt_rand(150, 255));
			// 绘制点
			imagesetpixel($this->code_image, mt_rand(0, $this->code_width), mt_rand(0, $this->code_height), $pointcolor);
		}
		// 干扰线
		for($i=0;$i<10;$i++){
			// 设置线条粗细
			imagesetthickness($this->code_image, 1);
			// 随机颜色
			$linecolor = imagecolorallocate($this->code_image, mt_rand(150, 255), mt_rand(150, 255), mt_rand(150, 255));
			// 绘制线条
			imageline($this->code_image, mt_rand(0, $this->code_width), mt_rand(0, $this->code_height), mt_rand(0, $this->code_width), mt_rand(0, $this->code_height), $linecolor);
		}		
	}
	
	// 显示验证码并销毁图像资源(开放)
	public function showCode(){
		// 生成
		header("content-type:image/png");
		imagepng($this->code_image);
		// 销毁
		imagedestroy($this->code_image);
	}
	
	
	// 生成验证码(开放)
	public function generateCode(){
		$this->createImage();
	}
	
	// 输入返回验证码(开放)
	public function returnCode(){
		// 全部转小写
		return strtolower($this->code);
	}
}
?>