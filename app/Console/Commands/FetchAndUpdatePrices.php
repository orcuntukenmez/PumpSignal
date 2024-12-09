<?php

namespace App\Console\Commands;

use App\Helpers\Apis\CoinCap;
use Illuminate\Console\Command;

class FetchAndUpdatePrices extends Command
{
    /**
     * Komut adı.
     *
     * @var string
     */
    protected $signature = 'cryptos:update-prices';

    /**
     * Komut açıklaması.
     *
     * @var string
     */
    protected $description = 'CoinCap API\'den kripto para fiyatlarını al ve güncelle';

    /**
     * Komut işlemini gerçekleştir.
     */
    public function handle()
    {
        // CoinCap sınıfından bir örnek al
        $coinCap = new CoinCap();

        // FetchAndUpdatePrices fonksiyonunu çalıştır ve veriyi al
        $updatedCryptos = $coinCap->FetchAndUpdatePrices();

        // Veriyi ekrana yazdır
        $this->info('Kripto para fiyatları başarıyla güncellendi.');

        // Ekstra olarak gelen kripto para fiyatlarını ekrana yazdır
        foreach ($updatedCryptos as $index => $crypto) {
            $this->line(($index + 1) . ". Name: {$crypto['name']} - Symbol: {$crypto['symbol']} - Price: {$crypto['priceUsd']} USD");
        }
    }
}
