<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use omnilight\assets\SweetAlertAsset;

SweetAlertAsset::register($this);

$canUpdate = \app\access\AccessControl::can("automation/access-level/update", []);

$this->title = Yii::t('automation', 'Access Level Dashboard');
?>

<?=
\app\components\PageHeader::widget([
    'title' => $this->title,
    'icon' => 'im-screen',
]);
?>

<div class='col-md-12 col-lg-12'>

    <?=
    kartik\grid\GridView::widget([
        'id' => 'human-resource-grid',
        'floatHeader' => true,
        'options' => [
            'style' => 'font-family:tahoma; font-size:12px;',
        ],
        'tableOptions' => ['class' => 'table table-striped table-hover non-responsive table table-bordered'],
        'dataProvider' => $accessLevelDataProvider,
        'filterModel' => $accessLevelSearchModel,
        'hover' => true,
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'id' => 'access-level-grid-pjax',
            ],
        ],
        'columns' => [
            [
                'label' => \Yii::t("automation", 'ID'),
                'attribute' => 'ID',
                'value' => 'ID',
                'options' => [
                    'width' => '10px'
                ],
            ],
            [
                'attribute' => 'company',
                'label' => Yii::t("obs", 'Company'),
                'value' => function ($model) {
                    return $model->company->companyName;
                },
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $companyArray,
                    'options' => ['placeholder' => 'انتخاب شرکت'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px'
                    ]
                ],
            ],
            [
                'label' => \Yii::t("automation", 'NationalCode'),
                'attribute' => 'NationalCode',
//                'value' => 'NationalCode',
                'value' => function ($model) {
                    return $model->humanResource->NationalCode;
                },
            ],
            [
                'label' => \Yii::t("automation", 'FirstName and LastName'),
                'attribute' => 'FullName',
                'value' => function ($model) {
                    return $model->humanResource->fullName;
                },
            ],
            [
                'label' => \Yii::t("automation", 'Father'),
                'attribute' => 'Father',
//                'value' => 'Father',
                'value' => function ($model) {
                    return $model->humanResource->Father;
                },
            ],
            [
                'label' => \Yii::t("automation", 'MembershipDate'),
                'attribute' => 'MembershipDate',
                'value' => function ($model) {
                    return $model->humanResource->MembershipDate;
                },
            ],
            [
                'attribute' => 'HasSecret',
                'filter' => Html::activeDropDownList($accessLevelSearchModel, 'HasSecret', app\modules\automation\models\AccessLevel::getHasSecretList(), ['class' => 'form-control', 'prompt' => '']),
                'format' => 'raw',
                'value' => function ($model) {
                    return '<table class="inner-table">'
                    . '<tr><td width=\'200\'>'
                    . "<span class='" . ($model->HasSecret ? 'text-success' : '') . "'><strong>" . ($model->HasSecret ? 'دارد' : 'ندارد') . "</strong></span>"
                    . '<tr><td >'
                    . $model->SecretLastUpdate
                    . '</table>';
                }
            ],
            [
                'attribute' => 'HasTopSecret',
                'filter' => Html::activeDropDownList($accessLevelSearchModel, 'HasTopSecret', app\modules\automation\models\AccessLevel::getHasSecretList(), ['class' => 'form-control', 'prompt' => '']),
                'format' => 'raw',
                'value' => function ($model) {
                    return '<table class="inner-table">'
                    . '<tr><td width=\'200\'>'
                    . "<span class='" . ($model->HasTopSecret ? 'text-success' : '') . "'><strong>" . ($model->HasTopSecret ? 'دارد' : 'ندارد') . "</strong></span>"
                    . '<tr><td >'
                    . $model->TopSecretLastUpdate
                    . '</table>';
                }
            ],
            [
                'attribute' => 'Description',
                'value' => 'Description',
            ],
            [
                'attribute' => 'lastUser',
                'label' => Yii::t('automation', 'Last Update'),
                'format' => 'raw',
                'value' => function ($model) {
                    return '<table class="inner-table">'
                    . '<tr><td width=\'200\'>'
                    . (isset($model->lastUser) ? $model->lastUser->fullName : '')
                    . '<tr><td >'
                    . $model->lastUpdate
                    . '</table>';
                },
            ],
            [
                'class' => kartik\grid\ActionColumn::className(),
                'template' => '{edit}',
                'options' => [
                    'width' => '100px'
                ],
                'buttons' => [
                    'edit' => function ($key, $model, $index) use ($canUpdate) {
                        if ($canUpdate) {
                            return kartik\helpers\Html::a('<i class="im-pencil s15"></i>', '#', [
                                        'class' => 'btn btn-xs ',
                                        'data-pjax' => '0',
                                        'title' => 'Edit Access Level',
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top',
                                        'data-original-title' => 'Edit Access Level',
                                        'data-url' => Yii::$app->urlManager->createAbsoluteUrl(["automation/access-level/update", 'Id' => $model->ID]),
                                        'onclick' => 'showAccessLevelModal(this)',
                            ]);
                        }
                    },
                ],
            ]
        ],
    ]);
    ?>
