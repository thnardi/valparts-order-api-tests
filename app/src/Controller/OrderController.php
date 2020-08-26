<?php
declare(strict_types=1);

namespace Farol360\Ancora\Controller;

use Farol360\Ancora\Controller;
use Farol360\Ancora\Mailer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// use Slim\Flash\Messages as FlashMessages;
// use Slim\Views\Twig as View;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\EntityFactory;

class OrderController extends Controller
{
    // protected $mailer;
    protected $ambiente_windows;


    public function __construct(
        // View $view, 
        // FlashMessages $flash, 
        // Mailer $mailer, 
        Model $orderModel, 
        EntityFactory $entityFactory, 
        $ambiente_windows)
    {
        $this->orderModel        = $orderModel;
        $this->entityFactory    = $entityFactory;
        $this->ambiente_windows    = $ambiente_windows;
    }


    public function erp_service(Request $request, Response $response): Response
    {

        $body = $request->getParsedBody();
        // var_dump($body); die;
        // valida a order
        $data['cli_id']             = isset($body['CLI_ID']) ? (int)$body['CLI_ID'] : null;
        $data['total']              = isset($body['TOTAL']) ? $body['TOTAL'] : null;
        $data['pagamento']          = isset($body['PAGAMENTO']) ? $body['PAGAMENTO'] : null; 
        $data['produtos']           = isset($body['PRODUTOS']) ? json_encode($body['PRODUTOS']) : null;
        $data['endereco_entrega']   = isset($body['SHIPPING']) ? $body['SHIPPING'] : null;
        
        // adiciona a order
        $order = $this->entityFactory->createOrder($data);
        
        $return_order = $this->orderModel->add($order);
        
        $return['payload']   = $return_order->data;
        $return['amount'] = 1; 
        return $response->withJson($return, 200);
    }

    public function financeiro(Request $request, Response $response): Response
    {

        $data['payload']   = 'Financeiro';
        $data['amount'] = 1; 
        return $response->withJson($data, 200);
    }

}
