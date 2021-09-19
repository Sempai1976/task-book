<?php
if (!function_exists('get_base_url')) {
	function get_base_url() {
		$base_url = (empty($_SERVER['HTTPS']) OR strtolower($_SERVER['HTTPS']) === 'off') ? 'http' : 'https';
        $base_url .= '://'. $_SERVER['HTTP_HOST'];
        $base_url .= substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], basename($_SERVER['SCRIPT_FILENAME'])));
	    
	    return $base_url;
	}
}

if (!function_exists('get_current_url')) {
	function get_current_url() {
		$current_url = sprintf("%s://%s%s",
	        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
	        $_SERVER['SERVER_NAME'],
	        $_SERVER['REQUEST_URI']
	    );
	    
	    return $current_url;
	}
}

if (!function_exists('get_user_ip')) {
    function get_user_ip() {
        if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '' ) {
            $client_ip =
            ( !empty($_SERVER['REMOTE_ADDR']) ) ?
               $_SERVER['REMOTE_ADDR']
               :
               ( ( !empty($_ENV['REMOTE_ADDR']) ) ?
                  $_ENV['REMOTE_ADDR']
                  :
                  "unknown" );
            $entries = explode('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);

            reset($entries);
            while (list(, $entry) = each($entries))
            {
                $entry = trim($entry);
                if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list) )
                {
                    $private_ip = array(
                        '/^0\./',
                        '/^127\.0\.0\.1/',
                        '/^192\.168\..*/',
                        '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
                        '/^10\..*/');

                    $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

                    if ($client_ip != $found_ip)
                    {
                        $client_ip = $found_ip;
                        break;
                    }
                }
            }
        } else {
            $client_ip =
            ( !empty($_SERVER['REMOTE_ADDR']) ) ?
                $_SERVER['REMOTE_ADDR']
                :
                ( ( !empty($_ENV['REMOTE_ADDR']) ) ?
                   $_ENV['REMOTE_ADDR']
                   :
                   "unknown" );
        }

        return $client_ip;
    }
}

if (!function_exists('get_adjusted_date')) {
    function get_adjusted_date($d) {

	    $sel_period = 24 * 3600; //in hours
	    $event_time = time() - $d;
        if ($event_time < $sel_period) 
	    {
	        if ($sel_period > 3600)
	        {
	            $hours = floor($event_time / 3600);
                if ($hours == 0) {
                    $minutes = floor($event_time / 60);
                    if ($minutes == 0) {
                        return 'Now';
		            } else {
			            return $minutes.' '.get_padezh_of_date($minutes, 'min. ago', 'min. ago', 'min. ago');
		            }
		        } else {
			        $minutes = $event_time - (3600 * $hours);
			        $minutes = floor($minutes / 60);
                    if ($minutes == 0) {
                        return $hours.' '.get_padezh_of_date($hours, 'hour ago', 'hours ago', 'hours ago');
		            } else {
			            return $hours.' '.get_padezh_of_date($hours, 'hour,', 'hours,', 'hours,') .' '.$minutes.' '.get_padezh_of_date($minutes, 'min. ago', 'min. ago', 'min. ago');
		            }
		        }
		    } else {
	            $minutes = floor($event_time / 60);
                if ($minutes == 0) {
                    return 'Now';
		        } else {
			        return $minutes.' '.get_padezh_of_date($minutes, 'min. ago', 'min. ago', 'min. ago');
		        }
		    }
        } else {
		    return date('j.m.Y H:i', $d);
	    }
	}
}

if (!function_exists('get_padezh_of_date')) {
    function get_padezh_of_date($num, $p1, $p2, $p5) {
        $x = $num % 100;
        $y = ($x % 10)-1;
        $res = ($x/10)>>0==1 ? $p5 : ($y&12 ? $p5 : ($y&3 ? $p2 : $p1));

        return $res;
    }
}

if (!function_exists('get_avatar')) {
    function get_avatar($email, $size = null) {
        $url = 'https://robohash.org/'.$email;
        if ($size) {
            $url .= sprintf('?size=%dx%d', $size, $size);
        }
		return '<img src="'.$url.'">';
    }
}
?>