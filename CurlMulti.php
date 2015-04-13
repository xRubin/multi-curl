<?php

class CurlMulti
{
    /**
     * @var resource
     * служебный указатель на ресурс
     */
    private $handle = null;
    /**
     * список задач для модуля
     * @var CurlTask[]
     */
    private $tasks;
    /**
     * список задач для модуля
     * @var resource[]
     */
    private $resources;

    /**
     * @var integer
     */
    static protected $id = 0;

    public function __construct()
    {
        $this->handle = curl_multi_init();
        $this->tasks = array();
    }

    public function __destruct()
    {
        // не забываем закрывать дескрипторы
        curl_multi_close($this->handle);
    }

    /**
     * @param CurlTask $task
     */
    public function addTask(CurlTask $task)
    {
        static::$id++;
        $this->tasks[static::$id] = $task;
        $ch = curl_init();
        curl_setopt_array($ch, $task->options);
        curl_multi_add_handle($this->handle, $ch);
        $this->resources[static::$id] = $ch;
    }

    public function getTasksCnt()
    {
        return count($this->tasks);
    }

    // $callback будет вызываться при завершении любого запроса из списказадач
    public function run()
    {
        do {
            while (($execrun = curl_multi_exec($this->handle, $running)) == CURLM_CALL_MULTI_PERFORM) ;
            if ($execrun != CURLM_OK)
                break;
            while ($done = curl_multi_info_read($this->handle)) {
                $id = array_search($done['handle'], $this->resources);
                $content =curl_multi_getcontent($done['handle']);
                $info = curl_getinfo($done['handle']);
                curl_multi_remove_handle($this->handle, $done['handle']);
                /**
                 * дальше магия. причем официальная.
                 *
                 * if (ch->uses) {
                 *     ch->uses--;
                 * } else {
                 *     zend_list_delete(Z_LVAL_P(zid));
                 * }
                 */
                curl_close($done['handle']);
                curl_close($done['handle']);
                call_user_func($this->tasks[$id]->callback, $content, $info);
            }
        } while ($running);
    }

}
