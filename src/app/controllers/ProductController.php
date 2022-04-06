<?php

use App\Components\EscapeClass;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

/**
 * ProductController class
 * 
 * Implements the Product functionality
 */
class ProductController extends Controller
{
    /**
     * addProductAction function
     *
     * Add new products to the  database
     * @return void
     */
    public function addProductAction()
    {

        if ($this->request->hasPost("submit")) {
            $product = new Products();
            $escaper=new EscapeClass();

            $arr=$escaper->sanitize($this->request->getPost());
            $product->assign(
                $arr,
                [
                    'name',
                    'description',
                    'tags',
                    'price',
                    'stock'
                ]
            );
            $success = $product->save();

            // <.............................Event fired from here .................................>
            $eventsManager = new EventsManager();

            $component   = new App\Components\loader();

            $component->setEventsManager($eventsManager);

            $eventsManager->attach(
                'notifications',
                new App\Components\listener()
            );
            $component->process();
            // <................................Event was fired.....................................>


            $this->view->success = $success;
            if ($success) {
                $this->view->message = "Register succesfully";
            } else {
                $this->view->message = "Not Register succesfully due to following reason: <br>" . implode("<br>", $product->getMessages());
            }
        }
    }

    /**
     * listProductAction function
     *
     * Lists all the products after fetching from the database
     * 
     * @return void
     */
    public function listProductAction()
    {

        //Delete the product
        if ($this->request->hasPost("delete")) {
            $id=$this->request->get("id");
            $res = Products::findFirst("id IN (".$id.")");
            $res->delete();
        }
        $res = Products::find();
        $this->view->data = $res;

    }

    /**
     * updateProductAction function
     *
     * Updates the details of the selected product
     * 
     * @return void
     */
    public function updateProductAction()
    {
        $id=$this->request->get("id");
        $this->view->data = Products::findFirst("id IN (".$id.")");
        if ($this->request->hasPost("submit")) {
            $res = Products::findFirst("id IN (".$id.")");
            $res->name=$this->request->get("name");
            $res->description=$this->request->get("description");
            $res->tags=$this->request->get("tags");
            $res->price=$this->request->get("price");
            $res->stock=$this->request->get("stock");
            $success=$res->save();
            if ($success) {
                $response= new Response();
                $response->redirect(URLROOT."product/listProduct", true, 303);
                $response->send();
            }
        }
    }
}