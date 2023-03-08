<?php

namespace App\PlusCourtChemin\Modele\DataObject;

use JsonSerializable;

abstract class AbstractDataObject implements JsonSerializable
{

    public abstract function exporterEnFormatRequetePreparee(): array;

    public function toJson(){
        return json_encode($this,JSON_UNESCAPED_UNICODE);
    }

    public function jsonSerialize(): array
    {
        return [];
    }

}