<?php
require '../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

class Firebase {
    private $auth;

    public function __construct() {
        $factory = (new Factory)->withServiceAccount(__DIR__ . '/softengfinal-firebase-adminsdk-fbsvc-28ce87f4f6.json');
        $this->auth = $factory->createAuth();
    }

    public function sendVerificationEmail($email) {
        try {
            $user = $this->auth->getUserByEmail($email);
            $this->auth->sendEmailVerification($user->uid);
            return true;
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            return false;
        }
    }
}
?>