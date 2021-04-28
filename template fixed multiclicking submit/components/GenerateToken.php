<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\bootstrap\Html;

/**
 * Компонент для работы с токенами, которые передаются с POST/GET запросами.
 */
class GenerateToken extends Component
{
    /**
     * Генерирует токен для формы(необходимо вызвать функцию после открытия формы) и записывает данные о токене в сессию.
     * @return string
     */
    public function setFormToken()
    {
        $token = \Yii::$app->security->generateRandomString(19);
        $tokenEncrypt = Yii::$app->security->hashData($token, yii::$app->request->cookieValidationKey);
        Yii::$app->session->set('formTokenUsed', Yii::$app->session->get('formTokenUsed') + 1);
        Yii::$app->session->set('formToken', (Yii::$app->session->get('formToken') !== null ? array_merge(Yii::$app->session['formToken'], [$tokenEncrypt]) : [$tokenEncrypt]));

        return Html::hiddenInput(\Yii::$app->params['formToken'] . '-' . Common::generateExtid(16), $token);
    }

    /**
     * Очищает сессию, связанную с токенами.
     * @return void
     */
    public function clearFormToken()
    {
        Yii::$app->session->remove('formToken');
        Yii::$app->session->set('formTokenUsed', 0);
    }

    /**
     * Очищает сессию токенов с дополнительными условиями.
     * @return void
     */
    public function clearFormTokenBeforeAction()
    {
        $data = \Yii::$app->request->post();
        $sessionToken = Yii::$app->session->get('formToken');

        if ($data == null) {
            //if ($sessionToken !== null) {
                //if (isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0') {
                    self::clearFormToken();
                //}
            //}
       }
    }
}