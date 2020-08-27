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

        // RANDOM PRODUTOS

        $array_status[0] = 'aprovado';
        $array_status[1] = 'aprovado';
        $array_status[2] = 'aprovado';
        $array_status[3] = 'aprovado';
        $array_status[4] = 'aprovado';
        $array_status[5] = 'falta_credito';
        $array_status[6] = 'falta_credito';
        $array_status[7] = 'falta_produto';
        $array_status[8] = 'falta_produto';
        $array_status[9] = 'outro_erro';
        $array_status[10] = 'server_error';
        
        $random_int = random_int(0,10);

        if ($random_int < 5)  {
            
            // valida a order
            $data['cli_id']             = isset($body['CLI_ID']) ? (int)$body['CLI_ID'] : null;
            $data['total']              = isset($body['TOTAL']) ? $body['TOTAL'] : null;
            $data['pagamento']          = isset($body['PAGAMENTO']) ? $body['PAGAMENTO'] : null; 
            $data['produtos']           = isset($body['PRODUTOS']) ? json_encode($body['PRODUTOS']) : null;
            $data['endereco_entrega']   = isset($body['SHIPPING']) ? $body['SHIPPING'] : null;
            $data['status']             = $array_status[$random_int];
            
            // adiciona a order
            $order = $this->entityFactory->createOrder($data);
            $return_order = $this->orderModel->add($order);
            

            $return['payload']   = $return_order->data;
            $return['status'] = $array_status[$random_int]; 
            return $response->withJson($return, 200);
        } else if (($random_int > 5) && ($random_int < 10)) {
            $return['payload']   = false;
            $return['amount'] = $array_status[$random_int]; 
            return $response->withJson($return, 200);
        }

        $return['payload']   = false;
        $return['amount'] = $array_status[$random_int]; 
        return $response->withJson($return, 400);
    }

    public function financeiro(Request $request, Response $response): Response
    {

        $data['payload']   = 'Financeiro';
        $data['amount'] = 1; 
        return $response->withJson($data, 200);
    }

}
