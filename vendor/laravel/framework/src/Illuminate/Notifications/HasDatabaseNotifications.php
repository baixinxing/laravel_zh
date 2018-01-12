<?php

namespace Illuminate\Notifications;

trait HasDatabaseNotifications
{
    /**
     * 获取实体的通知。
     */
    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')
                            ->orderBy('created_at', 'desc');
    }

    /**
     * 获取实体的读取通知。
     */
    public function readNotifications()
    {
        return $this->notifications()
                            ->whereNotNull('read_at');
    }

    /**
     * 获取实体的未读通知。
     */
    public function unreadNotifications()
    {
        return $this->notifications()
                            ->whereNull('read_at');
    }
}
