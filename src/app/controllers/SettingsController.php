<?php

use App\Components\EscapeClass;
use Phalcon\Mvc\Controller;

/**
 * SettingsController class
 */
class SettingsController extends Controller
{
    public function indexAction()
    {
        if ($this->request->hasPost("submit")) {
            $arr = $this->request->getPost();
            $setting = Settings::findFirst(1);
            $setting->title_optimization = $arr["title_optimization"];
            $setting->default_price = $arr["default_price"];
            $setting->default_stock = $arr["default_stock"];
            $setting->default_zipcode = $arr["default_zipcode"];
            $success = $setting->save();
            $this->view->success = $success;
            if ($success) {
                $this->view->message = "Register succesfully";
            } else {
                $this->view->message = "Not Register succesfully due to following reason: <br>" . implode("<br>", $setting->getMessages());
            }
        }
    }

    /**
     * addroleAction function
     *
     * @return void
     */
    public function addroleAction()
    {
        if ($this->request->hasPost("submit")) {
            $res = new Permissions();

            $escaper= new EscapeClass();
            $arr= $escaper->sanitize($this->request->getPost());

            $res->assign(
                $arr,
                [
                    'role',
                ]
            );
            $success = $res->save();
            $this->view->success = $success;
            if ($success) {
                $this->view->message = "Register succesfully";
            } else {
                $this->view->message = "Not Register succesfully due to following reason: <br>" . implode("<br>", $res->getMessages());
            }
        }
    }

    /**
     * accessAction function
     *
     * Give the access to particular controller and action to a specific user
     * 
     * @return void
     */
    public function accessAction()
    {

        $user = Permissions::find();
        $this->view->user = $user;

        $d = new App\Components\helper();
        $data = $d->getcontroller();

        $this->view->data = $data;

        if ($this->request->hasPost("submit")) {
            $role = new Roles();

            $res = array();
            $controller = str_replace("Controller", "", $this->request->getPost()["controller"]);
            $action = str_replace("Action", "", $this->request->getPost()["action"]);
            $res = [
                "role" => strtolower($this->request->get("role")),
                "controller" => strtolower($controller),
                "action" => strtolower($action)
            ];
            $role->assign(
                $res,
                [
                    'role',
                    'controller',
                    'action'
                ]
            );
            $success = $role->save();
            $this->view->success = $success;
            if ($success) {
                $this->view->message = "Register succesfully";
            } else {
                $this->view->message = "Not Register succesfully due to following reason: <br>" . implode("<br>", $role->getMessages());
            }
        }
    }
}