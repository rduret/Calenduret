<?php

namespace App\Service\Utils;

/**
 * Class CaptchaVerify
 * @package App\Service\Utils
 */
class CaptchaVerify
{
    public function checkCaptcha(string $token): bool
    {
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=6Lc14vYoAAAAAL8Eu6cY4ob_Zly7xCOXeTVKlLlG&response=$token";
    
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);
    
        $result = curl_exec($curl);
    
        curl_close($curl);
    
        if(is_string($result)) {
            $result = json_decode($result, true);
            return $result['success'] && $result['score'] >= 0.5;
        } else {
            return false;
        }
    }
}