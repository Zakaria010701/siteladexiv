<?php

namespace App\Enums\Transactions;

enum FintsImportStage: string
{
    case ChooseAccount = 'choose_account';
    case Login = 'login';
    case Import = 'import';
}
