<?php

use app\components\PageHeader;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\DatepickerAsset;
use app\assets\JdateTimeAsset;
use app\modules\automation\models\AccessLevel;

DatepickerAsset::register($this);
JdateTimeAsset::register($this);
$jDate = new \app\components\jDateTime(true, true, 'Asia/Tehran');

$accessLevelModel->loadHumanResource();
?>

<style>
    #content > div:first-child {
        padding: 0;
    }

    #content .panel {
        margin-top: 10px !important;
    }

    #tender-tenderconditionarray label:not(:last-child) {
        margin-right: 30px;
    }

    #button {
        display: block;
        margin: 20px;
        padding: 10px 30px;
        background-color: #eee;
        border: solid #ccc 1px;
        cursor: pointer;
    }

    .overlay {
        position: absolute;
        top: 3px;
        right: 0;
        padding-right: 45px;
        z-index: 100;
        width: 100%;
        height: 100%;
        display: none;

    }

    .cv-spinner {
        height: 100%;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .spinner {
        width: 30px;
        height: 30px;
        border: 4px #ddd solid;
        border-top: 4px #2e93e6 solid;
        border-radius: 50%;
        animation: sp-anime 0.8s infinite linear;
    }

    @keyframes sp-anime {
        100% {
            transform: rotate(360deg);
        }
    }

    .is-hide {
        display: none;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .field-tender-tenderconditionarray > label:first-child {
        display: none;
    }

    .text-on-panel {
        background: #FFF2B3 none repeat scroll 0 0;
        color: #0b58a2;
        height: auto;
        margin-left: 20px;
        padding: 3px 5px;
        position: absolute;
        margin-top: -31px;
        border: 2px solid #337ab7;
        border-radius: 8px;
    }

    .panel {
        margin-top: 27px !important;
    }

    .panel-body {
        border: 2px solid #337ab7;
        border-radius: 8px;
        padding-bottom: 0 !important;
    }

    .panel-body strong {
        font-size: 13px !important;
    }

    select {
        padding: 0 !important;
        font-size: 12px !important;
    }
    #automation-automationtypearray label, #automation-sequeritylevelarray label:not(:last-child), #automation-positiontype label, #automation-hasappointment label {
        margin-right: 30px;
    }
</style>

<?php

$form = ActiveForm::begin(['id' => 'access-level-form',
            'options' => [
                'data-pjax' => '1',
                'enctype' => 'multipart/form-data',
        ]]);
?>

<div>
    <div class="panel panel-primary">
        <div class="panel-body">
            <h4 class="text-on-panel text-primary"><strong
                    style="font-size: 1.9rem"><?= \Yii::t("automation", 'Personal Specification') ?></strong></h4>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <?= $form->field($accessLevelModel, 'fullName')->textInput(['disabled' => true])->label(Yii::t('automation', 'FirstName and LastName')); ?>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <?= $form->field($accessLevelModel, 'NationalCode')->textInput(['disabled' => true])->label(Yii::t('automation', 'NationalCode') . ' *'); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <?= $form->field($accessLevelModel, 'Father')->textInput(['disabled' => true])->label(Yii::t('automation', 'Father')); ?>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <?= $form->field($accessLevelModel, 'Mobile')->textInput(['disabled' => true])->label(Yii::t('automation', 'Mobile')); ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <?= $form->field($accessLevelModel, 'HasSecret')->dropDownList(AccessLevel::getHasSecretList(), ['prompt' => Yii::t('automation', 'Secret')])->label(Yii::t('automation', 'Secret') . ' *'); ?>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <?= $form->field($accessLevelModel, 'HasTopSecret')->dropDownList(AccessLevel::getHasSecretList(), ['prompt' => Yii::t('automation', 'Top Secret')])->label(Yii::t('automation', 'Top Secret') . ' *'); ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <?= $form->field($accessLevelModel, 'Description')->textarea()->label(Yii::t('automation', 'Description')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?=
Html::submitButton(Yii::t('automation', 'Save'), [
    'class' => 'btn btn-success btn-sm pull-right',
])
?>
</div>
<?php ActiveForm::end(); ?>
