<?php
//dinh nghia key
if(!defined('COIN_KEY')){
	define('COIN_KEY', 'coin');
}

//dinh nghia bang
if(!defined('T_COIN')){
	global $prefix;
	define('T_COIN', $prefix . COIN_KEY);
	define('T_COIN_PAIR', $prefix . COIN_KEY . '_pair');
	define('T_COIN_RATE', $prefix . COIN_KEY . '_rate');
	define('T_COIN_USER', $prefix . COIN_KEY . '_user');
}

