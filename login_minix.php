<?php namespace Loggin_minix;

// Dejará pasar solo si tiene una sesion iniciada
function is_logged() {
	session_start();
	if(!isset($_SESSION["id"])) {
		header("Location: ./");
		exit;
	}
}

// Esta hará lo contraro a la primera
function isnt_logged() {
	session_start();
	if(isset($_SESSION["id"])) {
		header("Location: files");
	}
}
