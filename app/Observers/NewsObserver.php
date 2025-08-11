<?php

namespace App\Observers;

use App\Models\News;
use App\Jobs\PublishNewsToTelegramJob;
use Illuminate\Support\Facades\Log;

class NewsObserver
{
    /**
     * Handle the News "created" event.
     */
    public function created(News $news): void
    {
        Log::info('NewsObserver: диспатчим публикацию новости (created)', [
            'news_id' => $news->getKey(),
        ]);
        PublishNewsToTelegramJob::dispatch($news->getKey());
    }

    /**
     * Handle the News "updated" event.
     */
    public function updated(News $news): void
    {
        Log::info('NewsObserver: публикация после обновления', [
            'news_id' => $news->getKey(),
        ]);
        PublishNewsToTelegramJob::dispatch($news->getKey());
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
