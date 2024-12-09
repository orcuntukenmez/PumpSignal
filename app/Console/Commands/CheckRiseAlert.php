<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;
use App\Helpers\SmsServices\Twilio; // SMS gönderimi için sınıfı dahil ediyoruz

class CheckRiseAlert extends Command
{
    /**
     * Komut adı.
     *
     * @var string
     */
    protected $signature = 'cryptos:check-rise-alert {rise_alert_interval} {column}';

    /**
     * Komut açıklaması.
     *
     * @var string
     */
    protected $description = 'Rise alert değeri belirli yüzdelik dilime uyan aktif kripto paralara mesaj gönderir ve {$column} sütununu günceller.';

    /**
     * Komut işlemini gerçekleştir.
     *
     * @return void
     */
    public function handle()
    {
        // Parametreleri alıyoruz
        $riseAlertInterval = $this->argument('rise_alert_interval'); // rise_alert_interval parametresi
        $column = $this->argument('column'); // {$column} sütunu parametresi

        // Rise alert interval değeri ve is_active değeri true olan kripto paraları alıyoruz
        $currencies = Currency::where('rise_alert_interval', $riseAlertInterval)
            ->where('is_active', true)
            ->get();

        // Tetiklenme yapılmadığını takip etmek için bir flag
        $triggered = false;

        // Tetikleme mesajlarını birleştirmek için bir değişken
        $alertMessages = [];

        // Her bir kripto parayı kontrol ediyoruz
        foreach ($currencies as $currency) {
            // Parametre olarak gelen {$column} sütunu değeri ile price arasındaki farkı kontrol et
            $oldPrice = $currency->{$column}; // {$column} sütununu parametreye göre alıyoruz
            $price = $currency->price;
            $riseAlert = $currency->rise_alert;

            // Eğer sütun değeri 0 ise ve price değeri 0 ise özel bir mesaj tetikle
            if ($oldPrice === 0 && $price === 0) {
                // burada bir şey yapmıyoruz
            }

            // Eğer sütun değeri 0 ve price değeri 0'dan farklı ise, değer 0'dan yukarı çıkmış demektir
            elseif ($oldPrice === 0 && $price !== 0) {
                // $alertMessages[] = "Kripto para {$currency->name} ({$currency->symbol}) değeri 0'dan yukarı çıkmış.";
                // $triggered = true;
            }

            // Eğer price değeri 0 ve sütun değeri 0 değilse, kripto para değeri 0'a düşmüş demektir
            elseif ($oldPrice !== 0 && $price === 0) {
                // $alertMessages[] = "Kripto para {$currency->name} ({$currency->symbol}) değeri 0'a düşmüş.";
                // $triggered = true;
            }

            // Eğer herhangi bir sütun veya price null ise, geçersiz durum mesajı
            elseif ($oldPrice === null || $price === null) {
                // burada bir şey yapmıyoruz
            }

            // Eğer price ve sütun değerleri geçerliyse ve artış varsa, rise alert mesajı tetiklenir
            elseif ($oldPrice !== null && $price !== null && $riseAlert !== null) {
                $percentageDifference = (($price - $oldPrice) / $oldPrice) * 100;

                // Eğer price değeri rise_alert kadar artmışsa, mesaj gönderilecek
                if ($percentageDifference >= $riseAlert) {
                    $alertMessages[] = "Kripto para {$currency->name} ({$currency->symbol}) fiyatı, %{$riseAlert} oranında artış gösterdi.";
                    $triggered = true;
                }
            }

            // {$column} sütununu price ile güncelliyoruz
            $currency->update([$column => $price]);
            $this->info("{$column} sütunu güncellendi: {$currency->name} - Yeni {$column} değeri: {$price}");
        }

        // Eğer tetiklenme olduysa, tüm tetikleme mesajlarını birleştirip tek bir mesaj olarak göndereceğiz
        if ($triggered) {
            // Tetikleme mesajlarını birleştiriyoruz
            $alertMessage = implode(' ', $alertMessages);

            // SMS gönderme işlemi
            // Örnek: SMS gönderme (bu kısmı kendi Twilio sınıfınıza göre değiştirebilirsiniz)
            $smsService = new Twilio();
            $smsService->sendWhatsApp('+905444524027', $alertMessage);

            // Bilgi mesajı gösteriyoruz
            $this->info($alertMessage);
        } else {
            $this->info("Hiçbir kripto para için Rise Alert tetiklenmedi.");
        }
    }
}
