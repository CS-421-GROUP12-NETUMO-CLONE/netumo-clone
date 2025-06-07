<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\Status;
use App\Models\Target;
use App\Notifications\SendAlertNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckTargetStatus implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public  $target;
    /**
     * Create a new job instance.
     */
    public function __construct(Target $target)
    {
        $this->target = $target;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $start = microtime(true);

            $response = Http::timeout(10)->get($this->target->url);

            $latency = round((microtime(true) - $start) * 1000, 2); // in ms
            $statusCode = $response->status();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $latency = 0;
            $statusCode = 0; // 0 indicates failure
        }

        Status::create([
            'target_id' => $this->target->id,
            'status_code' => $statusCode,
            'latency' => $latency,
            'checked_at' => Carbon::now(),
        ]);

        $failures = Status::where('target_id', $this->target->id)
            ->latest()->take(2)->pluck('status_code')->all();

        if ($failures === [0, 0]) {
            $message = "Downtime alert: {$this->target->url} failed twice in a row.";
            Alert::firstOrCreate([
                'target_id' => $this->target->id,
                'type' => 'downtime',
                'message' => $message
            ]);
            Notification::route('mail', 'admin@example.com')
                ->notify(new SendAlertNotification($message));
        }
    }
}
