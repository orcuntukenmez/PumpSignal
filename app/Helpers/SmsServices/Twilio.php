<?php

namespace App\Helpers\SmsServices;

use Twilio\Rest\Client;

class Twilio
{
    protected $sid;
    protected $authToken;
    protected $from;
    protected $from_whatsapp;

    public function __construct()
    {
        // Twilio API bilgilerini env dosyasından alıyoruz
        $this->sid = env('TWILIO_SID');
        $this->authToken = env('TWILIO_AUTH_TOKEN');
        $this->from = env('TWILIO_PHONE_NUMBER'); // Twilio numaranız
        $this->from_whatsapp = env('TWILIO_WHATSAPP');
    }

    /**
     * SMS gönderme fonksiyonu
     *
     * @param string $to - Alıcı telefon numarası
     * @param string $message - Gönderilecek mesaj
     * @return string - SMS gönderme sonucu
     */
    public function sendSms(string $to, string $message): string
    {
        try {
            // Twilio API istemcisini oluşturuyoruz
            $client = new Client($this->sid, $this->authToken);

            // SMS gönderiyoruz
            $message = $client->messages->create(
                $to, // Alıcı telefon numarası
                [
                    'from' => $this->from, // Twilio numaranız
                    'body' => $message, // Gönderilecek mesaj
                ]
            );

            // Başarıyla gönderildiğinde dönecek mesaj
            return "SMS başarıyla gönderildi: {$message->sid}";
        } catch (\Exception $e) {
            // Hata durumu
            return "SMS gönderme hatası: " . $e->getMessage();
        }
    }

    /**
     * WhatsApp üzerinden mesaj gönderme fonksiyonu
     *
     * @param string $to - Alıcı telefon numarası (whatsapp:<numara>)
     * @param string $message - Gönderilecek mesaj
     * @return string - WhatsApp mesaj gönderme sonucu
     */
    public function sendWhatsApp(string $to, string $message): string
    {
        try {
            // Twilio API istemcisini oluşturuyoruz
            $client = new Client($this->sid, $this->authToken);

            // WhatsApp mesajı gönderiyoruz
            $message = $client->messages->create(
                "whatsapp:{$to}", // Alıcı telefon numarası whatsapp: ile başlamalı
                [
                    'from' => "whatsapp:{$this->from_whatsapp}", // Twilio WhatsApp numaranız
                    'body' => $message, // Gönderilecek mesaj
                ]
            );

            // Başarıyla gönderildiğinde dönecek mesaj
            return "WhatsApp mesajı başarıyla gönderildi: {$message->sid}";
        } catch (\Exception $e) {
            // Hata durumu
            return "WhatsApp mesajı gönderme hatası: " . $e->getMessage();
        }
    }
}
