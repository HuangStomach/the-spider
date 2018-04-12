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
    public static function afterSave ($e, $server, $record) {
        if (!$record->id) return false;
        $site = $record->site;
        if (!$site->id) return false;

        /**
         * 当:
         * 记录创建时间早于监控客户端最后更新时间时
         * 不做广播
         */
        $last = those('record/host')->whose('site')->is($site)
            ->orderBy('last', 'desc')->current()->last;
        if (strtotime($last) < strtotime($site->update)) return;

        // 更新各个插件的报警情况
        $siteLevel = a('site/level')->whose('site')->is($site);
        $refresh = $siteLevel->host != $record->level();
        if ($refresh) $siteLevel->host = $record->level();
        $siteLevel->save();
        
        // 然后再去更新站点的情况
        $site->level = $site->level();
        $site->status = $record->state;
        $site->update = $last;

        if ($site->save() && $refresh) {
            // 当报警level出现变化时 进行广播
            if ($server->count()) foreach ($server->table as $row) {
                $server->push($row['fd'], 'boardcast'); // 等待具体广播内容
            }
        }
    }

    /**
     * Undocumented function
     *
     * @param [type] $e
     * @param [type] $server
     * @return void
     */
    public static function hook ($e, $server) {
        foreach ($server->table as $row) {
            $server->push($row['fd'], 'boardcast'); // 消息广播
        }
    }
}