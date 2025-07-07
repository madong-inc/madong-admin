
export interface DbSettingRow {
    id: string;
    name: string;
    description: string;
    driver: string;
    host: string;
    port: string;
    database: string;
    username: string;
    password: string;
    prefix: string;
    variable: any;
    is_default: number;
    enabled: number;
    created_date: string;
}

