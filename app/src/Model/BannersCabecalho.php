<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

class BannersCabecalho
{
  public $id;
  public $name;
  public $description;
  public $img_featured;
  public $img_mobile;

  public function __construct(array $data = [])
  {
    $this->id       = $data['id'] ?? null;
    $this->name     = $data['name'] ?? null;
    $this->description     = $data['description'] ?? null;
    $this->img_featured = $data['img_featured'] ?? null;
    $this->img_mobile = $data['img_mobile'] ?? null;
  }
}
