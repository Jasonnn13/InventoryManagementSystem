<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Mail;
use App\Mail\OverduePaymentMail;

class CheckOverduePayments extends Command
{
    protected $signature = 'payments:check-overdue';

    protected $description = 'Check for overdue payments and send email notifications';

    public function handle()
    {
        // Check for overdue payments
        $overduePenjualans = Penjualan::where('tenggat_waktu', '<', now())
            ->where('status', 'Belum Lunas')
            ->get();

        foreach ($overduePenjualans as $penjualan) {
            $this->sendOverdueEmail($penjualan);
        }

        $this->info('Overdue payments checked and emails sent if necessary.');

        
    }

    protected function sendOverdueEmail($penjualan)
    {
        Mail::send(new OverduePaymentMail($penjualan));
    }
}
