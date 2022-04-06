<?php

namespace App\Components;

use Phalcon\Events\Event;
use Phalcon\Logger;
use Products;
use SettingsController;
use Settings;
use Orders;

use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;

/**
 * @property Logger $logger
 */
class listener extends SettingsController
{
    // public function __construct(Logger $logger)
    // {
    //     $this->logger = $logger;
    // }

    public function product(
        Event $event,
        loader $component
    ) {
        $res = Settings::findFirst();
        $product = Products::find();
        $last = $product->getLast();
        if ($res->title_optimization == 'with tags') {

            $title = $last->name . $last->tags;
            $last->name = $title;
            $last->save();
        }
        foreach ($product as $prod) {
            if (empty($prod->price) || $prod->price == '0') {
                $prod->price = $res->default_price;
                $prod->save();
            }
            if (empty($prod->stock) || $prod->stock == '0') {
                $prod->stock = $res->default_stock;
                $prod->save();
            }
        }
    }
    public function orders(
        Event $event,
        loader $component
    ) {
        $res = Settings::findFirst();
        $order = Orders::find();
        $lastorder = $order->getLast();
        if (empty($lastorder->zipcode) || $lastorder->zipcode == '0') {

            $lastorder->zipcode = $res->default_zipcode;
            $lastorder->save();
        }
    }

    public function beforeHandleRequest(Event $event, \Phalcon\Mvc\Application $application)
    {

        $aclFile = APP_PATH . '/security/acl.cache';



        if (true == is_file($aclFile)) {
            $acl = unserialize(
                file_get_contents($aclFile)
            );



            //token
            $bearer = $application->request->get("bearer");
            if ($bearer) {
                try {
                    $parser = new Parser();
                    $tokenObject = $parser->parse($bearer);
                    $now = new \DateTimeImmutable();
                    // $expires=$now->modify('+1 day')->getTimestamp();
                    $expires = $now->getTimestamp();
                    $validator = new Validator($tokenObject, 100);
                    $validator->validateExpiration($expires);
                    $role =  $tokenObject->getClaims()->getPayload()["sub"];

                    //get Acl conditions
                    $res =  $application->request->get();
                    if (!isset($res["_url"])) {
                        $controller = "index";
                        $action = "index";
                    } else {
                        $controller = $this->router->getControllerName();
                        $action = $this->router->getActionName();
                        if ($action == null) {
                            $action = "index";
                        }
                    }

                    if (!$role || true !== $acl->isAllowed($role, $controller, $action)) {
                        die("Access Denied!!");
                    }

                    //acl end

                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                $controller = $this->router->getControllerName();
                $action = $this->router->getActionName();
                if ($controller == 'login' && $action == null) {
                    $action = "index";
                    $role = "guest";
                } else {
                    echo "Please add bearer";
                    die;
                }
                if (!$role || true !== $acl->isAllowed($role, $controller, $action)) {
                    die("access denied man if admin change role");
                }
            }
        }
    }
}
