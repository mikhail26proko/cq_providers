<?php

    require_once "interface.php";

    abstract class Provider implements providerInterface
{
    public      $funnelFormData = [];
    public      $requestLead = [];
    public      $providerImpression = [];
    public      $providerData = [];
    public      $test = false;

    public function __construct($funnelFormData)
    {

        $this->funnelFormData = $funnelFormData['data'] ?? [];
        $this->test           = (boolean)(!empty($funnelFormData['data']['test']) && (int)$funnelFormData['data']['test'] == 1 ? 1 : 0);

        /* Start step by step get impression */
        $this->translationDataSend();
        $this->impressionRequest();
        $this->translationDataRequest();
        /* End step by step get impression */

    }

    public function sendError($message)
    {
        $body = [
            'message' => $message ?? '',
            'debug' => $this->debug()
        ];

        // send data method ?
    }

    public function debug(...$fields)
    {
        if (!empty($fields)) {
            $response = [];
            foreach ($fields as $field) {
                if (isset($this->$field)) {
                    $response[$field] = $this->$field;
                }
            }

            return $response;
        }
        $response = get_object_vars($this);

        if(!empty($this->providerUrl)) {
            $response['providerUrl'] = $this->providerUrl;
        } else {
            $response['providerUrl'] = getenv('url');
        }

        return $response;
    }

    public function errorData($fuild){
        echo "Fuild $fuild - is empty!";
        die;
    }

}


?>