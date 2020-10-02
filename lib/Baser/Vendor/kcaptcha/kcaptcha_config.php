<?php

# KCAPTCHA configuration file
# (71文字)
$alphabet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-^@[;:],."; # do not change without changing font files!
$convert = "あいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよらりるれろわをんがぎぐげござじづぜぞだぢづでどばびぶべぼぱぴぷぺぽ";
# symbols used to draw CAPTCHA
//$allowed_symbols = "23456789abcdeghkmnpqsuvxyz"; #alphabet without similar symbols (o=0, 1=l, i=j, t=f)
$allowed_symbols = $alphabet;

# folder with fonts
$fontsdir = 'fonts';

# CAPTCHA string length
$length = mt_rand(5,6); # random 5 or 6

# CAPTCHA image size (you do not need to change it, whis parameters is optimal)
$width = 210;
$height = 76;

# symbol's vertical fluctuation amplitude divided by 2
$fluctuation_amplitude = 3;

# increase safety by prevention of spaces between symbols
$no_spaces = false;

# show credits
$show_credits = false; # set to false to remove credits line. Credits adds 12 pixels to image height
$credits = 'www.captcha.ru'; # if empty, HTTP_HOST will be shown

# CAPTCHA image colors (RGB, 0-255)
$foreground_color = array(0, 0, 0);
$background_color = array(255, 255, 255);
//$foreground_color = array(mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
//$background_color = array(mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));

# JPEG quality of CAPTCHA image (bigger is better quality, but larger file size)
$jpeg_quality = 90;
