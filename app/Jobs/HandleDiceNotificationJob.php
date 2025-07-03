<?php

namespace App\Jobs;

use App\Http\Controllers\DiceController;
use App\Traits\TraitsUtil;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Ramsey\Uuid\Nonstandard\Uuid;

class HandleDiceNotificationJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dice_id;
    protected $diceController;
    protected $title;
    protected $chat_id;


    public function __construct($dice_id, $title, $chat_id, DiceController $diceController)
    {
        $this->dice_id = $dice_id;
        $this->diceController = $diceController;
        $this->title = $title;
        $this->chat_id = $chat_id;

    }


    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {

        Log::info("Start HandleDiceNotificationJob "
            . $this->dice_id . Carbon::now()->format('Y-m-d H:i:s.')
            . sprintf('%03d', Carbon::now()->microsecond / 1000));

        if ($this->chat_id == null) {
            Log::info("Finished  HandleDiceNotificationJob with no chat id" . Carbon::now()->format('Y-m-d H:i:s.')
                . sprintf('%03d', Carbon::now()->microsecond / 1000));
            return;
        }

        $sessions = $this->diceController->get($this->dice_id);


        $botToken = '7489917462:AAFjYo08cr1sjng52Nk2H70ZHAi731XpcVc';
        $htmlContent = View::make('dice.show-file', compact('sessions'))->render();

        Http::attach(
            'document', $htmlContent, $this->title . ' (' . (new \DateTime())->format('Uv') . ').html'
        )->post("https://api.telegram.org/bot{$botToken}/sendDocument", [
            'chat_id' => $this->chat_id,
        ]);

        Log::info("Finished  HandleDiceNotificationJob" . Carbon::now()->format('Y-m-d H:i:s.') . sprintf('%03d', Carbon::now()->microsecond / 1000));

    }

    public function failed($exception)
    {
        Log::error('Job failed: HandleDiceNotificationJob' . $exception->getMessage());
    }


}
