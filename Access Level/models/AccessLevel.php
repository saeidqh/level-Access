<?php

namespace app\modules\automation\models;

use Yii;
use app\components\jDateTime;

/**
 *
 * @property integer $ID
 * @property integer $HumanResourceIdRef
 * @property integer $LastUserIDRef
 * @property integer $HasSecret
 * @property integer $HasTopSecret
 * @property string $Description
 * @property string $lastUpdate
 */
class AccessLevel extends \yii\db\ActiveRecord {

    public $FirstName;
    public $LastName;
    public $Father;
    public $Mobile;
    public $NationalCode;
    public $EmploymentType;

    public static function tableName() {
        return 'automation_table_access_level';
    }

    public static function primaryKey() {
        return ['ID'];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public function rules() {
        return [
            [['HumanResourceIdRef', 'LastUserIDRef', 'HasSecret', 'HasTopSecret'], 'integer'],
            [['HumanResourceIdRef'], 'required'],
            [['Description', 'lastUpdate'], 'string']
        ];
    }

    public function scenarios() {
        $scenario = parent::scenarios();
        $scenario['workflow'] = ['HumanResourceIdRef', 'LastUserIDRef', 'HasSecret', 'HasTopSecret', 'Description', 'lastUpdate', 'SecretLastUpdate', 'TopSecretLastUpdate'];
        $scenario['update'] = ['LastUserIDRef', 'HasSecret', 'HasTopSecret', 'Description', 'lastUpdate', 'SecretLastUpdate', 'TopSecretLastUpdate'];
        return $scenario;
    }

    public function beforeValidate() {
        if ($this->scenario != 'workflow') {
            $jdate = new jDateTime(true, true, 'Asia/Tehran');
            $this->lastUpdate = $jdate->date("Y/m/d", false, false);
            $this->LastUserIDRef = Yii::$app->session->get('UserID');
        }
        return parent::beforeValidate($this);
    }

    public function attributeLabels() {
        return [
            'Id' => \Yii::t("automation", 'Management ID'),
            'NationalCode' => \Yii::t("automation", 'NationalCode'),
            'FirstName' => \Yii::t("automation", 'First Name'),
            'Father' => \Yii::t("automation", 'Father'),
            'EmploymentType' => \Yii::t("automation", 'Employment Type'),
            'HasSecret' => \Yii::t("automation", 'Secret'),
            'HasTopSecret' => \Yii::t("automation", 'Top Secret'),
            'Description' => \Yii::t("automation", 'Description'),
            'MembershipDate' => \Yii::t("automation", 'MembershipDate'),
            'CompanyIDRef' => \Yii::t("automation", 'Company'),
            'Status' => Yii::t('automation', 'Status'),
        ];
    }

    public static function getHasSecretList() {
        return [
            0 => 'ندارد',
            1 => 'دارد',
        ];
    }

    public static function getHasSecretType($type) {
        switch ($type) {
            case '0':
                return 'دارد';
            case '1':
                return 'ندارد';
            default:
                return 'invalid';
        }
    }

    public function getCompany() {
        return $this->hasOne(\app\modules\OBS\models\Person::className(), ['PersonID' => 'CompanyIDRef']);
    }

    public function getHumanResource() {
        return $this->hasOne(\app\modules\automation\models\HumanResource::className(), ['Id' => 'HumanResourceIdRef']);
    }

    public function getLastUser() {
        return $this->hasOne(\app\modules\account\models\User::className(), ['UserID' => 'LastUserIDRef']);
    }

    public function getFullName() {
        return $this->FirstName . ' ' . $this->LastName;
    }

    public static function saveInfo($model) {
        $isNew = false;
        $humanModel = \app\modules\automation\models\HumanResource::find()
                ->where(['NationalCode' => $model->NationalCode])
                ->one();
        if (!isset($humanModel)) {
            $isNew = true;
            $humanModel = new HumanResource();
        }
        $humanModel->setScenario('save-info');
        if ($isNew) {
            $humanModel->loadInfo($model);
            $humanModel->Status = 'New';
            $humanModel->Verified = 0;
            $humanModel->ObsIDRef = $model->Position;
            $humanModel->CompanyIDRef = $model->CompanyIDRef;
            if ($humanModel->validate()) {
                $humanModel->save();
            } else {
                var_dump($humanModel->errors);
                return false;
            }
        }
        return $humanModel;
    }

    public function loadHumanResource() {
        $humanResource = $this->humanResource;
        if (isset($humanResource)) {
            $this->FirstName = $humanResource->FirstName;
            $this->LastName = $humanResource->LastName;
            $this->Father = $humanResource->Father;
            $this->Mobile = $humanResource->Mobile;
            $this->NationalCode = $humanResource->NationalCode;
            $this->EmploymentType = $humanResource->EmploymentType;
        }
    }

}
