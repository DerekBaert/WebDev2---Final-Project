<?php

    function verifyConsole($consoleId)
    {

        $validId = false;
        $name = '';
        switch($consoleId)
        {
            case 49:
                $validId = true;
                $name = 'Xbox One';
                break;
            case 169:
                $validId = true;
                $name = 'Xbox Series';
                break;
            case 48:
                $validId = true;
                $name = 'Playstation 4';
                break;
            case 167:
                $validId = true;
                $name = 'Playstation 5';
                break;
            case 130:
                $validId = true;
                $name = 'Nintendo Switch';
                break;
            case 6:
                $validId = true;
                break;
            default:
                break;
        }
                
        return ['valid' => $validId, 'console' => $name];
    }

    function commaList($array, $key)
    {
        $return = '';
        $first = true;
        foreach($array as $item)
        {
            if($first)
            {
                $return .= $item[$key];
                $first = false;
            }
            else
            {
                $return .= ', ' . $item[$key];
            }               
        }
        return $return;                            
    }

    function getRating($ratingCode)
    {
        $rating = '';
        switch($ratingCode)
        {
            case 1:
                $rating = 'PEGI: Three';
                break;
            case 2:
                $rating = 'PEGI: Seven';
                break;
            case 3: 
                $rating = 'PEGI: Twelve';        
                break;
            case 4:
                $rating = 'PEGI: Sixteen';
                break;
            case 5:
                $rating = 'PEGI: Eighteen';
                break;
            case 6:
                $rating = 'ESRB: Rating Pending';
                break;
            case 7:
                $rating = 'ESRB: Early Childhood';
                break;
            case 8:
                $rating = 'ESRB: Everyone';
                break;
            case 9: 
                $rating = 'ESRB: Everyone 10+';
                break;
            case 10:
                $rating = 'ESRB: Teen';
                break;
            case 11:
                $rating = 'ESRB: Mature';
                break;
            case 12:
                $rating = 'ESRB: Adults Only';
                break;
            default:
                $rating = '[Unknown Rating]';
                break;
        }

        return $rating;
    }

    function getToken($data, $url)
    {
        # Create a connection
        $ch = curl_init($url);
        # Form data string
        $postString = http_build_query($data, '', '&');
        # Setting our options
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        # Get the response
        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response, true);
        return $json;   
    }
?>