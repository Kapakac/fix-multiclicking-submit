<?php

namespace app\components;

use yii;
use yii\base\ExitException;

/**
 * Исключение, которое будет автоматически обрабатываться на уровне yii\base\Application.
 */
class ExceptionEx extends ExitException
{
    /**
     * @param name: название (выведем в качестве названия страницы).
     * @param message: подробное сообщение об ошибке.
     * @param code: код ошибки.
     * @param status: статус ответа(по умолчанию: 500).
     * @param urlRedirect: редирект(чтобы не вываливаться во view с ошибкой, например).
     * @param previous: предыдущее исключение.
     */
    public function __construct(string $name, $message = null, $code = 0, int $status = 500, string $urlRedirect, \Exception $previous = null)
    {
        if ($urlRedirect !== null) {
           return yii::$app->controller->redirect($urlRedirect);
        } else {
            // Либо можно сразу здесь генерировать View.
            $view = yii::$app->getView();
            $response = yii::$app->getResponse();
            $response->data = $view->render('@app/views/site/exception-ex.php', [
                'name' => $name,
                'message' => $message,
            ]);

            $response->setStatusCode($status);

            parent::__construct($status, $message, $code, $previous);
        }
    }
}