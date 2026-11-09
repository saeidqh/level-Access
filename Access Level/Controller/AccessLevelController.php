<?php

namespace app\modules\automation\controllers;

use app\modules\automation\models\HumanResource;
use Yii;
use yii\web\Controller;
use yii\helpers\Html;

class AccessLevelController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['list', 'update'],
                'rules' => [
                    [
                        'actions' => ['list', 'update'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action, $exteraData = []) {
                            return \app\access\AccessControl::isAccess([
                                'or',
                                ['formAction', 'aid' => 414],
                                ['obs', 'check' => 'isCandoAdmin']
                            ]);
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionList() {
        $accessLevelSearchModel = new \app\modules\automation\models\AccessLevelSearch();
        $accessLevelSearchModel->setScenario('search');
        $accessLevelDataProvider = $accessLevelSearchModel->search(Yii::$app->request->queryParams);

        $companyArray = \yii\helpers\ArrayHelper::map(\app\components\ActiveModel::find('\app\modules\OBS\models\Company')
                                ->joinWith('company')
                                ->asArray()->all(), 'company.PersonID', 'company.PersonTitle');

        return $this->render('list', [
                    'companyArray' => $companyArray,
                    'accessLevelDataProvider' => $accessLevelDataProvider,
                    'accessLevelSearchModel' => $accessLevelSearchModel
        ]);
    }

    public function actionUpdate($Id) {
        if (!Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            throw new \yii\web\BadRequestHttpException();
        }
        $Id = Html::encode($Id);
        $accessLevelModel = \app\modules\automation\models\AccessLevel::find()->where(['ID' => $Id])->one();
        $accessLevelModel->setScenario('update');
        if ($accessLevelModel->load(Yii::$app->request->post())) {
            if ($accessLevelModel->validate()) {
                if ($accessLevelModel->save()) {
                    return \app\components\Utility::renderPjaxCallBack('accessLevelCallbackPjax', [
                                'result' => true,
                                'message' => Yii::t('automation', 'Update successfully.')
                    ]);
                } else {
                    return \app\components\Utility::renderPjaxCallBack('accessLevelCallbackPjax', [
                                'result' => false,
                                'message' => Yii::t('automation', 'Error on Update.')
                    ]);
                }
            }
        }


        return $this->renderAjax('update', [
                    'accessLevelModel' => $accessLevelModel,
        ]);
    }

    public function actionVerified($Id) {
        if (!Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            throw new \yii\web\BadRequestHttpException();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $humanResourceModel = HumanResource::find()->where(['Id' => $Id])->one();
        $humanResourceModel->setScenario('verified');
        if (!$humanResourceModel) {
            throw new \yii\web\BadRequestHttpException();
        }
        $humanResourceModel->Verified = $humanResourceModel->Verified ? 0 : 1;
        if ($humanResourceModel->save()) {
            return [
                'result' => true,
                'message' => 'Human Resource changed Verified successfully!',
            ];
        } else {
            return [
                'result' => false,
                'message' => 'Error in Verified Human Resource!',
            ];
        }
    }

}
