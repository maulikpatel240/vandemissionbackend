<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;

use common\models\AppCategory;

/**
 * HomeController Class Doc Comment
 *
 * @category Class
 * @package  MyPackage
 * @author   FRDP <info.frdp@gmail.com>
 * @license  GNU General Public License
 * @link     http://www.frdp.com/
 */
class HomeController extends Controller
{
    public function actionIndex()
    {
        return 'test';
    }

    public function actionTest()
    {
        return 'testing';
    }

    //01. Get Locum Category List (beforeauth)
public function actionAppCategory()
{ 	
	$model = AppCategory::find()->andWhere(['status'=>'Active','is_deleted'=>'No'])->orderBy(['display_order'=>SORT_ASC])->all(); 	
		
	if(!empty($model))
	{	
		$result = array(
			"result"=>"1",
			"message"=>Yii::t('app', 'Get List Successfully.'),			
			"data"=>ArrayHelper::toArray($model)
			);
	}
	else
	{
		$result = array(
			"method"=>"getlocumcategory",
			"result"=>"2",
			"message"=>Yii::t('app', 'List Not Found.')
			);	
	}			
    	echo json_encode($result,JSON_PRETTY_PRINT);
        exit;    
}

}
