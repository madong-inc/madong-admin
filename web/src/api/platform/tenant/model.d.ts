
export interface TenantRow {
    packages: any[];
    id?: string;
    db_id: string;
    code: string;
    name: string;
    type: string;
    contact_person: string;
    contact_phone: string | number;
    company_name: string;
    license_number: string;
    address: string;
    description: string;
    domain: string;
    enabled: number|string;
    is_default: number;
    gran_subscription?:any[];
}

