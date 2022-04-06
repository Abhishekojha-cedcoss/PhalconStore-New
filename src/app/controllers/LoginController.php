<?php

use App\Components\EscapeClass;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;


class LoginController extends Controller
{
    /**
     * Index Function of Login 
     *
     * Matches the email and password from the database
     * 
     * @return void
     */
    public function indexAction()
    {
        $escaper = new EscapeClass();
        if ($this->request->hasPost("submit")) {
            $result = $escaper->sanitize($this->request->getPost());
            $email = $result["email"];
            $password = $result["password"];

            $v = Users::findFirst(
                [
                    'columns' => '*',
                    'conditions' => 'email = ?1 AND password=?2',
                    'bind' => [
                        1 => $email,
                        2 => $password
                    ]
                ]
            );

            if (count((array)$v) > 0) {
                $response = new Response();
                $mysession = $this->session;
                $mysession = $this->container->getSession();
                $mysession->set('user', [
                    'email' => $email, 'password' => $password, 'role' => $v->role
                ]);
                $response->redirect("admin?bearer=".$v->token, true, 303);
                $response->send();
            } else {
                $msg = "Wrong Email or Password!!";
                $this->logger->error($msg);
                $this->view->data = $msg;
            }
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function signoutAction()
    {
        //Delete the Session
        $this->session->remove("user");
        $this->session->destroy();

        header("location: http://localhost:8080/login");
    }
}
