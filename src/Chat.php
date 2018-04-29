<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
require "../db/users.php";
require "../db/chatrooms.php";
class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        echo "Server Started.";
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
        $data = json_decode($msg, true);
        $objChatroom = new \chatrooms;
        $objChatroom->setUserId($data['userId']);
        $objChatroom->setMsg($data['msg']);
        $objChatroom->setCreatedOn(date("Y-m-d h:i:s"));
        if($objChatroom->saveChatRoom()) {
            $objUser = new \users;
            $objUser->setId($data['userId']);
            $user = $objUser->getUserById();
            $data['from'] = $user['name'];
            $data['msg']  = $data['msg'];
            $data['dt']  = date("d-m-Y h:i:s");
        }

        foreach ($this->clients as $client) {
            if ($from == $client) {
                $data['from']  = "Me";
            } else {
                $data['from']  = $user['name'];
            }
            $client->send(json_encode($data));
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}