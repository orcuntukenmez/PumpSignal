<?php

namespace App\Console\Commands;

use App\Helpers\Apis\CoinCap;
use Illuminate\Console\Command;

class FetchCryptos extends Command
{
    /**
     * Komut adı.
     *
     * @var string
     */
    protected $signature = 'cryptos:fetch';

    /**
     * Komut açıklaması.
     *
     * @var string
     */
    protected $description = 'CoinCap API\'den kripto para verilerini çek ve kaydet';

    /**
     * Komut işlemini gerçekleştir.
     */
    public function handle()
    {
        // CoinCap sınıfından bir örnek al
        $coinCap = new CoinCap();

        // FetchAndStoreCryptos fonksiyonunu çalıştır ve veriyi al
        $cryptos = $coinCap->FetchAndStoreCryptos();

        // Veriyi ekrana yazdır
        $this->info('Kripto paralar başarıyla güncellendi.');

        // Ekstra olarak gelen kripto para verisini ekrana yazdır
        foreach ($cryptos as $index => $crypto) {
            $this->line(($index + 1) . ". Name: {$crypto['name']} - Symbol: {$crypto['symbol']} - Price: {$crypto['priceUsd']} USD");
        }
    }
}
