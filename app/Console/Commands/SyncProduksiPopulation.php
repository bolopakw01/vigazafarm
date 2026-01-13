<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncProduksiPopulation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-produksi-population';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync jumlah_indukan to match jumlah_jantan + jumlah_betina for all production records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $produksiRecords = \App\Models\Produksi::where('tipe_produksi', 'puyuh')->get();

        $updated = 0;
        foreach ($produksiRecords as $produksi) {
            $calculatedTotal = ($produksi->jumlah_jantan ?? 0) + ($produksi->jumlah_betina ?? 0);
            if ($produksi->jumlah_indukan != $calculatedTotal) {
                $produksi->jumlah_indukan = $calculatedTotal;
                $produksi->save();
                $updated++;
            }
        }

        $this->info("Synced {$updated} production records.");
    }
}
