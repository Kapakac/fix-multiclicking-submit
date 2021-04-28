<?php

namespace app\controllers;

use yii;

use yii\web\HttpException;
use yii\web\Response;

/**
 * This is the extension for Controller.
 */
class ControllerEx extends \yii\web\Controller
{
    // Массив actions для которых стоит применять checkToken.
    const SKIP_ACTION_BY_FORM_TOKEN_WITH_REDIRECT_REFERRER = [
        'site/save-profile',
        'site/create-comment',
    ];
    // Actions для которых применяется редирект по кукам.
    const SKIP_ACTION_BY_FORM_TOKEN_WITH_REDIRECT_COOKIES = [
        'partner/save',
    ];
    // Actions для которых применяется редиктер, как параметр к функции.
    const SKIP_ACTION_BY_FORM_TOKEN_WITH_REDIRECT_URL = [
    ];
     // Массив actions для которых НЕ нужно чистить токены.
    const SKIP_ACTION_FOR_CLEAR_TOKEN = [
        'site/is-exist-email',
        'site/is-exist-phone',
    ];

    public function beforeAction($action)
    {
        //var_dump(Yii::$app->request->post());
        Yii::$app->aboutBrowser->aboutBrowser();

        if (!in_array(Yii::$app->controller->id . '/' . Yii::$app->controller->action->id, self::SKIP_ACTION_FOR_CLEAR_TOKEN)) {
            Yii::$app->generateToken->clearFormTokenBeforeAction();
        }

        if (in_array(Yii::$app->controller->id . '/' . Yii::$app->controller->action->id, self::SKIP_ACTION_BY_FORM_TOKEN_WITH_REDIRECT_REFERRER)) {
            Yii::$app->preventSubmit->providerCheckToken('referrer');
        }

        if (in_array(Yii::$app->controller->id . '/' . Yii::$app->controller->action->id, self::SKIP_ACTION_BY_FORM_TOKEN_WITH_REDIRECT_COOKIES)) {
            Yii::$app->preventSubmit->providerCheckToken('cookies');
        }

        if (in_array(Yii::$app->controller->id . '/' . Yii::$app->controller->action->id, self::SKIP_ACTION_BY_FORM_TOKEN_WITH_REDIRECT_URL)) {
            Yii::$app->preventSubmit->providerCheckToken('url');
        }

        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }
}