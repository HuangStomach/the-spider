<?php
namespace Task;

class Status {

    public function http ($record) {
        $server = $record->server;

        // 如果server并没有被记录或者server的最后更新时间比记录时间要新
        // 不做广播
        if (!$server->id || !$server->report 
        || strtotime($record->time) < strtotime($server->update)) return;
        
        $server->status = $record->level();
        if ($server->save()) {
            // do sth
        }
    }

}