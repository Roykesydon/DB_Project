<?php
// database connection will be here
// include database and object files
define('__ROOT__', dirname(dirname(__FILE__)));
require __ROOT__.'/JWTHandler.php';

class Auth extends JwtHandler{
    protected $db;
    protected $headers;
    protected $token;
    public function __construct($db,$headers) {
        parent::__construct();
        $this->db = $db;
        $this->headers = $headers;
    }

    public function isAuth(){
        if(array_key_exists('Authorization',$this->headers) && !empty(trim($this->headers['Authorization']))): // check headers have Authorization and is not empty (is token)
            $this->token = explode(" ", trim($this->headers['Authorization'])); // take out token
            if(isset($this->token[1]) && !empty(trim($this->token[1]))): // check token is not empty
                $data = $this->_jwt_decode_data($this->token[1]); // decode the token

                if(isset($data->user_ID)):
                    $user = $this->fetchUser($data->user_ID);
                    return $user;

                else:
                    return null;

                endif; // End of isset($this->token[1]) && !empty(trim($this->token[1]))
                
            else:
                return null;

            endif;// End of isset($this->token[1]) && !empty(trim($this->token[1]))

        else:
            return null;

        endif;
    }

    protected function fetchUser($user_id){
        try{
            $fetch_user_by_id = "SELECT * FROM `user` WHERE `user_ID`=:user_ID";
            $query_stmt = $this->db->prepare($fetch_user_by_id);
            $query_stmt->bindValue(':user_ID', $user_id,PDO::PARAM_INT);
            $query_stmt->execute();

            if($query_stmt->rowCount()):
                $row = $query_stmt->fetch(PDO::FETCH_ASSOC);
                return [
                    'success' => 1,
                    'status' => 200,
                    'user' => $row
                ];
            else:
                return null;
            endif;
        }
        catch(PDOException $e){
            return null;
        }
    }
}
?>