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
            

            $return['payload']  = $return_order->data;
            $return['status']   = $array_status[$random_int]; 
            return $response->withJson($return, 200);
        } else if (($random_int > 5) && ($random_int < 10)) {
            $return['payload']      = false;
            $return['status']       = $array_status[$random_int]; 
            return $response->withJson($return, 200);
        }

        $return['payload']      = false;
        $return['status']       = $array_status[$random_int]; 
        return $response->withJson($return, 400);
    }

    public function encaminhar_financeiro(Request $request, Response $response): Response
    {
        $queryParam = (isset($request->getQueryParams()['falha'])) ? $request->getQueryParams()['falha'] : false;
        if ($queryParam == 'true') {
            $data['payload']   = false;
            $data['status']   = 'falha';
            return $response->withJson($data, 403);
        }
        
        $order_id = (isset($request->getParsedBody()['id'])) ? (int)$request->getParsedBody()['id'] : 0;

        $return_order = $this->orderModel->get($order_id);
        // var_dump($return_order); die;
        if ($return_order->data != false) {
            $return_update = $this->orderModel->changeStatus((int)$return_order->data->id,'encaminhado_financeiro');
            if ($return_update->data != false) {
                $data['payload']    = true;
                $data['status']     = 'encaminhado_financeiro';
                $data['message']    = "Ordem Encaminhada para o financeiro."; 
                return $response->withJson($data, 200);
            } 
            $data['payload']    = false;
            $data['status']     = 'outro_erro';
            $data['message']    = "Erro não identificado."; 
            return $response->withJson($data, 200);
        }
        $data['payload']    = false;
        $data['status']     = 'ordem_not_found';
        $data['message']    = "Ordem Não encontrada."; 
        return $response->withJson($data, 200);
    }

    public function getAllOrders(Request $request, Response $response): Response
    {
        
        $return_orders = $this->orderModel->getAll();

        if ($return_orders->data != false) {
            $data['payload']    = $return_orders->data;
            $data['amount']     = count($return_orders->data); 
            return $response->withJson($data, 200);
        }
    }

    public function consultar_aprovacao_financeiro(Request $request, Response $response): Response
    {
        $queryParam = (isset($request->getQueryParams()['falha'])) ? $request->getQueryParams()['falha'] : false;
        if ($queryParam == 'true') {
            $data['payload']   = false;
            return $response->withJson($data, 403);
        }

        $random_int = random_int(0,1);

        $order_id = (isset($request->getParsedBody()['id'])) ? (int)$request->getParsedBody()['id'] : 0;

        $return_order = $this->orderModel->get($order_id);

        // aprovado
        if ($random_int == 0) {
            $return_update = $this->orderModel->changeStatus((int)$return_order->data->id,'aprovado_financeiro');
        }

        $return_order = $this->orderModel->get($order_id);

        if ($return_order->data != false ) {
            $data['payload']   = $return_order->data;
            if ($return_order->data->status == 'aprovado_financeiro') {
                $data['status'] = 'aprovado'; 
            } else {
                $data['status'] = 'pendente'; 
            }
            return $response->withJson($data, 200);
        }
        $data['payload']   = false;
        $data['status'] = 'outro_erro'; 
        return $response->withJson($data, 200);
    }

    public function erp_cancela_ordem(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();

        $return_order = $this->orderModel->get((int)$queryParams['id']);

        if ($return_order->data == false) {
            $data['payload'] = false;
            $data['message'] = "Id não encontrado.";

            return $response->withJson($data, 200);
        }

        if ($return_order->data->status != "aprovado_financeiro") {
            $array_status[0] = 'cancelado';
            $array_status[1] = 'cancelado';
            $array_status[2] = 'cancelado';
            $array_status[3] = 'nao_cancelado';
            $array_status[4] = 'nao_cancelado';
            $array_status[5] = 'server_error';
            
            $random_int = random_int(0,5);

            if ($random_int < 5 ) {
                $return_update = $this->orderModel->changeStatus((int)$return_order->data->id,$array_status[$random_int]);

                if ($return_update->status != false) {
                    $data['payload']['id'] = $return_order->data->id;
                    $data['payload']['status'] = $return_order->data->status;
                    $data['message'] = "Atualizado com Sucesso";
                    return $response->withJson($data, 200);
                } else {
                    $data['payload'] = false;
                    $data['message'] = "Erro do servidor";
                    return $response->withJson($data, 500);
                }
            } else 
            {
                $data['payload'] = false;
                $data['message'] = "Erro do servidor";
                return $response->withJson($data, 400);
            }
            
            
        } else {
            $array_status[0] = 'processando';
            $array_status[1] = 'processando';
            $array_status[2] = 'processando';
            $array_status[3] = 'caixa_fechado';
            $array_status[4] = 'caixa_fechado';
            $array_status[5] = 'server_error';
            
            $random_int = random_int(0,5);

            if ($random_int < 5 ) {
                $return_update = $this->orderModel->changeStatus((int)$return_order->data->id,$array_status[$random_int]);

                if ($return_update->status != false) {
                    $data['payload']['id'] = $return_order->data->id;
                    $data['payload']['status'] = $return_order->data->status;
                    $data['message'] = "Atualizado com Sucesso";
                    return $response->withJson($data, 200);
                } else {
                    $data['payload'] = false;
                    $data['message'] = "Erro do servidor";
                    return $response->withJson($data, 500);
                }
            } else 
            {
                $data['payload'] = false;
                $data['message'] = "Erro do servidor";
                return $response->withJson($data, 400);
            }
        }



    }


}
