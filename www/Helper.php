<?php

    class Helper{

        static function getIpAdress(){
            $ip = $_SERVER["REMOTE_ADDR"];
            return $ip;
        }

        static function getUserAgent(){
            $userAgent = $_SERVER["HTTP_USER_AGENT"];
            return $userAgent;
        }

        static function getDevice(){
            $userAgent  = self::getUserAgent();
            $isMobile   = is_numeric(strpos(strtolower($userAgent), "mobile"));
            $isTablet   = is_numeric(strpos(strtolower($userAgent), "tablet"));
            return $isTablet ? "Tablet" : ($isMobile ? "Mobile" : "Desktop");
        }

        static function getSourceValue($channel, $provider){
            $default_codes = [
                "tran"  => "6130",
                "sure5" => "606319",
                "eq"    => "SEM_Tier_3",
                "qw"    => "C31562",
            ];
            return $default_codes[$provider];
        }

        static function getAgeByBirthParams($d,$m,$y){
            if (!empty($d) && !empty($m) && !empty($y)){
                $diff = date( 'Ymd' ) - date( 'Ymd', strtotime("$d-$m-$y") );
                return substr( $diff, 0, -4 );    
            }
            return "";
        }

        public static function getAlphaDOBByBirthParams($data)
        {
            if (empty($data['birthYear']) || empty($data['birthMonth']) || empty($data['birthDay'])) {
                return null;
            }
            $year = $data['birthYear'];
            $month = (int)$data['birthMonth'] < 10 ? '0' . $data['birthMonth'] : $data['birthMonth'];
            $day = (int)$data['birthDay'] < 10 ? '0' . $data['birthDay'] : $data['birthDay'];

            return $year . '-' . $month . '-' . $day;
        }

        public static function sendPostApi($url, $request, $postFields, $httpHeader)
        {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_CONNECTTIMEOUT => 40,
                CURLOPT_TIMEOUT => 40,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $request,
                CURLOPT_POSTFIELDS => $postFields,
                CURLOPT_HTTPHEADER => $httpHeader,
            ));
    
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpCode !== 200) {
                $responseTextError = curl_error($curl);
            }
    
            curl_close($curl);
            return $response;
        }

    }
?>