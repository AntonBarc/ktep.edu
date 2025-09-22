<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'], // Разрешить эти действия
                        'roles' => ['@'], // Только для авторизованных пользователей
                    ],
                    [
                        'allow' => true,
                        'actions' => ['login'], // Разрешить действие login
                        'roles' => ['?'], // Только для гостей
                    ],
                    [
                        'allow' => false, // Запретить всё остальное
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

    public function beforeAction($action)
    {
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            $this->layout = $user->isAdmin() ? 'admin' : 'main';
        }
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()) {
            return $this->render('index_admin'); // Отображаем admin view
        }

        return $this->render('index'); // Отображаем обычный view
        
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome(); // Перенаправляем авторизованного пользователя на главную
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome(); // После успешного входа на главную
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }


    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionMaterials()
    {
        return $this->render('materials');
    }

    public function actionReports()
    {
        return $this->render('reports');
    }

    public function actionSettings()
    {
        return $this->render('settings');
    }

    /* public function actionUsers()
    {
        // Получаем всех пользователей из базы данных
        $users = User::find()->all();

        // Передаем данные в представление
        return $this->render('index', [
            'users' => $users,
        ]);
    } */

    public function actionProfile()
    {
        return $this->render('profile');
    }
}
