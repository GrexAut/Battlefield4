<?php

/**
 * This is the Battlefield4 class file
 * 
 * PHP Version 5.4
 * 
 * @category RCON API
 * @package Battlefield 4 RCON API
 * @version 1.0
 * @author: Gregor Ganglberger <gg@grexaut.net>
 * @license: Creative Commons http://creativecommons.org/licenses/by/3.0/
 * @link: http://github.com/GrexAut
 * */
class Battlefield4 extends RCON {

    private $socket = false;
    public $loggedIn = false;

    /*
     * create socket to battlefield 4 admin server (over rcon port)     
     */

    public function connectToServer($serverIP, $serverPort) {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($socket, $serverIP, $serverPort);
        socket_set_nonblock($socket);

        if ($socket !== false) {
            $this->socket = $socket;
            return $socket;
        } else {
            return false;
        }
    }

    /*
     * Close the given or actual socket
     */

    public function disconnecFromServer() {
        if (socket_close($this->socket)) {
            $this->socket = false;
            return true;
        }
        return false;
    }

    /*
     * =========[COMMANDS]===========
     */

    /**
     * getServerinfo()
     * Arguments: none
     * Login required: no
     *
     * @return array (status,name,slots,maxSlots,gameMode,map,ticketsA,ticketsB,address,version,country,language)
     * Get Server Informations
     * */
    public function getServerinfo() { // working
        $data = $this->exec($this->socket, 'serverinfo');
        $serverInfo = array(
            'status' => $data[0],
            'name' => $data[1],
            'players' => $data[2],
            'maxPlayers' => $data[3],
            'gameMode' => $data[4],
            'map' => $data[5],
            'ticketsA' => $data[9],
            'ticketsB' => $data[10],
            'address' => $data[18],
            'version' => $data[19],
            'country' => $data[21],
            'language' => $data[23]
        );
        return $serverInfo;
    }

    /**
     * login(string)
     * Arguments: password = admin.password in plainText
     * Login required: no
     * 
     * @return true/false
     * Login in as RCON Admin with plainText admin.password
     * */
    public function login($password) { // working
        $data = $this->exec($this->socket, 'login.plainText ' . $password);

        if ($data[0] == "OK") {
            $this->loggedIn = true;
            return true;
        } else {
            return false;
        }
    }

    /**
     * logout()
     * Arguments: none
     * Login required: yes
     * 
     * @return true/false
     * Logout from RCON Console
     * */
    public function logout() { // working
        $data = $this->exec($this->socket, 'logout');

        if ($data[0] == "OK") {
            $this->loggedIn = false;
            return true;
        } else {
            return false;
        }
    }

    /**
     * getAllPlayers()
     * Arguments: none
     * Login required: no
     *
     * @return array  (name,teamId,kills,deaths,score,rank,ping)
     * Get all players from server
     * */
    public function getAllPlayers() { // working 
        $data = $this->exec($this->socket, 'listPlayers all');

        $players = array();
        for ($i = 12; $i < count($data); $i += 9) {
            $next = count($players);
            $players[$next]['name'] = $data[$i];
            if ($this->loggedIn === false) {
                $players[$next]['guid'] = $data[$i + 1];
            }
            $players[$next]['teamId'] = $data[$i + 2];
            $players[$next]['kills'] = $data[$i + 3];
            $players[$next]['deaths'] = $data[$i + 4];
            $players[$next]['score'] = $data[$i + 5];
            $players[$next]['rank'] = $data[$i + 6];
            $players[$next]['ping'] = $data[$i + 7];
        }
        return $players;
    }

    /**
     * getCurrentLevel()
     * Arguments: none
     * Login required: yes
     *
     * @return array  (name,teamId,kills,deaths,score,rank,ping)
     * Get all players from server
     * */
    public function getCurrentLevel() { // working
        if($this->loggedIn === false) {
            return false;
        }
        
        $data = $this->exec($this->socket, 'currentlevel');
        if ($data[0] == "OK") {
            return $data[1];
        } else {
            return false;
        }
    }

