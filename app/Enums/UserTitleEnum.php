<?php

namespace App\Enums;

use ReflectionClass;

enum UserTitleEnum: int
{
    case ADMINISTRATOR = 1;
    case MODERATOR = 2;
    case USER = 3;
    case FINANCE_EXPERT = 4;
    case SALES_REPRESENTATIVE = 5;
    case ACCOUNTING_MANAGER = 6;
    case PROJECT_COORDINATOR = 7;
    case SOFTWARE_ENGINEER = 8;
    case HR_SPECIALIST = 9;
    case MARKETING_ASSISTANT = 10;
    case OPERATIONS_ASSISTANT = 11;
    case LOGISTICS_PLANNER = 12;
    case PUBLIC_RELATIONS_COORDINATOR = 13;
    case DATA_ANALYST = 14;
    case BUSINESS_DEVELOPMENT_MANAGER = 15;
    case GRAPHIC_DESIGNER = 16;
    case TECHNICAL_SUPPORT_SPECIALIST = 17;
    case RESEARCH_ANALYST = 18;
    case CONTENT_WRITER = 19;
    case TRAINING_COORDINATOR = 20;


    public static function getTitles(): array
    {
        return array_map(
            fn(UserTitleEnum $enum) => $enum->title(),
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

    public static function getKeys(): array
    {
        return array_keys(self::cases());
    }


    public function title()
    {
        return match ($this) {
            self::ADMINISTRATOR => 'Administrator',
            self::MODERATOR => 'Moderator',
            self::USER => 'User',
            self::FINANCE_EXPERT => 'Finance Expert',
            self::SALES_REPRESENTATIVE => 'Sales Representative',
            self::ACCOUNTING_MANAGER => 'Accounting Manager',
            self::PROJECT_COORDINATOR => 'Project Coordinator',
            self::SOFTWARE_ENGINEER => 'Software Engineer',
            self::HR_SPECIALIST => 'HR Specialist',
            self::MARKETING_ASSISTANT => 'Marketing Assistant',
            self::OPERATIONS_ASSISTANT => 'Operations Assistant',
            self::LOGISTICS_PLANNER => 'Logistics Planner',
            self::PUBLIC_RELATIONS_COORDINATOR => 'Public Relations Coordinator',
            self::DATA_ANALYST => 'Data Analyst',
            self::BUSINESS_DEVELOPMENT_MANAGER => 'Business Development Manager',
            self::GRAPHIC_DESIGNER => 'Graphic Designer',
            self::TECHNICAL_SUPPORT_SPECIALIST => 'Technical Support Specialist',
            self::RESEARCH_ANALYST => 'Research Analyst',
            self::CONTENT_WRITER => 'Content Writer',
            self::TRAINING_COORDINATOR => 'Training Coordinator',
        };
    }
}
