<?php

namespace App\Helpers\Apis;

use App\Models\Currency;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class CoinCap
{
    /**
     * CoinCap API'sinden tüm kripto para verilerini alır ve veritabanına kaydeder.
     */
    public function FetchAndStoreCryptos(): array
    {
        $url = 'https://api.coincap.io/v2/assets';
        $limit = 2000;
        $offset = 0;
        $allCryptos = [];

        try {
            do {
                // GET isteği gönder
                $response = Http::get($url, [
                    'limit' => $limit,
                    'offset' => $offset,
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data['data'])) {
                        // Gelen verileri işleyerek kaydet
                        $cryptosToInsert = [];

                        foreach ($data['data'] as $crypto) {
                            $cryptosToInsert[] = [
                                'unique_id' => $crypto['id'],
                                'symbol' => $crypto['symbol'],
                                'name' => $crypto['name'],
                                'price' => $crypto['priceUsd'],
                                'is_active' => false, // Varsayılan olarak aktif yapılıyor
                                'm1' => $crypto['priceUsd'],
                                'm5' => $crypto['priceUsd'],
                                'm15' => $crypto['priceUsd'],
                                'm30' => $crypto['priceUsd'],
                                'h1' => $crypto['priceUsd'],
                                'h4' => $crypto['priceUsd'],
                                'h12' => $crypto['priceUsd'],
                                'd1' => $crypto['priceUsd'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        // Veritabanına topluca insert veya update yap
                        Currency::upsert($cryptosToInsert, ['unique_id'], ['symbol', 'name', 'price', 'is_active', 'updated_at']);
                    }

                    // Gelen tüm veriyi birleştir
                    $allCryptos = array_merge($allCryptos, $data['data']);
                    // Yeni offset hesapla
                    $offset += $limit;

                    // Eğer daha fazla veri yoksa döngüyü sonlandır
                    if (count($data['data']) < $limit) {
                        break;
                    }
                } else {
                    logger()->error('CoinCap API başarısız yanıt: ' . $response->status());
                    break;
                }
            } while (true);
        } catch (\Exception $e) {
            die($e);
            logger()->error('CoinCap API Hatası: ' . $e->getMessage());
        }

        // Ek olarak dönen veriyi log'la ve ekrana yazdır
        logger()->info('Alınan kripto verileri:', $allCryptos);

        return $allCryptos;
    }

    /**
     * CoinCap API'sinden tüm kripto para verilerini alır ve sadece fiyatlarını günceller.
     */
    public function FetchAndUpdatePrices(): array
    {
        $url = 'https://api.coincap.io/v2/assets';
        $limit = 2000;
        $offset = 0;
        $updatedCryptos = [];

        try {
            do {
                $response = Http::get($url, [
                    'limit' => $limit,
                    'offset' => $offset,
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data['data'])) {
                        $casesPrice = [];
                        $ids = [];

                        foreach ($data['data'] as $crypto) {
                            $id = addslashes($crypto['id']); // Özel karakterlere karşı koruma
                            $price = floatval($crypto['priceUsd']); // Sayısal değer
                            $casesPrice[] = "WHEN unique_id = '{$id}' THEN {$price}";
                            $ids[] = "'{$id}'";
                        }

                        // SQL sorgusunu oluştur
                        $query = "
                        UPDATE currencies
                        SET
                            price = CASE
                                " . implode(" ", $casesPrice) . "
                            END,
                            updated_at = NOW()
                        WHERE unique_id IN (" . implode(",", $ids) . ")
                    ";

                        DB::statement($query);
                    }

                    $updatedCryptos = array_merge($updatedCryptos, $data['data']);
                    $offset += $limit;

                    if (count($data['data']) < $limit) {
                        break;
                    }
                } else {
                    logger()->error('CoinCap API başarısız yanıt: ' . $response->status());
                    break;
                }
            } while (true);
        } catch (\Exception $e) {
            logger()->error('CoinCap API Hatası: ' . $e->getMessage());
        }

        logger()->info('Güncellenen kripto fiyatları:', $updatedCryptos);

        return $updatedCryptos;
    }


}
