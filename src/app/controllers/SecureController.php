<?php

use Phalcon\Acl\Adapter\Memory;
use Phalcon\Mvc\Controller;

use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;

/**
 * SecureController class
 */
class SecureController extends Controller
{

    /**
     * Undocumented function
     *
     * Creates token for every new user
     * 
     * @param [type] $role
     * @return void
     */
    public function createTokenAction($role)
    {
        // Defaults to 'sha512'
        $signer = new Hmac();

        // Builder object
        $builder = new Builder($signer);

        $now        = new DateTimeImmutable();
        $issued     = $now->getTimestamp();

        $notBefore  = $now->modify('-1 minute')->getTimestamp();
        $expires    = $now->modify('+1 day')->getTimestamp();
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

        // Setup
        $builder
            ->setAudience('https://target.phalcon.io')  // aud
            ->setContentType('application/json')        // cty - header
            ->setExpirationTime($expires)               // exp 
            ->setId('abcd1234')                         // JTI id 
            ->setIssuedAt($issued)                      // iat 
            ->setIssuer('https://phalcon.io')           // iss 
            ->setNotBefore($notBefore)                  // nbf
            ->setSubject($role)            // sub
            ->setPassphrase($passphrase)                // password 
        ;

        // Phalcon\Security\JWT\Token\Token object
        $tokenObject = $builder->getToken();

        $t = $tokenObject->getToken();
        return $t;
        // The token

    }

    /**
     * buildACLAction function
     *
     * Builds the Acl file if it is not already present
     * 
     * @return void
     */
    public function buildACLAction()
    {
        $aclFile = APP_PATH . '/security/acl.cache';

        if (true !== is_file($aclFile)) {

            // The ACL does not exist - build it
            $acl = new Memory();


            echo "<pre>";
            $var = new \App\Components\myController();
            $result = $var->getcontrol();

            foreach ($result as $key => $value) {
                foreach ($value as $k) {
                    $acl->addComponent(
                        strtolower(str_replace("Controller", "", $key)),
                        [
                            strtolower(str_replace("Action", "", $k))
                        ]
                    );
                }
            }

            $var = Permissions::find();
            $res = json_decode(json_encode($var));
            foreach ($res as $key => $value) {
                // echo($value->role);
                $acl->addRole($value->role);
                if ($value->role == "admin") {
                    $acl->allow($value->role, "*", "*");
                } else if ($value->role == "manager") {
                    $acl->allow($value->role, "product", "*");
                    $acl->allow($value->role, "admin", "index");
                } else if ($value->role == "accountant") {
                    $acl->allow($value->role, "order", "*");
                    $acl->allow($value->role, "admin", "index");
                }
            }

            $acl->addRole("guest");
            $acl->allow("guest", "login", "*");

            // Store serialized list into plain file
            file_put_contents(
                $aclFile,
                serialize($acl)
            );
        }

    }
}