</div>
</div>


<?php
yii\bootstrap\Modal::begin([
    'id' => 'access-level-modal',
    'header' => Yii::t('automation', 'Create or Update Access Level'),
    'size' => yii\bootstrap\Modal::SIZE_LARGE,
    'options' => [
        'tabindex' => '',
    ]
]);
?>
<?php Pjax::begin(['id' => 'access-level-form-pjax']); ?>

<?php Pjax::end(); ?>
<?php yii\bootstrap\Modal::end(); ?>


<script>

    function showAccessLevelModal(obj) {
        ajaxProgressLoader('show', '.modal-content');
        var url = $(obj).attr('data-url');
        $.ajax({
            url: url,
            dataType: 'html'
        }).done(function (data) {
            $('#access-level-form-pjax').html(data);
            $('#access-level-modal').modal('show');
            ajaxProgressLoader('hide', '.modal-content');
        }).error(function () {
            showMsgBoxInBottomRight('خطای سرور', 'error');
            return false;
        });
    }

    $(document).ready(function () {
        $('#access-level-form-pjax').on('pjax:send', function () {
            ajaxProgressLoader('show', '#access-level-modal .modal-body');
        });
        $('#access-level-form-pjax').on('pjax:complete', function (b, d) {
            ajaxProgressLoader('hide', '#access-level-modal .modal-body');
        });

    });


    function accessLevelCallbackPjax(data) {
        $('#access-level-modal').modal('hide');
        if (data['result']) {
//            showMsgBoxInBottomRight(data['message'], 'success');
            swal({
                title: data.message,
                type: 'success',
                text: 'Are you want to reload data?',
                confirmButtonText: "Reload",
                showConfirmButton: true,
                showCancelButton: true,
            }, function (isConfirm) {
                if (isConfirm) {
                    reloadForm();
                }
            });
        } else {
            showMsgBoxInBottomRight(data['message'], 'error');
        }
    }

    function reloadForm() {
        $.pjax.reload({
            container: '#access-level-grid-pjax',
            timeout: false,
            push: false,
            replace: false,
        });
    }




</script>

<?php
$this->registerCss("
        input[type=text]{height: 30px;} 
        .table > thead > tr > td{padding:4px;} 

        .table {font-family: tahoma; font-size:11px; width:100%; } 

        .table input[type='text']{font-family: tahoma; font-size:12px; width:100%;} 
        .table tr{/*height:100px !important;overflow: hidden*/}
        .table tr th{text-align:center;}
        .table tr td{padding:0 important;text-align: center;}
        .table>tbody>tr>td, 
        .table>tbody>tr>th, 
        .table>tfoot>tr>td, 
        .table>tfoot>tr>th, 
        .table>thead>tr>td, 
        .table>thead>tr>th {
            padding: 3px !important;
            vertical-align: middle;
        }

        .box-bg-3{
//            background-position: top center;
            background-position: top -47px right 211px;
            background-size: 578px;
        }
        .table-striped  tbody tr.odd {
                background-color: #ffecec;
        }

        .table .filters td{
        /*    background: rgb(231, 210, 189);*/
//            background: background:#f8e777;
            background: background:#f8e111;
        }
        .table td{
            max-height: 42px !important;

        }
        .table .table-responsive1
        {
            overflow: visible !important;    
        }
        .table tbody>tr>td, 
        .table tbody>tr>th, 
        .table tfoot>tr>td, 
        .table tfoot>tr>th, 
        .table thead>tr>td, 
        .table thead>tr>th {
            border-top: 1px solid #E7E7E7;
        }
        #human-resource-grid-container .table.table-bordered thead tr{
            background:#f8e777;
            height:35px;
        }
        #ims-document-expire-grid .table.table-bordered thead tr{
            background:#fdf4b1;
            height:35px;
        }






        .table .td-indent-popup{
            min-width: 10px;
            background: #eee !important; 
            position:relative;
            border-color: #eee !important;
        }
        .table .td-indent-popup .indent-line{
        /*    height:100%;
            width: 100%;*/
            border-left: solid 1px white;
            position:absolute;
            top:0;
            bottom: 0;
            left:0px;
            right:0;


        }

        .table .td-indent{
            width:10px;
            background: #9543FD; 
        }
        /*.table th{
            line-height: 3 !important;
            background: rgb(0, 144, 175) !important;
            color:#DADADA !important;

        }
        .table th a {
            color:#fff !important;
        }*/
        ");
?>

