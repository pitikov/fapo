<?php

class DossierController extends Controller
{

  public function init()
  {
      parent::init();
      $this->defaultAction='list';
  }

  public function actionAdd()
  {
    $this->render('add');
  }

  public function actionDrop()
  {
    $this->render('drop');
  }

  public function actionEdit()
  {
    $this->render('edit');
  }

  public function actionList()
  {
      $federationMembers = new CActiveDataProvider('FederationMember',  array(
      'criteria'=>array(
	'order'=>'federation_role ASC',
      ),
      'pagination'=>array(
	'pageSize'=>10,
      ),
    ));
    $this->render('list', array('federationMembers'=>$federationMembers));
  }

  public function actionView()
  {
    $this->render('view');
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
