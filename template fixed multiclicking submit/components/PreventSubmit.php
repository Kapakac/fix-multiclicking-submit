<?php

namespace app\components;

use Yii;
use yii\web\Response;
use app\controllers\ControllerEx;
use app\models\NavigationBar;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Компонент обработки запросов.
 */
class PreventSubmit extends Component
{
    /**
     * Вычисляет адрес для дальнейшего редиректа в случае выброса исключения. См: $this->checkToken().
     * @param whereTo: тип, куда делать редирект(пока 3 случая: предыдущий адрес, адрес из cookies, адрес как параметр).
     * @param urlRedirect: адрес для редиректа.
     * @return function
     */
    public function providerCheckToken(string $whereTo = 'referrer', string $urlRedirect = null)
    {
        $pathCA = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;

        switch ($whereTo) {
            case 'url':
                $urlRedirect = ($urlRedirect !== null ? $urlRedirect : NavigationBar::getDefaultPage());
                break;

            case 'cookies':
                $urlPath = Yii::$app->common->getUrlFromReferrer(false)->pathInfo;
                if ($urlPath !== null) {
                    $controller = Yii::$app->common->explodePath($urlPath)[0];
                    $action = Yii::$app->common->explodePath($urlPath)[1];
                    $paramsUrl = ControllerEx::getArrayCookie($controller);
                    $paramsUrl[0] = $controller . '/index';
                }
                $urlRedirect = (isset($paramsUrl) ? Yii::$app->urlManager->createUrl($paramsUrl) : NavigationBar::getDefaultPage());
                break;

            case 'referrer':
                $urlRedirect = Yii::$app->request->referrer;
                break;
        }

        return $this->checkToken($urlRedirect);
    }

    /**
     * Callback функция для self::checkToken().
     * @see https://www.php.net/manual/ru/function.array-intersect-ukey.php
     * @param keySession: ключ массива сессии.
     * @param keyData: ключ массива даты.
     * @return int 0|1|-1
     */
    private function compareToken($keySession, $keyData)
    {
        if ($keySession === Yii::$app->security->hashData($keyData, yii::$app->request->cookieValidationKey)) {
            return 0;
        }

        return -1;
    }

    /**
     * Проверяет наличие токена для положительной обработки запроса.
     * @param urlRedirect: куда может быть редирект.
     * @return \ExceptionEx
     */
    public function checkToken(string $urlRedirect = null)
    {
        $data = \Yii::$app->request->post();
        $regExp = '(^[formToken]{9}+(-)+([A-Z0-9]){16}+$)';

        $sessionToken = Yii::$app->session->get('formToken');

        $matchingKeys = preg_grep($regExp, array_keys($data)); // Ищем в запросе элемент с hidden-token.
        $filteredArray = array_intersect_key($data, array_flip($matchingKeys)); // Формируем массив из этих токенов.

        $isSended = ($sessionToken !== null ? array_intersect_ukey(array_flip($sessionToken), array_flip($filteredArray), 'self::compareToken') : false); // Ищем совпадения среди токена из сессии и пришедшего с формы.

        if (isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0') {
            if (empty($isSended) === true) {
                Yii::$app->response->format = Response::FORMAT_HTML;
                throw new ExceptionEx(\Yii::t('app', 'Данные уже были отправлены.'), \Yii::t('app', 'Данные уже были отправлены.'), 429, 429, $urlRedirect);
            }

            Yii::$app->generateToken->clearFormToken();
        }
    }
}
