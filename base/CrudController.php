<?php

namespace abcms\library\base;

use Yii;
use yii\web\NotFoundHttpException;

class CrudController extends AdminController
{

    public $createScenario = null;

    /**
     * Activate/Deactivate an existing model.
     * If action is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionActivate($id)
    {
        $model = $this->findModel($id);
        $model->activate()->save(false);

        return $this->redirect($this->listingUrl($model));
    }

    /**
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchClassName = $this->searchClassName;
        $searchModel = new $searchClassName();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
                    'model' => $model,
        ]);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $className = $this->className;
        $model = new $className();
        if($this->createScenario) {
            $model->scenario = $this->createScenario;
        }
        $model->loadDefaultValues();

        if($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect($this->viewUrl($model));
        }
        else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect($this->viewUrl($model));
        }
        else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect($this->listingUrl($model));
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Banner the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $className = $this->className;
        if(($model = $className::findOne($id)) !== null) {
            return $model;
        }
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Return the redirect url that should be used after activate and delete actions
     * @param Model $model
     * @return mixed
     */
    protected function listingUrl($model)
    {
        return ['index'];
    }

    /**
     * Return the redirect url that should be used after update and create actions
     * @param Model $model
     * @return mixed
     */
    protected function viewUrl($model)
    {
        return ['view', 'id' => $model->id];
    }

}
