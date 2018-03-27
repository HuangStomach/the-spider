<?php

namespace Gini\Model\Record;

class Host
{
    /**
     * host监控记录保存触发修改状态和广播事件
     *
     * @param [\Gini\Event] $e
     * @param [\Gini\ORM\Record\Host] $record
     * @return void
     */
    public static function afterSave ($e, $record) {
        if (!$record->id) return false;
        $site = $record->site;
        if (!$site->id) return false;

        /**
         * 当:
         * 记录创建时间早于监控客户端最后更新时间时
         * 不做广播
         */
        
        if (strtotime($record->ctime) < strtotime($site->update)) return;
        
        $site->status = $record->level();
        if ($site->save()) {
            // do sth
        }
    }

    public static function hook ($e, $server) {
        foreach ($server->table as $row) {
            $server->push($row['fd'], 'boardcast'); // 消息广播
        }
    }
}