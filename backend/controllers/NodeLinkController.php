<?php

namespace backend\controllers;

use common\models\Node;
use Yii;
use common\models\NodeLink;
use common\models\NodeLinkSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\grid\GridView;

/**
 * NodeLinkController implements the CRUD actions for NodeLink model.
 */
class NodeLinkController extends BackendController
{
    public $title = '节点推荐链接管理';
    public $description = '推荐链接管理';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all NodeLink models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NodeLinkSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $gridColumns[] = [
            'class'=>'kartik\grid\SerialColumn',
            'contentOptions'=>['class'=>'kartik-sheet-style'],
            'width'=>'36px',
            'header'=>'序号',
            'headerOptions'=>['class'=>'kartik-sheet-style']
        ];
        $gridColumns[] = [
            'attribute'=>'id',
            'vAlign'=>'middle',
        ];
        $gridColumns[] = [
            'attribute'=>'node_id',
            'vAlign'=>'middle',
            'width'=>'180px',
            'value'=>function ($searchModel, $key, $index, $widget) {
                return empty($searchModel->node->name) ? null : $searchModel->node->name;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(Node::find()->orderBy('name')->asArray()->all(), 'id', 'name'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'node'],
            'format' => 'raw'
        ];
        $gridColumns[] = 'content';
        $gridColumns[] = [
            'class'=>'kartik\grid\BooleanColumn',
            'attribute'=>'status',
            'width'=>'100px',
            'vAlign'=>'middle',
            'trueLabel' => '启用',
            'falseLabel' => '禁用',
        ];

        $gridColumns[] = [
            'class' => '\kartik\grid\ActionColumn',
            'template'=>'{view} {update} {delete}',
        ];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays a single NodeLink model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new NodeLink model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new NodeLink();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->cache->delete('NodeLink'.$model->node_id);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing NodeLink model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->cache->delete('NodeLink'.$model->node_id);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing NodeLink model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        Yii::$app->cache->delete('NodeLink'.$model->node_id);
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the NodeLink model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NodeLink the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NodeLink::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
