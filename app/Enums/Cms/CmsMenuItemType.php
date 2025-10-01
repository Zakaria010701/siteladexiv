<?php

namespace App\Enums\Cms;

enum CmsMenuItemType: string
{
    case Dropdown = 'dropdown';
    case Link = 'link';
    case Page = 'page';
    case Icon = 'icon';
    case Header = 'header';
    case Button = 'button';
}
