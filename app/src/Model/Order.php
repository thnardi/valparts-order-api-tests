<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

class Order
{
    public $id;
    public $cli_id;
    public $total;
    public $pagamento;
    public $produtos;
    public $endereco_entrega;
    public $status;

    public function __construct(array $data = [])
    {
        $this->id                   = $data['id'] ?? null;
        $this->cli_id               = $data['cli_id'] ?? null;
        $this->total                = $data['total'] ?? null;
        $this->pagamento            = $data['pagamento'] ?? null;
        $this->produtos             = $data['produtos'] ?? null;
        $this->endereco_entrega     = $data['endereco_entrega'] ?? null;
        $this->status               = $data['status'] ?? null;
        
    }
}
