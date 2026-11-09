<?php

namespace app\modules\automation\models;

use Yii;
use yii\data\ActiveDataProvider;

class AccessLevelSearch extends AccessLevel {

    public $company;
    public $NationalCode;
    public $FullName;
    public $Father;
    public $MembershipDate;
    public $lastUser;

    public static function getDb() {
        return \app\components\Utility::db();
    }

    public function rules() {
        return [
            [['ID', 'company', 'FullName', 'Father', 'NationalCode', 'MembershipDate', 'HasSecret', 'HasTopSecret', 'lastUser', 'Description'], 'safe', 'on' => 'search']
        ];
    }

    public function search($params) {
        $humanResourceTable = \app\modules\automation\models\HumanResource::tableName();
        $accessLevelTable = \app\modules\automation\models\AccessLevel::tableName();
        $userTable = \app\modules\account\models\User::tableName();
        
        $companyChildsArray = \app\components\CandoHelper::companyChilds(Yii::$app->session->get('CompanyID'), 'array');

        $activeQuery = AccessLevel::find()
                ->leftJoin($humanResourceTable, "$humanResourceTable.Id = $accessLevelTable.HumanResourceIdRef")
                ->leftJoin($userTable, "$userTable.UserID = $accessLevelTable.LastUserIDRef")
                ->where(['in', "$accessLevelTable.CompanyIDRef", $companyChildsArray]);

        $dataProvider = new ActiveDataProvider([
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'defaultOrder' => ['ID' => SORT_DESC],
                'attributes' => [
                    'ID', 'HasSecret', 'HasTopSecret', 'Description', 
                    'company' => [
                        'asc' => ["$accessLevelTable.CompanyIDRef" => SORT_ASC],
                        'desc' => ["$accessLevelTable.CompanyIDRef" => SORT_DESC],
                    ],
                    'lastUser' => [
                        'asc' => ["$accessLevelTable.lastUpdate" => SORT_ASC],
                        'desc' => ["$accessLevelTable.lastUpdate" => SORT_DESC],
                    ],
                    'FullName' => [
                        'asc' => ["concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)" => SORT_ASC],
                        'desc' => ["concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)" => SORT_DESC],
                    ],
                    'NationalCode' => [
                        'asc' => ["$humanResourceTable.NationalCode" => SORT_ASC],
                        'desc' => ["$humanResourceTable.NationalCode" => SORT_DESC],
                    ],
                    'Father' => [
                        'asc' => ["$humanResourceTable.Father" => SORT_ASC],
                        'desc' => ["$humanResourceTable.Father" => SORT_DESC],
                    ],
                    'MembershipDate' => [
                        'asc' => ["$humanResourceTable.MembershipDate" => SORT_ASC],
                        'desc' => ["$humanResourceTable.MembershipDate" => SORT_DESC],
                    ],
                ]
            ],
        ]);
        $this->load($params);

        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', "$accessLevelTable.ID", $this->ID])
                ->andFilterWhere(['=', "$accessLevelTable.CompanyIDRef", $this->company])
                ->andFilterWhere(['=', "$accessLevelTable.HasSecret", $this->HasSecret])
                ->andFilterWhere(['=', "$accessLevelTable.HasTopSecret", $this->HasTopSecret])
                ->andFilterWhere(['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", $this->FullName])
                ->andFilterWhere(['LIKE', "$humanResourceTable.Father", $this->Father])
                ->andFilterWhere(['LIKE', "$humanResourceTable.Mobile", $this->Mobile])
                ->andFilterWhere(['LIKE', "$humanResourceTable.NationalCode", $this->NationalCode])
                ->andFilterWhere(['LIKE', "$humanResourceTable.MembershipDate", $this->MembershipDate])
                ->andFilterWhere(['LIKE', "$accessLevelTable.Description", $this->Description])
                ->andFilterWhere(
                        ['or',
                            ['LIKE', "$accessLevelTable.lastUpdate", $this->lastUser],
                            ['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", $this->lastUser]
        ]);
        return $dataProvider;
    }

}
