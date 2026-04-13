<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\EmailJob;
use App\Core\Flash;
use App\Core\RedisManager;

class QueueManagerController
{
    public function index()
    {
        $jobs = EmailJob::orderBy('id', 'desc')->limit(100)->get();
        echo View::render('admin.queue.index', ['jobs' => $jobs]);
    }

    public function retry($id)
    {
        $job = EmailJob::find($id);

        if ($job && $job->status === 'failed') {
            $job->update(['status' => 'pending']);
            
            // Re-push to Redis
            $redis = RedisManager::getClient();
            $redis->rpush('email_queue', $job->id);

            Flash::success("Job #{$job->id} reenfileirado para envio!");
        } else {
            Flash::error("Job inválido ou já processado.");
        }

        header('Location: /admin/queue');
        exit;
    }
}
