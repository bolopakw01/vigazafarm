<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penetasan;
use Carbon\Carbon;

class UpdatePenetasanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'penetasan:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status penetasan dari proses ke aktif setelah 1 hari';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // Update status dari 'proses' ke 'aktif' jika sudah lebih dari 1 hari
        $updated = Penetasan::where('status', 'proses')
            ->whereDate('dibuat_pada', '<=', $now->copy()->subDay())
            ->update(['status' => 'aktif']);
        
        $this->info("✅ Berhasil update {$updated} data penetasan dari status 'proses' ke 'aktif'");
        
        return Command::SUCCESS;
    }
}
