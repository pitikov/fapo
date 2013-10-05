<?php

class UserController extends Controller
{

	public function init()
	{
	    if (Yii::app()->user->isGuest) {
		$this->defaultAction = 'login';
	    } else {
	        $this->defaultAction = 'profile';
	    }
	    parent::init();
	}


	protected function beforeAction($action)
	{
	    if (in_array($action->id, array('logout', 'profile')) and Yii::app()->user->isGuest) {
		throw new CHttpException(400,'Нет доступа');
	    }
	    if (!Yii::app()->user->isGuest) {
		$this->layout = '//layouts/column2';
		$this->menu = array(
	            array('label'=>'Профиль','url'=>array('/user/profile')),
	            array('label'=>'Привязка соц.сетей','url'=>array('/user/openidattach')),
	            array('label'=>'Выйти','url'=>array('/user/logout')),

	        );
	    }
	    $ret = parent::beforeAction($action);
	    return $ret;
	}


	/** @fn actionInformation
	* @brief Получение публичной информации о пользователе
	*
	* @param $login Учетное имя пользователя
	*/
	public function actionInformation($login)
	{
		$this->render('information');
	}

	/** @fn actionLogin
	* @brief Авторизация пользователя
	*/
	public function actionLogin()
	{
	    $serviceName = Yii::app()->request->getQuery('service');
	    if (isset($serviceName)) {
		/** @var $eauth EAuthServiceBase */
		$eauth = Yii::app()->eauth->getIdentity($serviceName);
		$eauth->redirectUrl = Yii::app()->user->returnUrl;
		$eauth->cancelUrl = $this->createAbsoluteUrl('user/login');

		try {
		    if ($eauth->authenticate()) {
			//var_dump($eauth->getIsAuthenticated(), $eauth->getAttributes());
			$identity = new EAuthUserIdentity($eauth);

			// successful authentication
			if ($identity->authenticate()) {
			    Yii::app()->user->login($identity);
			    //var_dump($identity->id, $identity->name, Yii::app()->user->id);exit;

			    // special redirect with closing popup window
			    $eauth->redirect();
			} else {
			    // close popup window and redirect to cancelUrl
			    $eauth->cancel();
			}
		    }

		    // Something went wrong, redirect to login page
		    $this->redirect(array('user/login'));
		}
		catch (EAuthException $e) {
		    // save authentication error to session
		    Yii::app()->user->setFlash('error', 'EAuthException: '.$e->getMessage());

		    // close popup window and redirect to cancelUrl
		    $eauth->redirect($eauth->getCancelUrl());
		}
	    }
	    $model=new LoginForm;

	    // if it is ajax validation request
	    if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
	    {
		echo CActiveForm::validate($model);
		Yii::app()->end();
	    }

	    // collect user input data
	    if(isset($_POST['LoginForm']))
	    {
		$model->attributes=$_POST['LoginForm'];
		// validate user input and redirect to the previous page if valid
		if($model->validate() && $model->login())
		    $this->redirect(Yii::app()->user->returnUrl);
	    }
	    // display the login form
	    $this->render('login',array('model'=>$model));
	}

	/** @fn actionLogout
	* @brief Деавторизация пользователя
	*/
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	/** @fn actionProfile
	* @brief редактирование/просмотр профиля пользователя
	*/
	public function actionProfile()
	{
		$this->render('profile');
	}

	public function actionPwdrecovery()
	{
		$this->render('pwdrecovery');
	}

	public function actionPwdrecoverycomplit()
	{
		$this->render('pwdrecoverycomplit');
	}

	public function actionOpenidattach()
	{
		$this->render('openidattach');
	}

	public function actionOpenidAuthorizated($emal)
	{

	}

	/** @fn actionRegistration
	* @brief Регистрация пользователя (локальная регистрация)
	*/
	public function actionRegistration()
	{
	    $model=new SiteUser;

	    if(isset($_POST['ajax']) && $_POST['ajax']==='site-user-registration-form')
	    {
		echo CActiveForm::validate($model);
		Yii::app()->end();
	    }

	    if(isset($_POST['SiteUser']))
	    {
		$model->attributes=$_POST['SiteUser'];
		if($model->validate())
		{
		    // form inputs are valid, do something here
		    $model->save();
		    return;
		}
	    }
	    $this->render('registration',array('model'=>$model));;
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}