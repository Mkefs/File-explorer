<?php
require_once "router.php";
require_once "login_minix.php";

// Get page request
Router::add("", function() {
	Loggin_minix\isnt_logged();
	require_once "view/template.php";
	View\Render("view/index.php");
});

Router::add("register", function() {
	Loggin_minix\isnt_logged();
	require_once "view/template.php";
	View\Render("view/register.php");
});

Router::add("files", function() {
	Loggin_minix\is_logged();
	require_once "view/template.php";
	View\Render("view/files.php");
});

Router::add("logout", function() {
	session_start();
	session_destroy();
	header("Location: ./");
	exit;
});


// Post request
Router::add("register", function() {
	Loggin_minix\isnt_logged();
	require_once "comp/register_comp.php";
	$resp = new Register();
	$resp->insert();
}, "POST");

Router::add("verif", function() {
	Loggin_minix\isnt_logged();
	require_once "comp/login_comp.php";
	$resp = new Login();
	$resp->verif();
}, "POST");

Router::add("login", function() {
	Loggin_minix\isnt_logged();
	require_once "comp/login_comp.php";
	$resp = new Login();
	$resp->login();
}, "POST");

Router::add("get_dir", function() {
	Loggin_minix\is_logged();
	require_once "comp/dir_comp.php";
	$resp = new Dirs_comp();
	$resp->get_user_dir_data();
}, "POST");

Router::add("create_dir", function() {
	Loggin_minix\is_logged();
	require_once "comp/dir_comp.php";
	$resp = new Dirs_comp();
	$resp->create_subdir();
});

Router::add("upload_file", function() {
	/* Loggin_minix\is_logged(); */
	/* require_once "comp/upload_files.php"; */
	/* $resp = new Upload_files(); */
	/* $resp->upload_files(); */
}, "POST");

Router::run();