    /**
     * kickPlayer(string, string)
     * Arguments: Soldiername, Reason
     * Login required: yes
     *
     * @return true/false
     * kick a player with a reason
     * */
    public function kickPlayer($player, $reason) { // working
        if($this->loggedIn === false) {
            return false;
        }
        
        $data = $this->exec($this->socket, 'admin.kickPlayer ' . $player . ' ' . $reason);
        echo "<pre>", print_r($data), "</pre>";
        if ($data[0] == "OK") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * killPlayer(string)
     * Arguments: Soldiername
     * Login required: yes
     *
     * @return true/false
     * kill a player
     * */
    public function killPlayer($player) { // to test
        if($this->loggedIn === false) {
            return false;
        }
        $data = $this->exec($this->socket, 'admin.killPlayer ' . $player);
        return $data; // 4 testing
        if ($data[0] == "OK") {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * killPlayer(int,int,int,true/false)
     * Arguments: Soldiername, TeamID, SquadID, ForceKill
     * Login required: yes
     *
     * @return true/false
     * kill a player
     * */
    public function movePlayer($player, $teamId, $squadId = 0, $forceKill = false) { // to test
        if($this->loggedIn === false) {
            return false;
        }

        $data = $this->exec($this->socket, 'admin.movePlayer ' . $player . ' ' . $teamId . ' ' . $squadId . ' ' . $forceKill);
        echo "<pre>", print_r($data), "</pre>";
        if ($data[0] == "OK") {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * adminSay(string)
     * Arguments: Text
     * Login required: yes
     *
     * @return true/false
     * kill a player
     * */
    public function adminSay($text, $players) { // to test
        if($this->loggedIn === false) {
            return false;
        }
        
        $data = $this->exec($this->socket, 'admin.say '.$text .' '.$players);
        echo "<pre>", print_r($data), "</pre>";
        if ($data[0] == "OK") {
            return true;
        } else {
            return false;
        }
    }
    
    public function adminYell($text,$duration = 10,$players) {
        if($this->loggedIn === false) {
            return false;
        }
        
        $data = $this->exec($this->socket, 'admin.yell '.$text.' '.$duration.' '.$players);
        echo "<pre>", print_r($data), "</pre>";
    }

    /*
     * Callbacks (?)
     */
}

class RCON {

    private function EncodeHeader($isFromServer, $isResponse, $sequence) {
        $header = $sequence & 0x3fffffff;
        if ($isFromServer) {
            $header += 0x80000000;
        }
        if ($isResponse) {
            $header += 0x40000000;
        }
        return pack('I', $header);
    }

    private function DecodeHeader($data) {
        $header = unpack('I', mb_substr($data, 0, 4));

        return array($header & 0x80000000, $header & 0x40000000, $header & 0x3fffffff);
    }

    private function EncodeInt32($size) {
        return pack('I', $size);
    }

    private function DecodeInt32($data) {
        $decode = unpack('I', mb_substr($data, 0, 4));
        return $decode[1];
    }

    private function EncodeWords($words) {
        $size = 0;
        $encodedWords = '';

        foreach ($words as $word) {
            $encodedWords .= $this->EncodeInt32(strlen($word));
            $encodedWords .= $word;
            $encodedWords .= "\x00";
            $size += strlen($word) + 5;
        }

        return array($size, $encodedWords);
    }

    private function DecodeWords($size, $data) {
        $numWords = $this->DecodeInt32($data);
        $words = array();
        $offset = 0;
        while ($offset < $size) {
            $wordLen = $this->DecodeInt32(mb_substr($data, $offset, 4));
            $word = mb_substr($data, $offset + 4, $wordLen);
            array_push($words, $word);
            $offset += $wordLen + 5;
        }

        return $words;
    }

    private function EncodePacket($isFromServer, $isResponse, $sequence, $words) {
        $encodedHeader = $this->EncodeHeader($isFromServer, $isResponse, $sequence);
        $encodedNumWords = $this->EncodeInt32(count($words));
        list($wordsSize, $encodedWords) = $this->EncodeWords($words);
        $encodedSize = $this->EncodeInt32($wordsSize + 12);

        return $encodedHeader . $encodedSize . $encodedNumWords . $encodedWords;
    }

    private function DecodePacket($data) {
        list($isFromServer, $isResponse, $sequence) = $this->DecodeHeader($data);
        $wordsSize = $this->DecodeInt32(mb_substr($data, 4, 4)) - 12;
        $words = $this->DecodeWords($wordsSize, mb_substr($data, 12));

        return array($isFromServer, $isResponse, $sequence, $words);
    }

    private function EncodeClientRequest($string) {
        global $clientSequenceNr;

        // string splitting
        if ((strpos($string, '"') !== FALSE) or (strpos($string, '\'') !== FALSE)) {
            $words = preg_split('/["\']/', $string);

            for ($i = 0; $i < count($words); $i++) {
                $words[$i] = trim($words[$i]);
            }
        } else {
            $words = preg_split('/\s+/', $string);
        }

        $packet = $this->EncodePacket(False, False, $clientSequenceNr, $words);
        $clientSequenceNr = ($clientSequenceNr + 1) & 0x3fffffff;

        return $packet;
    }

    private function containsCompletePacket($data) {
        if (mb_strlen($data) < 8) {
            return False;
        }

        if (mb_strlen($data) < $this->DecodeInt32(mb_substr($data, 4, 4))) {
            return False;
        }

        return True;
    }

    private function receivePacket(&$socket) {
        $receiveBuffer = '';
        while (!$this->containsCompletePacket($receiveBuffer)) {
            global $receiveBuffer;

            if (($receiveBuffer .= socket_read($socket, 4096)) === FALSE) {
                echo "Socket error: " . socket_strerror(socket_last_error($socket)) . "\n";
                socket_close($socket);
                exit;
            }
        }

        $packetSize = $this->DecodeInt32(mb_substr($receiveBuffer, 4, 4));
        $packet = mb_substr($receiveBuffer, 0, $packetSize);
        $receiveBuffer = mb_substr($receiveBuffer, $packetSize);

        return array($packet, $receiveBuffer);
    }

    private function printPacket($packet) {
        if ($packet[0]) {
            echo "IsFromServer, $packet[0] ";
        } else {
            echo "IsFromClient, ";
        }

        if ($packet[1]) {
            echo "Response, $packet[1] ";
        } else {
            echo "Request, ";
        }

        echo "Sequence: $packet[2]";

        if ($packet[3]) {
            echo " Words:";
            foreach ($packet[3] as $word) {
                echo " \"$word\"";
            }
        }
    }

    /**
     * Main RCON Command. Call this to send a command to the server.
     * Arguments: socket, command to send
     *
     * @return array of words; server's response
     * */
    protected function exec(&$socket, $string) {
        if ((socket_write($socket, $this->EncodeClientRequest($string))) === FALSE) {
            echo "Socket error: " . socket_strerror(socket_last_error($socket)) . "\n";
            socket_close($socket);
            exit;
        }

        list($packet, $receiveBuffer) = $this->receivePacket($socket);
        list($isFromServer, $isResponse, $sequence, $words) = $this->DecodePacket($packet);

        return $words;
    }

}

?>
