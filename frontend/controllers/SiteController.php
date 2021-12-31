<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\components\BaseController;
/**
 * Site controller
 */
class SiteController extends BaseController
{
    // public $_baseUrl = "/";
    // public $_basePath = "/";
    // public $_lang = "en";
    // public $_langID = 1;
    // public $_module = array();
    // public $_user = array();
    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        //'actions' => ['login', 'error','index'],
                        'actions' => ['error','index'],
                        'allow' => true,
                    ],
                    [
                        //'actions' => ['logout', 'index'],
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    // public function actions()
    // {
    //     return [
    //         'error' => [
    //             'class' => 'yii\web\ErrorAction',
    //         ],
    //         'captcha' => [
    //             'class' => 'yii\captcha\CaptchaAction',
    //             'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
    //         ],
    //     ];
    // }
    
    public function init() {
        parent::init();
        global $vm;
        
    }
    
    public function beforeAction($action) {
        
        return parent::beforeAction($action);
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    { 
        global $vm;
        $this->layout = 'blank';
        return $this->render('index');
    }
    
    public function actionError()
    {
        $this->layout = 'blank';
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
    }
    
}
