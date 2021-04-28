<?php

namespace app\components;

use Yii;
use \yii\web\Request;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Общий компонент.
 */
class Common extends Component
{
    /**
     * Generate primitive string.
     * @param length
     * @return string
     */
    public function generateExtid($length = 14)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $extIdString = '';

            for ($i = 0; $i < $length; $i++) {
                $extIdString .= $characters[rand(0, $charactersLength - 1)];
            }

        return $extIdString;
    }

    /**
     * Get url from referrer request.
     * @param parseUrl - если хотим распарсить url средствами urlManager.
     * @return string
     */
    public function getUrlFromReferrer(bool $parseUrl = true)
    {
        $urlReferrer = new Request(['url' => parse_url(Yii::$app->request->referrer, PHP_URL_PATH)]);

        return ($parseUrl === true ? Yii::$app->urlManager->parseRequest($urlReferrer) : $urlReferrer);
    }

    /**
     * Разделить на contoller и action строку controller/action.
     */
    public function explodePath($urlPath)
    {
        return [$controller, $action] = explode('/', $urlPath);
    }
}