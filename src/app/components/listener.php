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
                        die('
                        <section class="p-0 bg-img cover-background" style="background-image: url(https://bootdey.com/img/Content/bg1.jpg);color:white">
                                <div class="container-fluid d-flex flex-column">
                                    <div class="row align-items-center justify-content-center min-vh-100">
                                        <div class="col-md-9 col-lg-6 my-5">
                                            <div class="text-center error-page">
                                                <h1 class="mb-0 text-secondary">Access Denied!!</h1>
                                                <h2 class="mb-4 text-white">Sorry, you are not allowed!</h2>
                                                <p class="w-sm-80 mx-auto mb-4 text-white">
                                                    This page is incidentally inaccessible because of support. 
                                                    We will back very before long much obliged for your understanding</p>
                                                <div>
                                                    <a href="'.URLROOT.'admin?bearer='.$bearer.'" class="btn btn-info btn-lg me-sm-2 mb-2 mb-sm-0">Return Home</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>');
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
                } elseif ($controller == 'login' && $action == 'signout') {
                    $role = "guest";
                } else {
                    echo "Please add bearer";
                    die;
                }
                if (!$role || true !== $acl->isAllowed($role, $controller, $action)) {
                    die("Access Denied!!");
                }
            }
        }
    }
}
