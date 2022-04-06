<?php

use App\Components\EscapeClass;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

/**
 * AdminController class
 */
class AdminController extends Controller
{
    /**
     * IndexAction function
     *
     * Displays the 1st page after login
     * @return void
     */
    public function indexAction()
    {
        $locale = $this->request->get("Locale");

        $v = new App\Components\Locale();

        $data = $v->getTranslator();

        if (!$this->cache->has($locale)) {
            $this->cache->set($locale, $data);
        }
    }

    /**
     * usersAction function
     *
     * @return void
     */
    public function usersAction()
    {

        //Delete the user
        if ($this->request->hasPost("delete")) {
            $id = $this->request->get("id");
            $res = Users::findFirst("id IN (" . $id . ")");
            $res->delete();
        }

        $this->view->data = Users::find();
    }

    
    /**
     * addUserAction function
     *
     * Admin adds the user and specifies a particular role to that user
     * 
     * @return void
     */
    public function addUserAction()
    {

        $data = Permissions::find();
        $this->view->data = $data;

        if ($this->request->hasPost("submit")) {
            $res = $this->request->getPost();

            $role = $res["role"];
            $token = new SecureController();
            $t = $token->createTokenAction($role, $res["name"]);

            $escaper = new EscapeClass();
            $arr = [
                'email' => $this->request->get("email"),
                'name' => $this->request->get("name"),
                'password' => $this->request->get("password"),
                'role' => $role,
                'token' => $t

            ];
            $arr = $escaper->sanitize($arr);
            $user = new Users();

            $user->assign(
                $arr,
                [
                    'email',
                    'name',
                    'password',
                    'role',
                    'token'
                ]
            );
            $success = $user->save();
            $this->view->success = $success;
            if ($success) {
                $this->view->message = "Register succesfully<br>Please Copy the Token!<br>" . $t;
            } else {
                $this->view->message = "Not Register succesfully due to following reason: <br>" . implode("<br>", $user->getMessages());
            }
        }
    }

    /**
     * updateUserAction function
     *
     * Admin updates the details of the user by getting the id of that user
     * 
     * @return void
     */
    public function updateUserAction()
    {
        $data = Permissions::find();
        $this->view->roles = $data;
        $id = $this->request->get("id");
        $this->view->data = Users::findFirst("id IN (" . $id . ")");
        if ($this->request->hasPost("submit")) {
            $res = Users::findFirst("id IN (" . $id . ")");
            $res->name = $this->request->get("name");
            $res->email = $this->request->get("email");
            $res->password = $this->request->get("password");
            $res->role = $this->request->get("role");
            $success = $res->save();
            // die($res->getMessages());
            if ($success) {
                $response = new Response();
                $response->redirect(URLROOT . "admin/users", true, 303);
                $response->send();
            }
        }
    }
    
}
