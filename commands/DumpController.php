<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\Response;

/**
 * Комаднда загрузкт демо-данных
 */
class DumpController extends Controller
{
    /**
     * @var string
     */
    public $apiBaseUrl = 'http://localhost';

    /**
     * @var string
     */
    public $apiBaseUrlPort = '8080';

    /**
     * Необходимое число постов
     *
     * @var int
     */
    public $postCount = 200000;

    /**
     * Необходимое число юзеров
     *
     * @var int
     */
    public $userCount = 100;

    /**
     * Необходимое число IP адресов
     *
     * @var int
     */
    public $ipCount = 50;

    /**
     * Необходимое число оценок
     *
     * @var int
     */
    public $markCounts = 20000;

    /**
     * Предел случившихся ошибок, контроль зацикливания
     *
     * @var int
     */
    public $maxErrors = 50;

    /**
     * @inheritdoc
     */
    public $defaultAction = 'index';

    /**
     * @var int
     */
    protected $errorsCount = 0;

    /**
     * @var array
     */
    protected $users = [];

    /**
     * @var array
     */
    protected $ips = [];

    /**
     * @var array
     */
    protected $postIds = [];

    /**
     * @var int
     */
    protected $usersMaxIndex = 0;

    /**
     * @var int
     */
    protected $ipMaxIndex = 0;

    /**
     * @var int
     */
    protected $maxPostIdsIndex = 0;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var Client
     */
    protected $http;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->faker = \Faker\Factory::create();
        $this->http = new Client();
        parent::init();
    }

    /**
     * Импорта тестовых данных
     *
     * @return int
     */
    public function actionIndex()
    {
        $inserted = 0;

        while ($inserted < $this->userCount) {
            $this->users[] = $this->faker->userName;

            if ($inserted < $this->ipCount) {
                $this->ips[] = $this->faker->ipv4;
            }
            $inserted++;
        }
        $this->usersMaxIndex = count($this->users) - 1;
        $this->ipMaxIndex = count($this->ips) - 1;

        $inserted = 0;

        while ($inserted < $this->postCount) {
            try {
                if (!$this->checkForErrorsLimit($inserted)) {
                    return ExitCode::DATAERR;
                }

                if(false != ($response = $this->makeRequest($this->buildUrl('v1/post'), $this->getPostItem()))) {
                    $this->postIds[] = ArrayHelper::getValue($response->getData(), 'id');
                    $inserted++;
                }

            } catch (\Exception $e) {
                $this->processException($e);
                continue;
            }
        }

        $inserted = 0;
        $this->maxPostIdsIndex = count($this->postIds) - 1;

        if($this->maxPostIdsIndex > 0) {
            while ($inserted < $this->markCounts) {
                try {
                    if (!$this->checkForErrorsLimit($inserted)) {
                        return ExitCode::DATAERR;
                    }
                    if($this->makeRequest($this->buildUrl('v1/mark'), $this->getMarkItem())) {
                        $inserted++;
                    }

                } catch (\Exception $e) {
                    $this->processException($e);
                    continue;
                }
            }
        }

        return ExitCode::OK;
    }

    /**
     * Непосредственная отправка запроса
     *
     * @param $url      string Url места назначения
     * @param $data     array  Данных для запроса
     * @param $method   string Метод запроса
     *
     * @return bool|Response
     */
    protected function makeRequest($url, $data, $method = 'POST')
    {
        /** @var Response $response */
        $response = $this->http->createRequest()
            ->setMethod($method)
            ->setUrl($url)
            ->setData($data)
            ->send();

        if ($response->isOk) {
            return $response;
        } else {
            $this->errorsCount ++;
            $this->stdout('Error: bad response code: ' . $response->getStatusCode()
                . ' . Raw content: ' . $response->toString() . PHP_EOL);
            return false;
        }
    }

    /**
     * Проверка на кол-во случившихся ошибок, для контроля заципливания
     *
     * @param $inserted int Кол-во уже вставленных записей
     * 
     * @return bool
     */
    protected function checkForErrorsLimit($inserted)
    {
        if ($this->errorsCount >= $this->maxErrors) {
            $this->stdout('Errors limit raised. Exiting. Inserted: ' . $inserted . ' records' . PHP_EOL);
            return false;
        }
        return true;
    }

    /**
     * Генерация экземпляра поста
     *
     * @return array
     */
    protected function getPostItem()
    {
        $userRand = rand(0, $this->usersMaxIndex);
        $ipRand = rand(0, $this->ipMaxIndex);

        return [
            'title'        => $this->faker->sentence(10),
            'content'      => $this->faker->text,
            'author_login' => ArrayHelper::getValue($this->users, $userRand, ''),
            'author_ip'    => ArrayHelper::getValue($this->ips, $ipRand, ''),
        ];
    }

    /**
     * Генерация данных для оценки
     *
     * @return array
     */
    protected function getMarkItem()
    {
        $postRand = rand(0, $this->maxPostIdsIndex);

        return [
            'mark'        => rand(1, 5),
            'post_id'    => ArrayHelper::getValue($this->postIds, $postRand, ''),
        ];
    }

    /**
     * Обработчик исключения
     *
     * @param \Exception $e
     */
    protected function processException(\Exception $e)
    {
        $this->errorsCount ++;
        $this->stdout('Error: ' . $e->getMessage() . PHP_EOL);
    }

    /**
     * Построение URL пункта назначения
     *
     * @param $destination
     *
     * @return string
     */
    protected function buildUrl($destination)
    {
        return trim($this->apiBaseUrl, '/') . (!empty($this->apiBaseUrlPort) ? ':' .$this->apiBaseUrlPort : '') . '/' . $destination;
    }
}
