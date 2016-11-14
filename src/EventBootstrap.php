<?php
namespace tmukherjee13\eventHandler;

use yii\base\BootstrapInterface;
use yii\base\Application;

class EventBootstrap implements BootstrapInterface
{
    /**
     * @var EventHandler EventHandler memory storage for getEventHandler method
     */
    protected static $_eventHandler;
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        self::getEventHandler($app);
    }
    /**
     * finds and creates app event manager from its settings
     * @param Application $app yii app
     * @return EventManager app event manager component
     * @throws Exception Define event manager
     */
    public static function getEventHandler($app)
    {
        if (self::$_eventHandler) {
            return self::$_eventHandler;
        }
        foreach ($app->components as $name => $config) {
            $class = is_string($config) ? $config : @$config['class'];
            if($class == str_replace('Bootstrap', 'Manager', get_called_class())){
                return self::$_eventHandler = $app->$name;
            }
        }
        $eventFile = \Yii::getAlias('@app/config/_events.php');
        $app->setComponents([
            'eventHandler' => [
                'class'  => 'tmukherjee13\eventHandler\EventHandler',
                'events' => file_exists($eventFile) && is_file($eventFile)
                    ? include $eventFile 
                    : []
            ],
        ]);
        return self::$_eventHandler = $app->eventHandler;
    }
}