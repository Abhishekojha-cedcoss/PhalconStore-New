<?php

use App\Components\EscapeClass;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Phalcon\Events\Manager as EventsManager;

/**
 * OrderController class
 * 
 */
class OrderController extends Controller
{
    /**
     * Undocumented function
     *
     * Add the orders 
     * 
     * @return void
     */
    public function addOrderAction()
    {
        $res = Products::find();
        $this->view->data = $res;
        if ($this->request->hasPost("submit")) {
            $escape = new EscapeClass();
            $arr = $escape->sanitize($this->request->getPost());

            $order = new Orders();

            $order->assign(
                $arr,
                [
                    'customer_name',
                    'customer_address',
                    'zipcode',
                    'product',
                    'quantity'
                ]
            );
            $success = $order->save();

            //<...............................<Event fired>....................................>
            $eventsManager = new EventsManager();
            $component   = new App\Components\loader();
            $component->setEventsManager($eventsManager);
            $eventsManager->attach(
                'notifications',
                new App\Components\listener()
            );
            $component->orders();
            //<...............................<Event fired>....................................>

            $this->view->success = $success;
            if ($success) {
                $this->view->message = "Register succesfully";
            } else {
                $this->view->message = "Not Register succesfully due to following reason: <br>" . implode("<br>", $order->getMessages());
            }
        }
    }

     /**
     * listOrderAction function
     *
     * Lists all the orders
     * 
     * @return void
     */
    public function listOrderAction()
    {
        //Delete the Order
        if ($this->request->hasPost("delete")) {
            $id = $this->request->get("id");
            $res = Orders::findFirst("order_id IN (" . $id . ")");
            $res->delete();
        }
        $res = Orders::find();
        $this->view->data = $res;
    }

    /**
     * updateOrderAction function
     *
     * Helps to update the order Details
     * 
     * @return void
     */
    public function updateOrderAction()
    {
        $id = $this->request->get("id");
        $this->view->data = Orders::findFirst("order_id IN (" . $id . ")");
        if ($this->request->hasPost("submit")) {
            $res = Orders::findFirst("order_id IN (" . $id . ")");
            $res->customer_name = $this->request->get("customer_name");
            $res->customer_address = $this->request->get("customer_address");
            $res->zipcode = $this->request->get("zipcode");
            $res->product = $this->request->get("product");
            $res->quantity = $this->request->get("quantity");
            $success = $res->save();
            if ($success) {
                $response = new Response();
                $response->redirect(URLROOT . "order/listOrder", true, 303);
                $response->send();
            }
        }
    }
}
