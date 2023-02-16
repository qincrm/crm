<?php

namespace App\Models;

use Yii;

class Captcha {

    /**
     * 验证码
     * 
     * @author zhaoyuhao 2021-10-01
     */ 
    public function create($request) {

        $image = imagecreatetruecolor(100, 23);    //1>设置验证码图片大小的函数
        $bgcolor = imagecolorallocate($image,255,255,255); //#ffffff
        imagefill($image, 0, 0, $bgcolor);
        $captcha_code = "";
        for($i=0;$i<4;$i++){
          //设置字体大小
          $fontsize = 6;
          //设置字体颜色，随机颜色
          $fontcolor = imagecolorallocate($image, rand(0,120),rand(0,120), rand(0,120));      //0-120深颜色
          //设置数字
          $fontcontent = rand(0,9);
          //10>.=连续定义变量
          $captcha_code .= $fontcontent;
          //设置坐标
          $x = ($i*100/4)+rand(5,10);
          $y = rand(5,10);

          imagestring($image,$fontsize,$x,$y,$fontcontent,$fontcolor);
        }
        $request->session()->put('captcha', $captcha_code);
        //10>存到session
        //8>增加干扰元素，设置雪花点
        for($i=0;$i<200;$i++){
          //设置点的颜色，50-200颜色比数字浅，不干扰阅读
          $pointcolor = imagecolorallocate($image,rand(50,200), rand(50,200), rand(50,200));
          //imagesetpixel — 画一个单一像素
          imagesetpixel($image, rand(1,99), rand(1,29), $pointcolor);
        }
        //9>增加干扰元素，设置横线
        for($i=0;$i<4;$i++){
          //设置线的颜色
          $linecolor = imagecolorallocate($image,rand(80,220), rand(80,220),rand(80,220));
          //设置线，两点一线
          imageline($image,rand(1,99), rand(1,29),rand(1,99), rand(1,29),$linecolor);
        }

        //2>设置头部，image/png
        header('Content-Type: image/png');
        //3>imagepng() 建立png图形函数
        imagepng($image);
        //4>imagedestroy() 结束图形函数 销毁$image
        imagedestroy($image);
    }

}