<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UtilityBill;
use App\Events\NewNotificationEvent;
use Carbon\Carbon;

class SendUtilityBillReminders extends Command
{
    protected $signature = 'bills:remind';
    protected $description = 'Send reminders for utility bills due in 5 days';

    // public function handle()
    // {
    //     $targetDate = Carbon::today()->addDays(5);

    //     $bills = UtilityBill::whereDate('due_date', $targetDate)->get();

    //     if ($bills->isEmpty()) {
    //         $this->info("No bills due in 5 days.");
    //         return;
    //     }

    //     foreach ($bills as $bill) {
    //         $data = [
    //             'title' => 'Utility Bill Reminder',
    //             'message' => "{$bill->name} bill is due on {$bill->due_date->format('d M, Y')}.",
    //             'type' => 'utility_bill_reminder',
    //             'bill_id' => $bill->id,
    //         ];

    //         try {
    //             event(new NewNotificationEvent($data));
    //             $this->info("Reminder sent for Bill ID: {$bill->id}");
    //         } catch (\Exception $e) {
    //             $this->error("Failed for Bill ID: {$bill->id} - " . $e->getMessage());
    //         }
    //     }
    // }

    public function handle()
    {
        $data = [
            'title' => '🔔 Test Notification',
            'message' => 'This is a test broadcast from Laravel at ' . now()->format('H:i:s'),
            'type' => 'utility_bill_reminder',
            'bill_id' => rand(1, 100),
        ];

        try {
            event(new \App\Events\NewNotificationEvent($data));
            $this->info("✅ Test notification sent successfully at " . now());
        } catch (\Exception $e) {
            $this->error("❌ Failed to send test notification: " . $e->getMessage());
        }
    }
}
