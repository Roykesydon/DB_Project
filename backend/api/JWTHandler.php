<?php
require dirname(__FILE__).'/JWT/JWT.php';
require dirname(__FILE__).'/JWT/ExpiredException.php';
require dirname(__FILE__).'/JWT/SignatureInvalidException.php';
require dirname(__FILE__).'/JWT/BeforeValidException.php';

use \Firebase\JWT\JWT;

class JwtHandler {
    protected $jwt_secrect;
    protected $token;
    protected $issuedAt;
    protected $expire;
    protected $jwt;

    public function __construct()
    {
        // set your default time-zone
        date_default_timezone_set('Asia/Taipei');
        $this->issuedAt = time();
        
        // Token Validity (3600 second = 1hr)
        $this->expire = $this->issuedAt + 3600;

        // Set your secret or signature
        $this->jwt_secrect = "the_greatest_rent_room_website_in_taiwan";  
    }

    public function getSecret(){
        return $this->jwt_secrect;
    }

    // ENCODING THE TOKEN
    public function _jwt_encode_data($iss,$data){

        $this->token = array(
            //Adding the identifier to the token (who issue the token)
            "iss" => $iss,
            "aud" => $iss,
            // Adding the current timestamp to the token, for identifying that when the token was issued.
            "iat" => $this->issuedAt,
            // Token expiration
            "exp" => $this->expire,
            // Payload
            "data"=> $data
        );

        $this->jwt = JWT::encode($this->token, $this->jwt_secrect);
        return $this->jwt;

    }

    protected function _errMsg($msg){
        return [
            "auth" => 0,
            "message" => $msg
        ];
    }
    
    //DECODING THE TOKEN
    public function _jwt_decode_data($jwt_token){
        try{
            $decode = JWT::decode($jwt_token, $this->jwt_secrect, array('HS256'));
            return $decode->data;
        }
        catch(\Firebase\JWT\ExpiredException $e){
            return $e->getMessage();
        }
        catch(\Firebase\JWT\SignatureInvalidException $e){
            return $e->getMessage();
        }
        catch(\Firebase\JWT\BeforeValidException $e){
            return $e->getMessage();
        }
        catch(\DomainException $e){
            return $e->getMessage();
        }
        catch(\InvalidArgumentException $e){
            return $e->getMessage();
        }
        catch(\UnexpectedValueException $e){
            return $e->getMessage();
        }

    }
}
?>