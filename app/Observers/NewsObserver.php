<?php

namespace App\Observers;

use App\Models\News;
use App\Jobs\PublishNewsToTelegramJob;

class NewsObserver
{
    /**
     * Handle the News "created" event.
     */
    public function created(News $news): void
    {
        // Публикуем только если новость отмечена как опубликованная или поле отсутствует
        if (property_exists($news, 'is_published')) {
            if ((bool) $news->getAttribute('is_published') !== true) {
                return;
            }
        }

        PublishNewsToTelegramJob::dispatch($news->getKey());
    }

    /**
     * Handle the News "updated" event.
     */
    public function updated(News $news): void
    {
        // Если поле is_published изменилось с false на true — публикуем
        if ($news->wasChanged('is_published') && (bool) $news->getAttribute('is_published') === true) {
            PublishNewsToTelegramJob::dispatch($news->getKey());
        }
    }

    /**
     * Handle the News "deleted" event.
     */
    public function deleted(News $news): void
    {
        //
    }

    /**
     * Handle the News "restored" event.
     */
    public function restored(News $news): void
    {
        //
    }

    /**
     * Handle the News "force deleted" event.
     */
    public function forceDeleted(News $news): void
    {
        //
    }
}
