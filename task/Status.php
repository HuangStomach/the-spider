<?php
namespace Task;

class Status {

    public function http ($record) {
        $server = $record->server;
        if (!$server->id || !$server->report 
        || strtotime($record->time) < strtotime($server->update)) return;

        $server->status = $record->level();
        if ($server->save()) {
            // do sth
        }
    }

}