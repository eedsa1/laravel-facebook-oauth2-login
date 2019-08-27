<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Facebook\Facebook;
use PDO;

class FacebookLoginController extends Controller
{	

	
	public function login(){

		session_start();

		$fb = new Facebook([
		  'app_id' => 'yourcode',
		  'app_secret' => 'yourcode',
		  'default_graph_version' => 'v2.10',
		]);

		$helper = $fb->getRedirectLoginHelper();

		$permissions = ['email'];

		try {

			if(isset($_SESSION['face_access_token'])){
				$accessToken = $_SESSION['face_access_token'];
			}
			else{
				$accessToken = $helper->getAccessToken();
			}
 	
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  	echo 'Graph returned an error: ' . $e->getMessage();
		  	exit;
		} 
		catch(Facebook\Exceptions\FacebookSDKException $e) {
		  	echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  	exit;
		}

		if (! isset($accessToken)) {
			$url_login = 'https://laravel-facebook-login.test/login';

			$loginUrl = $helper->getLoginUrl($url_login, $permissions);
		}
		else{

			$url_login = 'https://laravel-facebook-login.test/login';

			$loginUrl = $helper->getLoginUrl($url_login, $permissions);

			//verifica se o usuário já está autenticado
			//usuário autenticado
			if(isset($_SESSION['face_access_token'])){
				//omite a necessidade de recuperar o token em algumas operações
				$fb->setDefaultAccessToken($_SESSION['face_access_token']);

			}
			//usuário não está autenticado
			else{
				$_SESSION['face_access_token'] = (string) $accessToken;
				$oAuth2Client = $fb->getOAuth2Client();

				//aumentar o "tempo de vida" do login
				$_SESSION['face_access_token'] = $oAuth2Client->getLongLivedAccessToken($_SESSION['face_access_token']);
				$fb->setDefaultAccessToken($_SESSION['face_access_token']);
			}

			try {
			  	$response = $fb->get('/me?fields=name, picture, email');
				
		  		//método que retorna as informações do usuário
			  	$user = $response->getGraphUser();

			  	//verifica se o usuário já está cadastrado no sistema
			  	$con = new \PDO("mysql:host=localhost;dbname=facebook_login", "homestead", "secret");
			  	$crud = $con->prepare("select id, nome, email, senha from usuario where email='".$user['email']."' limit 1");
			  	$resultado_usuario = $crud->execute();
			  	$row_usuario = $crud->fetch(PDO::FETCH_ASSOC);

			  	if($row_usuario){
			  		$_SESSION['nome'] = $row_usuario['nome'];
			  		$_SESSION['email'] = $row_usuario['email'];
			  		//redireciona o usuario para uma página (administrativo ou home)
			  		header("Location: /home");
			  	}
			  	else{
			  		$insert = $con->prepare("INSERT INTO usuario(nome, email, usuario) values('".$user['name']."', '".$user['email']."', '".$user['email']."');");

			  		$t = $insert->execute();

			  		$_SESSION['nome'] = $user['name'];
			  		$_SESSION['email'] = $user['email'];

			  		header("Location: /home");
			  	}
			} 
			catch(Facebook\Exceptions\FacebookResponseException $e) {
			  	echo 'Graph returned an error: ' . $e->getMessage();
			  	exit;
			} 
			catch(Facebook\Exceptions\FacebookSDKException $e) {
			  	echo 'Facebook SDK returned an error: ' . $e->getMessage();
			  	exit;
			}
		}

		return view('login', compact('loginUrl'));

	}

	public function home(){

		return view('home');

	}

	public function sair(){

		//elimina a sessão do facebook
		session_start();
		if(isset($_SESSION['face_access_token']));
			unset($_SESSION['face_access_token']);

		header("Location: /login");

	}

}
