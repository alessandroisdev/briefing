<?php

namespace App\Enums;

enum BriefingStatus: string
{
    case Criado = 'criado';
    case Editando = 'editando';
    case Executando = 'executando';
    case Cancelado = 'cancelado';
    case Finalizado = 'finalizado';
}
